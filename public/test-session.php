<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<!DOCTYPE html><html><head><title>Teste de Sessão</title><style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";
echo "<h1>📝 Teste de Sessão - Railway</h1>";

// 1. Informações da sessão
echo "<div class='section'>";
echo "<h2>📋 Informações da Sessão</h2>";
echo "<span class='info'><strong>Session ID:</strong> " . session_id() . "</span><br>";
echo "<span class='info'><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'ATIVA' : 'INATIVA') . "</span><br>";
echo "<span class='info'><strong>Session Name:</strong> " . session_name() . "</span><br>";
echo "<span class='info'><strong>Session Save Path:</strong> " . session_save_path() . "</span><br>";
echo "<span class='info'><strong>Session Cookie Params:</strong></span><br>";
$cookieParams = session_get_cookie_params();
foreach ($cookieParams as $key => $value) {
    echo "&nbsp;&nbsp;&nbsp;&nbsp;<strong>{$key}:</strong> " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "<br>";
}
echo "</div>";

// 2. Variáveis de sessão atuais
echo "<div class='section'>";
echo "<h2>🔍 Variáveis de Sessão Atuais</h2>";
if (empty($_SESSION)) {
    echo "<span class='warning'>⚠️ Nenhuma variável de sessão definida</span><br>";
} else {
    foreach ($_SESSION as $key => $value) {
        echo "<span class='info'><strong>\$_SESSION['{$key}']:</strong> " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "</span><br>";
    }
}
echo "</div>";

// 3. Teste de login
echo "<div class='section'>";
echo "<h2>🔐 Teste de Login</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Testando login com: '{$username}'</h3>";
    
    try {
        $auth = new App\Services\AuthService();
        $loginResult = $auth->login($username, $password);
        
        if ($loginResult) {
            echo "<span class='success'>✅ Login realizado com sucesso!</span><br>";
            echo "<span class='info'>Status de login: " . ($auth->isLoggedIn() ? 'LOGADO' : 'NÃO LOGADO') . "</span><br>";
            echo "<span class='info'>User ID: " . ($auth->getCurrentUserId() ?? 'NULL') . "</span><br>";
            
            // Mostrar variáveis de sessão após login
            echo "<h4>Variáveis de sessão após login:</h4>";
            foreach ($_SESSION as $key => $value) {
                echo "<span class='info'><strong>\$_SESSION['{$key}']:</strong> " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "</span><br>";
            }
        } else {
            echo "<span class='error'>❌ Login falhou!</span><br>";
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
}

echo "<form method='POST'>";
echo "<input type='text' name='username' placeholder='Usuário' value='Davi Sena' required><br><br>";
echo "<input type='password' name='password' placeholder='Senha' value='197508' required><br><br>";
echo "<button type='submit' name='test_login'>Testar Login</button>";
echo "</form>";
echo "</div>";

// 4. Teste de persistência de sessão
echo "<div class='section'>";
echo "<h2>🔄 Teste de Persistência de Sessão</h2>";

if (isset($_GET['set_test'])) {
    $_SESSION['test_variable'] = 'Teste de persistência - ' . date('Y-m-d H:i:s');
    echo "<span class='success'>✅ Variável de teste definida!</span><br>";
    echo "<a href='?'>Recarregar página para testar persistência</a><br>";
} elseif (isset($_SESSION['test_variable'])) {
    echo "<span class='success'>✅ Sessão persistiu! Valor: " . $_SESSION['test_variable'] . "</span><br>";
    unset($_SESSION['test_variable']);
} else {
    echo "<a href='?set_test=1'>Definir variável de teste</a><br>";
}
echo "</div>";

// 5. Teste de logout
echo "<div class='section'>";
echo "<h2>🚪 Teste de Logout</h2>";

if (isset($_POST['test_logout'])) {
    try {
        $auth = new App\Services\AuthService();
        $auth->logout();
        echo "<span class='success'>✅ Logout realizado!</span><br>";
        echo "<a href='?'>Recarregar página</a><br>";
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erro no logout: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    echo "<form method='POST'>";
    echo "<button type='submit' name='test_logout'>Testar Logout</button>";
    echo "</form>";
} else {
    echo "<span class='info'>Faça login primeiro para testar o logout</span><br>";
}
echo "</div>";

// 6. Informações do servidor
echo "<div class='section'>";
echo "<h2>🖥️ Informações do Servidor</h2>";
echo "<span class='info'><strong>PHP Version:</strong> " . PHP_VERSION . "</span><br>";
echo "<span class='info'><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</span><br>";
echo "<span class='info'><strong>Railway Environment:</strong> " . (getenv('RAILWAY_ENVIRONMENT') ?: 'NÃO DEFINIDA') . "</span><br>";
echo "<span class='info'><strong>Session Module:</strong> " . (extension_loaded('session') ? 'CARREGADO' : 'NÃO CARREGADO') . "</span><br>";
echo "<span class='info'><strong>Session Handler:</strong> " . session_module_name() . "</span><br>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>🛠️ Instruções</h2>";
echo "<p>1. Teste o login com as credenciais corretas</p>";
echo "<p>2. Verifique se as variáveis de sessão são definidas corretamente</p>";
echo "<p>3. Teste a persistência da sessão recarregando a página</p>";
echo "<p>4. Se algo não funcionar, verifique os logs do Railway</p>";
echo "</div>";

echo "</body></html>";
?>
