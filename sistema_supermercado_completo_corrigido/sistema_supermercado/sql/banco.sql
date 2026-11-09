-- Banco de dados do Sistema de Supermercado
-- Compatível com MySQL e MariaDB distribuídos com o XAMPP.
-- Importe este arquivo pelo phpMyAdmin (aba Importar).

CREATE DATABASE IF NOT EXISTS supermercado
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE supermercado;

CREATE TABLE IF NOT EXISTS categorias (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(80) NOT NULL,
  descricao VARCHAR(255) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_categorias_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produtos (
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
  CONSTRAINT fk_produto_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS movimentacoes (
  id INT NOT NULL AUTO_INCREMENT,
  produto_id INT NOT NULL,
  tipo ENUM('entrada','saida') NOT NULL,
  quantidade INT NOT NULL,
  observacao VARCHAR(255) NULL,
  data_movimento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_movimentacoes_produto (produto_id),
  CONSTRAINT fk_mov_produto
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorias de exemplo. INSERT IGNORE evita duplicar nomes ao importar novamente.
INSERT IGNORE INTO categorias (nome, descricao) VALUES
('Mercearia','Alimentos não perecíveis'),
('Bebidas','Água, sucos e refrigerantes'),
('Higiene','Produtos de higiene pessoal'),
('Limpeza','Produtos para limpeza doméstica'),
('Frios e laticínios','Leites, queijos e derivados');

-- Produtos de exemplo associados pelo nome da categoria, sem depender de IDs fixos.
INSERT IGNORE INTO produtos
(codigo, nome, categoria_id, preco, estoque, estoque_minimo, validade, descricao)
SELECT dados.codigo, dados.nome, categorias.id, dados.preco, dados.estoque,
       dados.estoque_minimo, dados.validade, dados.descricao
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
) AS dados
INNER JOIN categorias ON categorias.nome = dados.categoria;
