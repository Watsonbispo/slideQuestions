<?php
// Configuração local para desenvolvimento
echo "<h1>Configuração Local</h1>";

// Forçar configurações locais
$_ENV['DB_HOST'] = '127.0.0.1';
$_ENV['DB_PORT'] = '3306';
$_ENV['DB_NAME'] = 'projectslides';
$_ENV['DB_USER'] = 'DaviSena';
$_ENV['DB_PASS'] = '197508';

echo "<h2>Configurações Aplicadas:</h2>";
echo "Host: " . $_ENV['DB_HOST'] . "<br>";
echo "Port: " . $_ENV['DB_PORT'] . "<br>";
echo "Database: " . $_ENV['DB_NAME'] . "<br>";
echo "User: " . $_ENV['DB_USER'] . "<br>";
echo "Password: " . (empty($_ENV['DB_PASS']) ? 'VAZIA' : 'DEFINIDA') . "<br>";

echo "<h2>Teste de Conexão:</h2>";
try {
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $dbname = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Conexão local: SUCESSO<br>";
    
    // Verificar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas: " . implode(', ', $tables) . "<br>";
    
    // Contar perguntas
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
    $result = $stmt->fetch();
    echo "Perguntas: " . $result['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    echo "<p>Você precisa configurar um banco MySQL local ou usar o Railway.</p>";
}

echo "<h2>Links Úteis:</h2>";
echo '<a href="/">Página Principal</a><br>';
echo '<a href="/debug.php">Debug Completo</a><br>';
echo '<a href="/setup-database.php">Setup Database</a><br>';
?>
