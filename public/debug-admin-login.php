<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

echo "<!DOCTYPE html><html><head><title>Debug Admin Login</title><style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";
echo "<h1>🔐 Debug Admin Login - Railway</h1>";

// 1. Verificar variáveis de ambiente
echo "<div class='section'>";
echo "<h2>📋 Variáveis de Ambiente</h2>";

$env_vars = [
    'MYSQLHOST' => getenv('MYSQLHOST'),
    'MYSQLPORT' => getenv('MYSQLPORT'),
    'MYSQLDATABASE' => getenv('MYSQLDATABASE'),
    'MYSQLUSER' => getenv('MYSQLUSER'),
    'MYSQLPASSWORD' => getenv('MYSQLPASSWORD'),
    'DATABASE_URL' => getenv('DATABASE_URL'),
    'RAILWAY_ENVIRONMENT' => getenv('RAILWAY_ENVIRONMENT')
];

foreach ($env_vars as $key => $value) {
    echo "<span class='info'><strong>{$key}:</strong> " . ($value ?: 'NÃO DEFINIDA') . "</span><br>";
}
echo "</div>";

// 2. Testar conexão com banco
echo "<div class='section'>";
echo "<h2>🔌 Teste de Conexão</h2>";

try {
    $pdo = App\Support\Database::getConnection();
    echo "<span class='success'>✅ Conexão com banco: SUCESSO</span><br>";
    
    // Verificar se tabela admins existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    $adminsTable = $stmt->fetch();
    
    if ($adminsTable) {
        echo "<span class='success'>✅ Tabela 'admins' existe</span><br>";
        
        // Contar admins
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
        $result = $stmt->fetch();
        echo "<span class='info'>📊 Total de admins: " . $result['count'] . "</span><br>";
        
        // Listar admins
        $stmt = $pdo->query("SELECT id, username, created_at FROM admins");
        $admins = $stmt->fetchAll();
        
        echo "<h3>👥 Lista de Admins:</h3>";
        foreach ($admins as $admin) {
            echo "<span class='info'>ID: {$admin['id']}, Username: {$admin['username']}, Criado: {$admin['created_at']}</span><br>";
        }
        
    } else {
        echo "<span class='error'>❌ Tabela 'admins' NÃO existe</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro de conexão: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 3. Testar AuthService
echo "<div class='section'>";
echo "<h2>🔐 Teste do AuthService</h2>";

try {
    $auth = new App\Services\AuthService();
    echo "<span class='success'>✅ AuthService carregado com sucesso</span><br>";
    
    // Testar login com credenciais padrão
    $testCredentials = [
        ['Davi Sena', '197508'],
        ['DaviSena', '197508'],
        ['admin', '197508'],
        ['davi sena', '197508']
    ];
    
    foreach ($testCredentials as $i => $cred) {
        $username = $cred[0];
        $password = $cred[1];
        
        echo "<h4>Teste " . ($i + 1) . ": '{$username}' / '{$password}'</h4>";
        
        $loginResult = $auth->login($username, $password);
        echo "<span class='" . ($loginResult ? 'success' : 'error') . "'>" . 
             ($loginResult ? '✅ LOGIN SUCESSO' : '❌ LOGIN FALHOU') . "</span><br>";
        
        if ($loginResult) {
            echo "<span class='info'>Status de login: " . ($auth->isLoggedIn() ? '✅ LOGADO' : '❌ NÃO LOGADO') . "</span><br>";
            echo "<span class='info'>User ID: " . ($auth->getCurrentUserId() ?? 'NULL') . "</span><br>";
            $auth->logout(); // Limpar sessão para próximo teste
        }
        echo "<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro no AuthService: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 4. Testar rotas admin
echo "<div class='section'>";
echo "<h2>🌐 Teste de Rotas Admin</h2>";

$adminRoutes = [
    '/admin/csrf-token' => 'GET',
    '/admin/login' => 'POST',
    '/admin/questions' => 'GET',
    '/admin/logout' => 'POST'
];

foreach ($adminRoutes as $route => $method) {
    echo "<h4>{$method} {$route}</h4>";
    
    if ($method === 'GET') {
        echo "<a href='{$route}' target='_blank'>Testar {$route}</a><br>";
    } else {
        echo "<span class='info'>Rota {$method}: {$route} (requer dados POST)</span><br>";
    }
}
echo "</div>";

// 5. Teste de hash de senha
echo "<div class='section'>";
echo "<h2>🔑 Teste de Hash de Senha</h2>";

try {
    $pdo = App\Support\Database::getConnection();
    $stmt = $pdo->query("SELECT username, password_hash FROM admins LIMIT 1");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<span class='info'>Username: {$admin['username']}</span><br>";
        echo "<span class='info'>Hash: {$admin['password_hash']}</span><br>";
        
        // Testar diferentes senhas
        $testPasswords = ['197508', 'admin', 'password', '123456'];
        
        foreach ($testPasswords as $password) {
            $verify = password_verify($password, $admin['password_hash']);
            echo "<span class='" . ($verify ? 'success' : 'error') . "'>Senha '{$password}': " . 
                 ($verify ? '✅ VÁLIDA' : '❌ INVÁLIDA') . "</span><br>";
        }
    } else {
        echo "<span class='error'>❌ Nenhum admin encontrado no banco</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 6. Teste de sessão
echo "<div class='section'>";
echo "<h2>📝 Teste de Sessão</h2>";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<span class='info'>Session ID: " . session_id() . "</span><br>";
echo "<span class='info'>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ATIVA' : 'INATIVA') . "</span><br>";

// Verificar variáveis de sessão
$sessionVars = ['admin_logged_in', 'admin_user_id'];
foreach ($sessionVars as $var) {
    $value = $_SESSION[$var] ?? 'NÃO DEFINIDA';
    echo "<span class='info'>\$_SESSION['{$var}']: {$value}</span><br>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>🛠️ Instruções</h2>";
echo "<p>1. Verifique se todas as variáveis de ambiente estão definidas</p>";
echo "<p>2. Confirme se a tabela 'admins' existe e tem dados</p>";
echo "<p>3. Teste o login com as credenciais corretas</p>";
echo "<p>4. Se o problema persistir, verifique os logs do Railway</p>";
echo "</div>";

echo "</body></html>";
?>
