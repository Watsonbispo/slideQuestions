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
}


