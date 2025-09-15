<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

echo "<!DOCTYPE html><html><head><title>Railway Status</title><style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";
echo "<h1>🚂 Railway Status - ProjectSlides</h1>";

// 1. Verificar todas as variáveis de ambiente
echo "<div class='section'>";
echo "<h2>📋 Variáveis de Ambiente</h2>";

$all_env_vars = getenv();
$mysql_vars = [];
$railway_vars = [];

foreach ($all_env_vars as $key => $value) {
    if (strpos($key, 'MYSQL') === 0) {
        $mysql_vars[$key] = $value;
    }
    if (strpos($key, 'RAILWAY') === 0 || strpos($key, 'DATABASE') === 0) {
        $railway_vars[$key] = $value;
    }
}

echo "<h3>🔍 Variáveis MySQL:</h3>";
if (empty($mysql_vars)) {
    echo "<span class='error'>❌ Nenhuma variável MYSQL* encontrada!</span><br>";
} else {
    foreach ($mysql_vars as $key => $value) {
        echo "<span class='info'><strong>{$key}:</strong> " . ($value ?: 'VAZIA') . "</span><br>";
    }
}

echo "<h3>🚂 Variáveis Railway:</h3>";
if (empty($railway_vars)) {
    echo "<span class='warning'>⚠️ Nenhuma variável Railway encontrada</span><br>";
} else {
    foreach ($railway_vars as $key => $value) {
        echo "<span class='info'><strong>{$key}:</strong> " . ($value ?: 'VAZIA') . "</span><br>";
    }
}
echo "</div>";

// 2. Testar QuestionService
echo "<div class='section'>";
echo "<h2>🔧 Teste do QuestionService</h2>";

try {
    $questionService = new App\Services\QuestionService();
    $questions = $questionService->getQuestions();
    $allQuestions = $questionService->getAllQuestions();
    
    echo "<span class='info'>📝 Perguntas ativas: " . count($questions) . "</span><br>";
    echo "<span class='info'>📝 Total de perguntas: " . count($allQuestions) . "</span><br>";
    
    if (count($questions) <= 2) {
        echo "<span class='error'>❌ PROBLEMA: Apenas " . count($questions) . " perguntas (usando fallback)</span><br>";
        echo "<span class='info'>🔍 Isso indica que a conexão com o banco está falhando</span><br>";
        
        // Mostrar as perguntas de fallback
        echo "<span class='info'>📋 Perguntas de fallback:</span><br>";
        foreach ($questions as $i => $q) {
            echo "&nbsp;&nbsp;" . ($i + 1) . ". " . htmlspecialchars(substr($q['title'], 0, 60)) . "...<br>";
        }
    } else {
        echo "<span class='success'>✅ " . count($questions) . " perguntas carregadas do banco</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro no QuestionService: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 3. Testar conexão direta
echo "<div class='section'>";
echo "<h2>🔌 Teste de Conexão Direta</h2>";

// Tentar diferentes configurações
$configs = [
    'MYSQL*' => [
        'host' => $_ENV['MYSQLHOST'] ?? null,
        'port' => $_ENV['MYSQLPORT'] ?? '3306',
        'dbname' => $_ENV['MYSQLDATABASE'] ?? null,
        'user' => $_ENV['MYSQLUSER'] ?? null,
        'pass' => $_ENV['MYSQLPASSWORD'] ?? '',
    ],
    'DATABASE_URL' => [
        'url' => $_ENV['DATABASE_URL'] ?? null,
    ],
    'DB_*' => [
        'host' => $_ENV['DB_HOST'] ?? null,
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'dbname' => $_ENV['DB_NAME'] ?? null,
        'user' => $_ENV['DB_USER'] ?? null,
        'pass' => $_ENV['DB_PASS'] ?? '',
    ]
];

$connection_working = false;
foreach ($configs as $name => $config) {
    echo "<h3>{$name}:</h3>";
    
    if (isset($config['url']) && $config['url']) {
        try {
            $pdo = new PDO($config['url']);
            echo "<span class='success'>✅ Conexão via DATABASE_URL: SUCESSO</span><br>";
            
            // Testar tabelas
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<span class='info'>📋 Tabelas: " . implode(', ', $tables) . "</span><br>";
            
            // Contar perguntas
            if (in_array('questions', $tables)) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
                $result = $stmt->fetch();
                echo "<span class='info'>📝 Perguntas no banco: " . $result['count'] . "</span><br>";
                
                if ($result['count'] > 0) {
                    $connection_working = true;
                }
            }
            
        } catch (Exception $e) {
            echo "<span class='error'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</span><br>";
        }
    } else {
        if ($config['host'] && $config['dbname'] && $config['user']) {
            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
                $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                echo "<span class='success'>✅ Conexão: SUCESSO</span><br>";
                
                // Testar tabelas
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "<span class='info'>📋 Tabelas: " . implode(', ', $tables) . "</span><br>";
                
                // Contar perguntas
                if (in_array('questions', $tables)) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
                    $result = $stmt->fetch();
                    echo "<span class='info'>📝 Perguntas no banco: " . $result['count'] . "</span><br>";
                    
                    if ($result['count'] > 0) {
                        $connection_working = true;
                    }
                }
                
            } catch (Exception $e) {
                echo "<span class='error'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</span><br>";
            }
        } else {
            echo "<span class='warning'>⚠️ Configuração incompleta</span><br>";
        }
    }
    echo "<hr>";
}
echo "</div>";

// 4. Instruções baseadas no status
echo "<div class='section'>";
echo "<h2>🛠️ Instruções</h2>";

if (empty($mysql_vars)) {
    echo "<span class='error'>❌ PROBLEMA: MySQL não configurado no Railway</span><br>";
    echo "<span class='info'>📋 SOLUÇÃO:</span><br>";
    echo "1. Acesse: https://railway.app/dashboard<br>";
    echo "2. Selecione seu projeto<br>";
    echo "3. Clique em '+ New' → 'Database' → 'MySQL'<br>";
    echo "4. Aguarde o MySQL ser criado<br>";
    echo "5. Execute: <a href='/setup-database.php' target='_blank'>/setup-database.php</a><br>";
} elseif (!$connection_working) {
    echo "<span class='warning'>⚠️ MySQL configurado mas banco vazio</span><br>";
    echo "<span class='info'>📋 SOLUÇÃO:</span><br>";
    echo "1. Execute: <a href='/setup-database.php' target='_blank'>/setup-database.php</a><br>";
    echo "2. Isso criará todas as tabelas e dados<br>";
} else {
    echo "<span class='success'>✅ Tudo funcionando!</span><br>";
    echo "<span class='info'>📋 Próximos passos:</span><br>";
    echo "1. Teste o admin: <a href='/test-admin.php' target='_blank'>/test-admin.php</a><br>";
    echo "2. Acesse a aplicação: <a href='/' target='_blank'>Página principal</a><br>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>🔗 Links Úteis</h2>";
echo "<a href='/railway-debug.php' target='_blank'>🔧 Debug Completo</a><br>";
echo "<a href='/setup-database.php' target='_blank'>⚙️ Configurar Banco</a><br>";
echo "<a href='/test-admin.php' target='_blank'>👤 Testar Admin</a><br>";
echo "<a href='/' target='_blank'>🏠 Aplicação Principal</a><br>";
echo "</div>";

echo "</body></html>";
?>
