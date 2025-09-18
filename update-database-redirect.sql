-- Script para adicionar configuração de redirecionamento no banco de dados
-- Execute este script no DBeaver ou qualquer cliente MySQL

-- 1. Verificar se a tabela settings existe
SELECT 'Verificando tabela settings...' as status;
SHOW TABLES LIKE 'settings';

-- 2. Verificar configurações atuais
SELECT 'Configurações atuais:' as status;
SELECT * FROM settings;

-- 3. Inserir ou atualizar a configuração de redirecionamento
-- Se a configuração já existir, será atualizada
-- Se não existir, será criada
INSERT INTO settings (`key`, `value`) 
VALUES ('redirect_url', 'https://example.com/checkout/ABC123')
ON DUPLICATE KEY UPDATE `value` = 'https://example.com/checkout/ABC123';

-- 4. Verificar se a configuração foi inserida/atualizada
SELECT 'Configuração de redirecionamento:' as status;
SELECT * FROM settings WHERE `key` = 'redirect_url';

-- 5. Verificar todas as configurações
SELECT 'Todas as configurações:' as status;
SELECT * FROM settings;

-- 6. Teste de validação
SELECT 'Teste de validação - URL configurada:' as status;
SELECT 
    CASE 
        WHEN `value` LIKE 'http%' THEN '✅ URL válida'
        ELSE '❌ URL inválida'
    END as validacao,
    `value` as url_configurada
FROM settings 
WHERE `key` = 'redirect_url';
