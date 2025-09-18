<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\Database;

final class SettingsService
{
    public function getBackgroundUrl(): string
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT `value` FROM settings WHERE `key` = :k LIMIT 1');
            $stmt->execute([':k' => 'background_image_path']);
            $value = $stmt->fetchColumn();
            if (is_string($value) && $value !== '') {
                return $value;
            }
        } catch (\Throwable $e) {
            // Fallback silently if DB is not ready
        }

        // Fallback for current default image path
        return '/assets/imgs/590f67723c50604dd9ab22d6dd30c9ba.jpg';
    }

    public function getRedirectUrl(): string
    {
        try {
            $pdo = Database::getConnection();
            
            // Tentar primeiro com a chave correta
            $stmt = $pdo->prepare('SELECT `value` FROM settings WHERE `key` = :k LIMIT 1');
            $stmt->execute([':k' => 'redirect_url']);
            $value = $stmt->fetchColumn();
            if (is_string($value) && $value !== '') {
                return $value;
            }
            
            // Se não encontrar, tentar com a chave antiga
            $stmt = $pdo->prepare('SELECT `value` FROM settings WHERE `key` = :k LIMIT 1');
            $stmt->execute([':k' => 'redireccionamento_url']);
            $value = $stmt->fetchColumn();
            if (is_string($value) && $value !== '') {
                return $value;
            }
            
        } catch (\Throwable $e) {
            // Fallback silently if DB is not ready
        }

        // Fallback for default redirect URL
        return 'https://example.com/checkout/ABC123';
    }

    public function updateRedirectUrl(string $url): bool
    {
        try {
            $pdo = Database::getConnection();
            
            // Primeiro, tentar com a chave correta
            $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = :v');
            $result = $stmt->execute([
                ':k' => 'redirect_url',
                ':v' => $url
            ]);
            
            if ($result) {
                return true;
            }
            
            // Se falhar, tentar atualizar a chave existente
            $stmt = $pdo->prepare('UPDATE settings SET `key` = :new_key, `value` = :v WHERE `key` = :old_key');
            $result = $stmt->execute([
                ':new_key' => 'redirect_url',
                ':v' => $url,
                ':old_key' => 'redireccionamento_url'
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                return true;
            }
            
            // Se ainda falhar, inserir nova entrada
            $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (:k, :v)');
            return $stmt->execute([
                ':k' => 'redirect_url',
                ':v' => $url
            ]);
            
        } catch (\Throwable $e) {
            error_log('Error updating redirect URL: ' . $e->getMessage());
            return false;
        }
    }
}


