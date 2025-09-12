<?php
declare(strict_types=1);

namespace App\Support;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        // Configurações para Railway (variáveis de ambiente)
        $host = $_ENV['MYSQLHOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['MYSQLPORT'] ?? $_ENV['DB_PORT'] ?? '3306';
        $name = $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME'] ?? 'projectslides';
        $user = $_ENV['MYSQLUSER'] ?? $_ENV['DB_USER'] ?? 'DaviSena';
        $pass = $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS'] ?? '197508';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$connection = new PDO($dsn, $user, $pass, $options);
            return self::$connection;
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new PDOException('Falha na conexão com o banco de dados: ' . $e->getMessage());
        }
    }
}


