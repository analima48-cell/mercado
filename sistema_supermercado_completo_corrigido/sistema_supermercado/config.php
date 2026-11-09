<?php
/**
 * Conexão e instalação automática do banco no XAMPP.
 * Na primeira abertura, cria o banco, as tabelas e dados demonstrativos.
 * Para instalações locais padrão do XAMPP: usuário root e senha vazia.
 */
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'supermercado');
define('DB_USER', 'root');
define('DB_PASS', '');

date_default_timezone_set('America/Sao_Paulo');

function exibirErroBanco(PDOException $e): void
{
    http_response_code(500);
    echo '<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><title>Erro de conexão</title></head>';
    echo '<body style="font:16px Arial,sans-serif;max-width:850px;margin:50px auto;padding:20px">';
    echo '<h1>Não foi possível conectar ao banco de dados</h1>';
    echo '<p>Confira se o MySQL está iniciado no XAMPP. A configuração atual usa host <strong>' . htmlspecialchars(DB_HOST) . '</strong>, porta <strong>' . htmlspecialchars(DB_PORT) . '</strong> e usuário <strong>' . htmlspecialchars(DB_USER) . '</strong>.</p>';
    echo '<h3>Detalhe técnico</h3><pre style="white-space:pre-wrap;background:#f3f4f6;padding:14px;border-radius:8px">' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>No XAMPP padrão, inicie Apache e MySQL no painel de controle. Se o MySQL estiver em outra porta, altere DB_PORT neste arquivo.</p>';
    echo '</body></html>';
    exit;
}

try {
    // Primeiro conecta ao servidor, sem selecionar um banco que talvez ainda não exista.
    $dsnServidor = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
    $servidor = new PDO($dsnServidor, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);

    // Cria o banco automaticamente caso seja a primeira execução.
    $servidor->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

    // Agora conecta ao banco já garantido.
    $dsnBanco = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsnBanco, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);

    // Instalação automática e idempotente: não apaga dados existentes.
    $pdo->exec("CREATE TABLE IF NOT EXISTS categorias (
        id INT NOT NULL AUTO_INCREMENT,
        nome VARCHAR(80) NOT NULL,
        descricao VARCHAR(255) NULL,
        criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_categorias_nome (nome)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id INT NOT NULL AUTO_INCREMENT,
        codigo VARCHAR(30) NOT NULL,
        nome VARCHAR(120) NOT NULL,
        categoria_id INT NOT NULL,
        preco DECIMAL(10,2) NOT NULL,
        estoque INT NOT NULL DEFAULT 0,
        estoque_minimo INT NOT NULL DEFAULT 5,
        validade DATE NULL,
        descricao TEXT NULL,
        criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uk_produtos_codigo (codigo),
        KEY idx_produtos_categoria (categoria_id),
        KEY idx_produtos_nome (nome),
        CONSTRAINT fk_produto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
            ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS movimentacoes (
        id INT NOT NULL AUTO_INCREMENT,
        produto_id INT NOT NULL,
        tipo ENUM('entrada','saida') NOT NULL,
        quantidade INT NOT NULL,
        observacao VARCHAR(255) NULL,
        data_movimento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_movimentacoes_produto (produto_id),
        CONSTRAINT fk_mov_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
            ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Categorias iniciais; INSERT IGNORE evita duplicações nas próximas visitas.
    $pdo->exec("INSERT IGNORE INTO categorias (nome, descricao) VALUES
        ('Mercearia','Alimentos não perecíveis'),
        ('Bebidas','Água, sucos e refrigerantes'),
        ('Higiene','Produtos de higiene pessoal'),
        ('Limpeza','Produtos para limpeza doméstica'),
        ('Frios e laticínios','Leites, queijos e derivados')");

    // Produtos demonstrativos vinculados pelo nome da categoria, sem depender de IDs fixos.
    $sqlProdutos = "INSERT IGNORE INTO produtos
        (codigo, nome, categoria_id, preco, estoque, estoque_minimo, validade, descricao)
        SELECT d.codigo, d.nome, c.id, d.preco, d.estoque, d.estoque_minimo, d.validade, d.descricao
        FROM (
            SELECT 'MER001' codigo, 'Arroz tipo 1 - 5 kg' nome, 'Mercearia' categoria, 28.90 preco, 32 estoque, 8 estoque_minimo, '2027-05-20' validade, 'Pacote de arroz' descricao
            UNION ALL SELECT 'MER002', 'Feijão carioca - 1 kg', 'Mercearia', 8.49, 4, 10, '2027-02-10', 'Pacote de feijão'
            UNION ALL SELECT 'MER003', 'Macarrão espaguete - 500 g', 'Mercearia', 5.79, 25, 6, '2027-08-15', 'Massa seca'
            UNION ALL SELECT 'BEB001', 'Água mineral - 1,5 L', 'Bebidas', 3.50, 48, 12, '2027-01-30', 'Garrafa sem gás'
            UNION ALL SELECT 'BEB002', 'Suco de uva - 1 L', 'Bebidas', 9.90, 3, 8, '2027-10-30', 'Suco integral'
            UNION ALL SELECT 'HIG001', 'Sabonete', 'Higiene', 2.99, 40, 10, '2028-01-01', 'Sabonete em barra'
            UNION ALL SELECT 'HIG002', 'Creme dental', 'Higiene', 6.50, 18, 5, '2027-12-01', 'Creme dental'
            UNION ALL SELECT 'LIM001', 'Detergente - 500 ml', 'Limpeza', 2.79, 2, 10, '2027-06-10', 'Detergente neutro'
            UNION ALL SELECT 'LIM002', 'Desinfetante - 2 L', 'Limpeza', 8.90, 16, 5, '2027-04-05', 'Desinfetante perfumado'
            UNION ALL SELECT 'FRI001', 'Leite integral - 1 L', 'Frios e laticínios', 5.29, 20, 8, '2027-11-05', 'Leite longa vida'
        ) d INNER JOIN categorias c ON c.nome = d.categoria";
    $pdo->exec($sqlProdutos);

} catch (PDOException $e) {
    exibirErroBanco($e);
}
