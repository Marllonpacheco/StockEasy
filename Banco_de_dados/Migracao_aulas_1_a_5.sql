-- StockEasy: ajustes mínimos para as aulas 1 a 5.
-- NÃO executar sem antes conferir os resultados das consultas abaixo e fazer
-- uma cópia de segurança. Esta migração não é aplicada pelo PHP.

USE stockeasy;

-- Antes de executar este arquivo, inspecione manualmente as tabelas existentes:
-- SHOW CREATE TABLE produtos;
-- SHOW CREATE TABLE itens_movimentacao; (somente se a tabela existir)

-- Se qualquer consulta retornar linhas, revise os dados antes de criar CHECKs.
SELECT id, quantidade, estoque_minimo
FROM produtos
WHERE quantidade < 0 OR estoque_minimo < 0;

SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = 'itens_movimentacao'
    ),
    'SELECT id, quantidade FROM itens_movimentacao WHERE quantidade <= 0',
    'SELECT ''Tabela itens_movimentacao ausente'''
) INTO @consulta;
PREPARE verificacao FROM @consulta;
EXECUTE verificacao;
DEALLOCATE PREPARE verificacao;

-- Remove os CHECKs restritivos descritos no SQL de criação do projeto.
SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'produtos'
          AND constraint_name = 'chk_produto_quantidade'
    ),
    'ALTER TABLE produtos DROP CHECK chk_produto_quantidade',
    'SELECT ''CHECK de quantidade de produtos ausente; nenhuma remoção necessária'''
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'produtos'
          AND constraint_name = 'chk_produto_minimo'
    ),
    'ALTER TABLE produtos DROP CHECK chk_produto_minimo',
    'SELECT ''CHECK de estoque mínimo ausente; nenhuma remoção necessária'''
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

-- Permite quantidades e estoques mínimos iguais ou maiores que zero.
SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'produtos'
          AND constraint_name = 'chk_produto_quantidade'
    ),
    'SELECT ''CHECK chk_produto_quantidade já existe''',
    'ALTER TABLE produtos ADD CONSTRAINT chk_produto_quantidade CHECK (quantidade >= 0)'
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'produtos'
          AND constraint_name = 'chk_produto_minimo'
    ),
    'SELECT ''CHECK chk_produto_minimo já existe''',
    'ALTER TABLE produtos ADD CONSTRAINT chk_produto_minimo CHECK (estoque_minimo >= 0)'
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

-- A coluna mantém somente o nome do arquivo, não o conteúdo da imagem.
SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'produtos'
          AND column_name = 'imagem'
    ),
    'SELECT ''Coluna produtos.imagem já existe''',
    'ALTER TABLE produtos ADD COLUMN imagem VARCHAR(255) NULL AFTER categoria_id'
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

-- itens_movimentacao pode não existir se a criação original parou no CHECK
-- inválido. Se existir, cada item deve movimentar pelo menos uma unidade.
SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'itens_movimentacao'
          AND constraint_name = 'chk_item_quantidade'
    ),
    'ALTER TABLE itens_movimentacao DROP CHECK chk_item_quantidade',
    'SELECT ''CHECK de itens_movimentacao ausente; nenhuma remoção necessária'''
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

SELECT IF(
    EXISTS (
        SELECT 1 FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = 'itens_movimentacao'
    ) AND NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints
        WHERE constraint_schema = DATABASE()
          AND table_name = 'itens_movimentacao'
          AND constraint_name = 'chk_item_quantidade'
    ),
    'ALTER TABLE itens_movimentacao ADD CONSTRAINT chk_item_quantidade CHECK (quantidade > 0)',
    'SELECT ''Tabela ausente ou CHECK chk_item_quantidade já existe'''
) INTO @ddl;
PREPARE migracao FROM @ddl;
EXECUTE migracao;
DEALLOCATE PREPARE migracao;

-- Após a execução, inspecione manualmente a estrutura novamente, caso a tabela
-- itens_movimentacao exista.
-- SHOW CREATE TABLE produtos;
-- SHOW CREATE TABLE itens_movimentacao;
