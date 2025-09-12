<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

echo "<h1>Teste do Admin no Railway</h1>";

// Teste 1: Verificar variáveis de ambiente
echo "<h2>1. Variáveis de Ambiente</h2>";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'NÃO DEFINIDA') . "<br>";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ?: 'NÃO DEFINIDA') . "<br>";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'NÃO DEFINIDA') . "<br>";
echo "MYSQLUSER: " . (getenv('MYSQLUSER') ?: 'NÃO DEFINIDA') . "<br>";
echo "MYSQLPASSWORD: " . (getenv('MYSQLPASSWORD') ?: 'NÃO DEFINIDA') . "<br>";

// Teste 2: Verificar configuração do banco
echo "<h2>2. Configuração do Banco</h2>";
$host = $_ENV['MYSQLHOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['MYSQLPORT'] ?? $_ENV['DB_PORT'] ?? '3306';
$name = $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME'] ?? 'projectslides';
$user = $_ENV['MYSQLUSER'] ?? $_ENV['DB_USER'] ?? 'DaviSena';
$pass = $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS'] ?? '197508';

echo "Host: {$host}<br>";
echo "Port: {$port}<br>";
echo "Database: {$name}<br>";
echo "User: {$user}<br>";
echo "Password: " . (empty($pass) ? 'VAZIA' : 'DEFINIDA') . "<br>";

// Teste 3: Testar conexão com banco
echo "<h2>3. Teste de Conexão</h2>";
try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "✅ Conexão com banco: SUCESSO<br>";
    
    // Teste 4: Verificar tabelas
    echo "<h2>4. Verificação de Tabelas</h2>";
    $tables = ['admins', 'questions', 'question_options', 'settings'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            $exists = $stmt->fetch();
            echo "Tabela {$table}: " . ($exists ? "✅ EXISTE" : "❌ NÃO EXISTE") . "<br>";
        } catch (Exception $e) {
            echo "Tabela {$table}: ❌ ERRO - " . $e->getMessage() . "<br>";
        }
    }
    
    // Teste 5: Verificar admin
    echo "<h2>5. Verificação de Admin</h2>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
        $result = $stmt->fetch();
        echo "Total de admins: " . $result['count'] . "<br>";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT username FROM admins LIMIT 1");
            $admin = $stmt->fetch();
            echo "Primeiro admin: " . $admin['username'] . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao verificar admins: " . $e->getMessage() . "<br>";
    }
    
    // Teste 6: Testar AuthService
    echo "<h2>6. Teste do AuthService</h2>";
    try {
        $auth = new App\Services\AuthService();
        echo "✅ AuthService carregado com sucesso<br>";
        
        // Teste de login (sem senha real)
        $loginResult = $auth->login('Davi Sena', '197508');
        echo "Teste de login: " . ($loginResult ? "✅ SUCESSO" : "❌ FALHOU") . "<br>";
        
        if ($loginResult) {
            echo "Status de login: " . ($auth->isLoggedIn() ? "✅ LOGADO" : "❌ NÃO LOGADO") . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro no AuthService: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "<br>";
}

echo "<h2>7. Teste de Rotas Admin</h2>";
echo '<a href="/admin/login" target="_blank">Testar /admin/login</a><br>';
echo '<a href="/admin/questions" target="_blank">Testar /admin/questions</a><br>';
echo '<a href="/admin/csrf-token" target="_blank">Testar /admin/csrf-token</a><br>';

echo "<h2>8. Teste do Modal</h2>";
echo '<button onclick="testModal()">Testar Modal Admin</button>';
echo '<script>
function testModal() {
    if (typeof openAdminModal === "function") {
        openAdminModal();
    } else {
        alert("Função openAdminModal não encontrada!");
    }
}
</script>';
?>

