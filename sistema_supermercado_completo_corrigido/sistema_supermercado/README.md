# Mercado Fácil — Sistema de supermercado em PHP e MySQL

Projeto acadêmico desenvolvido para atender à atividade do SENAI. O sistema permite cadastrar e administrar categorias e produtos, pesquisar e filtrar o catálogo, consultar detalhes e acompanhar estoque e validade.

## 1. Tecnologias
- PHP 8 ou superior
- MySQL/MariaDB
- PDO para conexão e consultas preparadas
- HTML5 e CSS3 responsivo
- XAMPP e phpMyAdmin para ambiente local

## 2. Como instalar no XAMPP
1. Instale e abra o XAMPP.
2. Inicie os módulos **Apache** e **MySQL**.
3. Copie a pasta `sistema_supermercado` para `C:\xampp\htdocs\` (Windows) ou para a pasta `htdocs` da sua instalação.
4. Abra `http://localhost/phpmyadmin`.
5. Vá em **Importar**, selecione `sistema_supermercado/sql/banco.sql` e clique em **Importar/Executar**. O script cria o banco `supermercado`, as tabelas e dados iniciais.
6. Confira usuário e senha do MySQL no arquivo `config.php`. No XAMPP padrão, usuário `root` e senha vazia.
7. Acesse `http://localhost/sistema_supermercado/`.

## 3. Funcionalidades entregues
- Página inicial com indicadores: total de produtos, categorias, estoque baixo e produtos vencidos.
- Listagem de produtos com código, nome, categoria, preço, quantidade, validade e ações.
- Busca por nome ou código e filtro por categoria.
- Cadastro e edição de produtos.
- Detalhes completos do produto e retorno à listagem.
- Exclusão de produto com tela de confirmação.
- Cadastro, listagem e edição de categorias.
- Bloqueio de exclusão de categoria que tenha produtos vinculados.
- Alertas de estoque igual ou abaixo do mínimo, produtos vencidos e vencimento nos próximos 30 dias.
- Mensagens de sucesso e erro.
- Preços com duas casas decimais e apresentação em reais.
- Proteção contra SQL injection com PDO e consultas preparadas; escape de conteúdo HTML para evitar inserção de HTML/script.

## 4. Modelo de dados
- **categorias**: id, nome único, descrição e data de criação.
- **produtos**: id, código único, nome, categoria, preço decimal, estoque inteiro, estoque mínimo, validade e descrição.
- **movimentacoes**: estrutura para registrar entradas e saídas futuras, vinculada ao produto.

Relacionamento: uma categoria pode possuir vários produtos (1:N). A chave estrangeira impede excluir uma categoria que ainda tenha produtos vinculados.

## 5. Regras e validações
- Código, nome, categoria, preço, estoque e estoque mínimo são obrigatórios.
- Preço deve ser numérico e maior que zero.
- Estoque e estoque mínimo devem ser números inteiros iguais ou maiores que zero; não são aceitas quantidades fracionadas.
- Código de produto e nome de categoria não podem se repetir.
- Categoria precisa existir no banco.
- Validade, quando preenchida, precisa ser uma data válida.
- Categoria com produtos vinculados não pode ser excluída.
- Registro inexistente ou ID inválido exibe mensagem e oferece retorno.
- Atualizações e exclusões afetam somente o registro selecionado.

## 6. Roteiro de testes para a demonstração
1. Cadastrar uma categoria válida e verificar se aparece na listagem.
2. Tentar cadastrar novamente a mesma categoria e conferir a mensagem de duplicidade.
3. Cadastrar produto válido e verificar os dados na listagem e na tela de detalhes.
4. Tentar salvar produto sem código ou nome e verificar a validação.
5. Informar preço zero ou negativo e conferir o bloqueio.
6. Informar estoque decimal (ex.: 2,5) e conferir o bloqueio.
7. Tentar repetir o código de um produto já cadastrado.
8. Editar nome, preço e categoria de um produto; conferir se somente ele foi alterado.
9. Pesquisar por parte do nome e por código.
10. Filtrar por categoria e depois limpar o filtro.
11. Tentar excluir categoria que tenha produtos; conferir que a exclusão é impedida.
12. Abrir exclusão de categoria vazia, cancelar e depois confirmar.
13. Abrir `produto_detalhe.php?id=999999` e verificar a mensagem de não encontrado.
14. Atualizar a página inicial e conferir os indicadores.
15. Testar texto com caracteres como `<`, `>` e aspas na descrição; o texto deve aparecer sem executar HTML.
16. Conferir alertas de estoque e validade. O banco inicial inclui exemplos de estoque baixo e datas vencidas para facilitar a demonstração.

## 7. Sugestão de divisão da apresentação
- Integrante 1: apresentar o problema, objetivo e tecnologias.
- Integrante 2: explicar o banco de dados e o relacionamento categoria-produto.
- Integrante 3: demonstrar cadastro, edição, busca e filtro de produtos.
- Integrante 4: demonstrar validações, exclusões e alertas de estoque/validade.

## 8. Estrutura dos arquivos
- `index.php`: painel inicial.
- `produtos.php`: listagem, busca e filtro.
- `produto_form.php`: cadastro e edição.
- `produto_detalhe.php`: consulta de detalhes.
- `produto_excluir.php`: confirmação e exclusão de produto.
- `categorias.php`: listagem de categorias.
- `categoria_form.php`: cadastro e edição de categoria.
- `categoria_excluir.php` e `categoria_excluir_confirmar.php`: exclusão protegida.
- `estoque.php`: alertas de estoque e validade.
- `config.php`: conexão com o banco.
- `sql/banco.sql`: criação do banco e dados de demonstração.

**Observação:** o sistema é uma aplicação didática de administração de estoque, não um sistema de frente de caixa. As movimentações possuem tabela preparada no banco, mas a interface de lançamento de entradas/saídas não faz parte do escopo essencial descrito nas folhas.

## Solução de problemas de conexão no XAMPP

- Inicie Apache e MySQL no painel do XAMPP.
- Importe `sql/banco.sql` pelo phpMyAdmin.
- A configuração padrão em `config.php` usa host `127.0.0.1`, porta `3306`, usuário `root` e senha vazia.
- Se o MySQL estiver em outra porta, confira a porta indicada no painel/configuração do XAMPP e altere `DB_PORT` em `config.php`.
- A tela de erro de conexão mostra o detalhe técnico para facilitar o diagnóstico no ambiente local.
