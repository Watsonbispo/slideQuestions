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

        $driver = 'mysql';
        $host = '127.0.0.1';
        $port = '3306';
        $name = 'projectslides';
        $user = 'DaviSena';
        $pass = '197508';

        if ($driver !== 'mysql') {
            throw new PDOException('Only mysql driver is supported in this configuration.');
        }

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$connection = new PDO($dsn, $user, $pass, $options);
        return self::$connection;
    }
}


