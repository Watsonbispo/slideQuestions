<?php
echo "<h1>Debug Banco de Dados - Railway</h1>";

// Mostrar todas as variáveis de ambiente relacionadas ao banco
echo "<h2>Variáveis de Ambiente do Railway:</h2>";
$env_vars = [
    'MYSQLHOST',
    'MYSQLPORT', 
    'MYSQLDATABASE',
    'MYSQLUSER',
    'MYSQLPASSWORD',
    'RAILWAY_ENVIRONMENT',
    'DATABASE_URL'
];

foreach ($env_vars as $var) {
    $value = getenv($var);
    echo "<strong>{$var}:</strong> " . ($value ? $value : 'NÃO DEFINIDA') . "<br>";
}

echo "<h2>Teste de Conexão:</h2>";

// Tentar diferentes configurações
$configs = [
    'Railway MySQL' => [
        'host' => getenv('MYSQLHOST'),
        'port' => getenv('MYSQLPORT'),
        'dbname' => getenv('MYSQLDATABASE'),
        'user' => getenv('MYSQLUSER'),
        'pass' => getenv('MYSQLPASSWORD')
    ],
    'Railway DATABASE_URL' => [
        'url' => getenv('DATABASE_URL')
    ],
    'Local fallback' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'projectslides',
        'user' => 'DaviSena',
        'pass' => '197508'
    ]
];

foreach ($configs as $name => $config) {
    echo "<h3>{$name}:</h3>";
    
    if (isset($config['url'])) {
        // Tentar DATABASE_URL
        if ($config['url']) {
            try {
                $pdo = new PDO($config['url']);
                echo "✅ Conexão via DATABASE_URL: SUCESSO<br>";
                
                // Testar tabelas
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "Tabelas encontradas: " . implode(', ', $tables) . "<br>";
                
                // Contar perguntas
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
                $result = $stmt->fetch();
                echo "Total de perguntas: " . $result['count'] . "<br>";
                
            } catch (Exception $e) {
                echo "❌ Erro: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "❌ DATABASE_URL não definida<br>";
        }
    } else {
        // Tentar conexão normal
        if ($config['host'] && $config['dbname'] && $config['user']) {
            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
                $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                echo "✅ Conexão: SUCESSO<br>";
                
                // Testar tabelas
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "Tabelas: " . implode(', ', $tables) . "<br>";
                
                // Contar perguntas
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
                $result = $stmt->fetch();
                echo "Perguntas: " . $result['count'] . "<br>";
                
            } catch (Exception $e) {
                echo "❌ Erro: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "❌ Configuração incompleta<br>";
        }
    }
    echo "<hr>";
}

echo "<h2>Teste do QuestionService:</h2>";
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $questionService = new App\Services\QuestionService();
    $questions = $questionService->getAllQuestions();
    echo "✅ QuestionService funcionando<br>";
    echo "Perguntas carregadas: " . count($questions) . "<br>";
    
    foreach ($questions as $q) {
        echo "- ID: {$q['id']}, Título: " . substr($q['title'], 0, 50) . "...<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro no QuestionService: " . $e->getMessage() . "<br>";
}
?>
