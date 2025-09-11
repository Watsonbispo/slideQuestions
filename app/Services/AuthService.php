<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\Database;
use PDO;

final class AuthService
{
    private const SESSION_KEY = 'admin_logged_in';
    private const SESSION_USER_ID = 'admin_user_id';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $username, string $password): bool
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE username = :username LIMIT 1');
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION[self::SESSION_KEY] = true;
                $_SESSION[self::SESSION_USER_ID] = $admin['id'];
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            error_log('Auth error: ' . $e->getMessage());
            return false;
        }
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        unset($_SESSION[self::SESSION_USER_ID]);
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] === true;
    }

    public function getCurrentUserId(): ?int
    {
        return $_SESSION[self::SESSION_USER_ID] ?? null;
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login', true, 302);
            exit;
        }
    }
}
