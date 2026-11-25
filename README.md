# StockEasy

Projeto acadêmico simples para cadastro e consulta de produtos em estoque, baseado nas aulas 1 a 5 de PHP, PDO, Bootstrap, sessões, pesquisa e upload.

## Abrir pelo XAMPP

1. Inicie Apache e MySQL pelo painel do XAMPP.
2. Acesse `http://localhost/phpmyadmin` e confira se o banco `stockeasy` existe.
3. Se for uma instalação nova, revise e execute `Banco_de_dados/Criacao_banco.txt` no phpMyAdmin. Esse script cria as tabelas planejadas para o projeto.
4. Para um banco já existente, confira primeiro `SHOW CREATE TABLE produtos` e `SHOW CREATE TABLE itens_movimentacao`. Revise `Banco_de_dados/Migracao_aulas_1_a_5.sql`; ela não é executada automaticamente e pode exigir ajustes conforme o schema e os dados locais.
5. Confira host, banco, usuário e senha locais em `conexao.php`. O arquivo contém os valores padrão do XAMPP, sem credenciais importadas do repositório de referência.
6. Cadastre ao menos uma categoria na tabela `categorias` pelo phpMyAdmin, caso ainda não exista. A chave estrangeira exige uma categoria antes do cadastro de produtos.
7. Abra `http://localhost/StockEasy/cadastro.php` para criar a conta e depois `http://localhost/StockEasy/login.php` para entrar.

## Aulas implementadas

| Aula | Conteúdo | Arquivos |
|---|---|---|
| 1 | CRUD de produtos com PDO e FK para categorias | `conexao.php`, `index.php`, `novo.php`, `salvar.php`, `editar.php`, `atualizar.php`, `excluir.php` |
| 2 | Bootstrap 5, navbar, tabela, formulários, alert e modal | `index.php`, `novo.php`, `editar.php`, `navbar.php` |
| 3 | Login, sessões, hash de senha, proteção e logout | `login.php`, `cadastro.php`, `verifica_login.php`, `logout.php` |
| 4 | Busca por nome/código, filtro por categoria e paginação com PDO | `index.php` |
| 5 | Upload opcional de imagem; valida MIME/conteúdo, extensão, tamanho e nome; pasta protegida | `upload.php`, `salvar.php`, `editar.php`, `atualizar.php`, `excluir.php`, `uploads/produtos/.htaccess` |

O banco guarda apenas o nome gerado da imagem na coluna `produtos.imagem`; os arquivos ficam em `uploads/produtos/`. Imagens antigas em `uploads/alunos/` foram preservadas, mas não fazem parte do fluxo atual.

## Como testar cada aula

- **Aula 1:** depois de cadastrar uma categoria, crie um produto, edite-o e exclua um produto de teste que não esteja ligado a uma movimentação.
- **Aula 2:** abra a listagem e os formulários em uma janela de navegador; confira a tabela responsiva, navbar, alertas e modal de exclusão.
- **Aula 3:** crie uma conta em `cadastro.php`, entre, saia e tente abrir `index.php` sem sessão. O sistema deve voltar ao login.
- **Aula 4:** cadastre mais de dez produtos e teste busca parcial por nome/código, filtro por categoria, combinação dos filtros e links de página.
- **Aula 5:** cadastre uma imagem JPG, PNG ou WebP de até 2 MB; tente também uma extensão que não corresponda ao conteúdo. Edite sem escolher arquivo para manter a imagem, depois substitua-a. Exclua somente um produto de teste e confirme a remoção do arquivo correspondente.

## Pendências conhecidas

- O MySQL local estava parado durante a adaptação. Tabelas/colunas reais, migração e operações SQL precisam ser conferidas com o serviço iniciado; nenhum dado ou schema foi alterado nesta sessão.
- A página do professor e PDFs das aulas não estavam acessíveis/localmente disponíveis. O alinhamento foi feito com o conteúdo existente em `BACKEND/main` e com o escopo de aulas fornecido.
- As operações de navegação e upload ainda precisam de teste manual no Apache/MySQL local. A sintaxe PHP foi conferida com `php -l`.
