-- Script RÁPIDO para adicionar configuração de redirecionamento
-- Execute apenas este comando no DBeaver:

INSERT INTO settings (`key`, `value`) 
VALUES ('redirect_url', 'https://example.com/checkout/ABC123')
ON DUPLICATE KEY UPDATE `value` = 'https://example.com/checkout/ABC123';

-- Para verificar se funcionou:
SELECT * FROM settings WHERE `key` = 'redirect_url';
