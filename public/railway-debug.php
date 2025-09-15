<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

echo "<!DOCTYPE html><html><head><title>Debug Railway</title><style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";
echo "<h1>🔧 Debug Railway - ProjectSlides</h1>";

// 1. Verificar variáveis de ambiente
echo "<div class='section'>";
echo "<h2>📋 1. Variáveis de Ambiente</h2>";
$env_vars = [
    'MYSQLHOST' => 'Host do MySQL',
    'MYSQLPORT' => 'Porta do MySQL', 
    'MYSQLDATABASE' => 'Nome do banco',
    'MYSQLUSER' => 'Usuário do banco',
    'MYSQLPASSWORD' => 'Senha do banco',
    'RAILWAY_ENVIRONMENT' => 'Ambiente Railway',
    'DATABASE_URL' => 'URL completa do banco'
];

$has_mysql_vars = false;
foreach ($env_vars as $var => $desc) {
    $value = getenv($var);
    $status = $value ? 'success' : 'error';
    $display_value = $value ?: 'NÃO DEFINIDA';
    
    if (in_array($var, ['MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD']) && $value) {
        $has_mysql_vars = true;
    }
    
    echo "<span class='{$status}'><strong>{$var}</strong> ({$desc}): {$display_value}</span><br>";
}
echo "</div>";

// 2. Testar diferentes configurações de conexão
echo "<div class='section'>";
echo "<h2>🔌 2. Teste de Conexões</h2>";

$configs = [
    'Railway MySQL (MYSQL*)' => [
        'host' => getenv('MYSQLHOST'),
        'port' => getenv('MYSQLPORT') ?: '3306',
        'dbname' => getenv('MYSQLDATABASE'),
        'user' => getenv('MYSQLUSER'),
        'pass' => getenv('MYSQLPASSWORD')
    ],
    'Railway DATABASE_URL' => [
        'url' => getenv('DATABASE_URL')
    ],
    'Configuração Alternativa' => [
        'host' => $_ENV['DB_HOST'] ?? null,
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'dbname' => $_ENV['DB_NAME'] ?? null,
        'user' => $_ENV['DB_USER'] ?? null,
        'pass' => $_ENV['DB_PASS'] ?? null
    ]
];

