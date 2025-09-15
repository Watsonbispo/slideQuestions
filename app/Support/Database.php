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

        // Tentar diferentes configurações do Railway
        $configs = [
            // Configuração 1: Variáveis MYSQL* (Railway padrão)
            [
                'host' => $_ENV['MYSQLHOST'] ?? null,
                'port' => $_ENV['MYSQLPORT'] ?? '3306',
                'name' => $_ENV['MYSQLDATABASE'] ?? null,
                'user' => $_ENV['MYSQLUSER'] ?? null,
                'pass' => $_ENV['MYSQLPASSWORD'] ?? '',
                'type' => 'MYSQL*'
            ],
            // Configuração 2: DATABASE_URL (Railway alternativa)
            [
                'url' => $_ENV['DATABASE_URL'] ?? null,
                'type' => 'DATABASE_URL'
            ],
            // Configuração 3: Variáveis DB_* (fallback)
            [
                'host' => $_ENV['DB_HOST'] ?? null,
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'name' => $_ENV['DB_NAME'] ?? null,
                'user' => $_ENV['DB_USER'] ?? null,
                'pass' => $_ENV['DB_PASS'] ?? '',
                'type' => 'DB_*'
            ],
            // Configuração 4: Local (desenvolvimento)
            [
                'host' => '127.0.0.1',
                'port' => '3306',
                'name' => 'projectslides',
                'user' => 'DaviSena',
                'pass' => '197508',
                'type' => 'LOCAL'
            ]
        ];

        $lastError = null;
        
        foreach ($configs as $config) {
            try {
                if (isset($config['url']) && $config['url']) {
                    // Tentar conexão via DATABASE_URL
                    self::$connection = new PDO($config['url'], null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    
                    // Debug para desenvolvimento
                    if (getenv('APP_ENV') === 'development' || !getenv('RAILWAY_ENVIRONMENT')) {
                        error_log("Database connected via DATABASE_URL ({$config['type']})");
                    }
                    
                    return self::$connection;
                    
                } elseif (isset($config['host']) && isset($config['name']) && isset($config['user']) && 
                          $config['host'] && $config['name'] && $config['user']) {
                    // Tentar conexão normal
                    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', 
                        $config['host'], $config['port'], $config['name']);
                    
                    self::$connection = new PDO($dsn, $config['user'], $config['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    
                    // Debug para desenvolvimento
                    if (getenv('APP_ENV') === 'development' || !getenv('RAILWAY_ENVIRONMENT')) {
                        error_log("Database connected via {$config['type']} - Host: {$config['host']}, Port: {$config['port']}, DB: {$config['name']}, User: {$config['user']}");
                    }
                    
                    return self::$connection;
                }
            } catch (PDOException $e) {
                $lastError = $e;
                error_log("Database connection failed ({$config['type']}): " . $e->getMessage());
                continue;
            }
        }

        // Se chegou aqui, todas as configurações falharam
        error_log('All database connection attempts failed');
        throw new PDOException('Falha na conexão com o banco de dados. Verifique as configurações do Railway. Último erro: ' . ($lastError ? $lastError->getMessage() : 'Desconhecido'));
    }
}


