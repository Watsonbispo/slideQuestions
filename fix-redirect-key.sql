-- Script para corrigir a chave de redirecionamento no banco
-- Execute no DBeaver:

-- 1. Atualizar a chave existente
UPDATE settings 
SET `key` = 'redirect_url' 
WHERE `key` = 'redireccionamento_url';

-- 2. Verificar se funcionou
SELECT * FROM settings WHERE `key` = 'redirect_url';