$working_config = null;
foreach ($configs as $name => $config) {
    echo "<h3>{$name}:</h3>";
    
    if (isset($config['url']) && $config['url']) {
        // Testar DATABASE_URL
        try {
            $pdo = new PDO($config['url']);
            echo "<span class='success'>✅ Conexão via DATABASE_URL: SUCESSO</span><br>";
            $working_config = $pdo;
            
            // Verificar tabelas
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<span class='info'>📋 Tabelas: " . implode(', ', $tables) . "</span><br>";
            
        } catch (Exception $e) {
            echo "<span class='error'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</span><br>";
        }
    } else {
        // Testar conexão normal
        if ($config['host'] && $config['dbname'] && $config['user']) {
            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";
                $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                echo "<span class='success'>✅ Conexão: SUCESSO</span><br>";
                $working_config = $pdo;
                
                // Verificar tabelas
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "<span class='info'>📋 Tabelas: " . implode(', ', $tables) . "</span><br>";
                
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

// 3. Verificar dados do banco
if ($working_config) {
    echo "<div class='section'>";
    echo "<h2>📊 3. Verificação de Dados</h2>";
    
    try {
        // Verificar perguntas
        $stmt = $working_config->query("SELECT COUNT(*) as count FROM questions");
        $result = $stmt->fetch();
        $question_count = $result['count'];
        echo "<span class='info'>📝 Total de perguntas: {$question_count}</span><br>";
        
        if ($question_count > 0) {
            $stmt = $working_config->query("SELECT id, title, is_active FROM questions ORDER BY sort_order");
            $questions = $stmt->fetchAll();
            echo "<span class='info'>📋 Lista de perguntas:</span><br>";
            foreach ($questions as $q) {
                $status = $q['is_active'] ? '✅' : '❌';
                echo "&nbsp;&nbsp;{$status} ID: {$q['id']} - " . htmlspecialchars(substr($q['title'], 0, 50)) . "...<br>";
            }
        } else {
            echo "<span class='warning'>⚠️ Nenhuma pergunta encontrada no banco!</span><br>";
        }
        
        // Verificar admins
        $stmt = $working_config->query("SELECT COUNT(*) as count FROM admins");
        $result = $stmt->fetch();
        $admin_count = $result['count'];
        echo "<span class='info'>👤 Total de admins: {$admin_count}</span><br>";
        
        if ($admin_count > 0) {
            $stmt = $working_config->query("SELECT username FROM admins");
            $admins = $stmt->fetchAll();
            echo "<span class='info'>👤 Admins disponíveis:</span><br>";
            foreach ($admins as $admin) {
                echo "&nbsp;&nbsp;• " . htmlspecialchars($admin['username']) . "<br>";
            }
        } else {
            echo "<span class='warning'>⚠️ Nenhum admin encontrado!</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erro ao verificar dados: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
    echo "</div>";
}

// 4. Testar QuestionService
echo "<div class='section'>";
echo "<h2>🔧 4. Teste do QuestionService</h2>";
try {
    $questionService = new App\Services\QuestionService();
    $questions = $questionService->getQuestions(); // Perguntas ativas
    $allQuestions = $questionService->getAllQuestions(); // Todas as perguntas
    
    echo "<span class='success'>✅ QuestionService carregado</span><br>";
    echo "<span class='info'>📝 Perguntas ativas: " . count($questions) . "</span><br>";
    echo "<span class='info'>📝 Total de perguntas: " . count($allQuestions) . "</span><br>";
    
    if (count($questions) <= 2 && count($allQuestions) <= 2) {
        echo "<span class='warning'>⚠️ Usando dados de fallback (hardcoded)!</span><br>";
        echo "<span class='info'>🔍 Isso indica que a conexão com o banco está falhando.</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro no QuestionService: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 5. Testar AuthService
echo "<div class='section'>";
echo "<h2>🔐 5. Teste do AuthService</h2>";
try {
    $auth = new App\Services\AuthService();
    echo "<span class='success'>✅ AuthService carregado</span><br>";
    
    // Teste de login
    $loginResult = $auth->login('Davi Sena', '197508');
    echo "<span class='info'>🔑 Teste de login: " . ($loginResult ? "✅ SUCESSO" : "❌ FALHOU") . "</span><br>";
    
    if ($loginResult) {
        echo "<span class='info'>🔑 Status de login: " . ($auth->isLoggedIn() ? "✅ LOGADO" : "❌ NÃO LOGADO") . "</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro no AuthService: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 6. Instruções para correção
echo "<div class='section'>";
echo "<h2>🛠️ 6. Instruções para Correção</h2>";

if (!$has_mysql_vars) {
    echo "<span class='error'>❌ PROBLEMA: Variáveis MySQL não configuradas no Railway</span><br>";
    echo "<span class='info'>📋 SOLUÇÃO:</span><br>";
    echo "1. Acesse o painel do Railway: https://railway.app/dashboard<br>";
    echo "2. Selecione seu projeto<br>";
    echo "3. Clique em <strong>'+ New'</strong> ou <strong>'Add Service'</strong><br>";
    echo "4. Selecione <strong>'Database'</strong> → <strong>'MySQL'</strong><br>";
    echo "5. O Railway criará automaticamente as variáveis MYSQL*<br>";
    echo "6. Após criar o MySQL, execute: <a href='/setup-database.php' target='_blank'>/setup-database.php</a><br>";
} else {
    echo "<span class='success'>✅ Variáveis MySQL configuradas</span><br>";
    if (!$working_config) {
        echo "<span class='warning'>⚠️ Mas a conexão está falhando</span><br>";
        echo "<span class='info'>📋 Verifique se o MySQL foi criado e está rodando</span><br>";
    }
}

if ($working_config) {
    echo "<span class='success'>✅ Banco de dados funcionando</span><br>";
    echo "<span class='info'>📋 Próximos passos:</span><br>";
    echo "1. Se não há perguntas, execute: <a href='/setup-database.php' target='_blank'>/setup-database.php</a><br>";
    echo "2. Teste o admin: <a href='/test-admin.php' target='_blank'>/test-admin.php</a><br>";
    echo "3. Acesse a aplicação: <a href='/' target='_blank'>Página principal</a><br>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>🔗 Links Úteis</h2>";
echo "<a href='/setup-database.php' target='_blank'>🔧 Configurar Banco</a><br>";
echo "<a href='/test-admin.php' target='_blank'>👤 Testar Admin</a><br>";
echo "<a href='/debug-db.php' target='_blank'>🐛 Debug Banco</a><br>";
echo "<a href='/' target='_blank'>🏠 Página Principal</a><br>";
echo "</div>";

echo "</body></html>";
?>
