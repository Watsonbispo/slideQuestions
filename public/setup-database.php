<?php
// Script para configurar o banco de dados no Railway
echo "Configurando banco de dados...<br>";

try {
    // Carrega variáveis de ambiente
    if (is_file(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
    }
    
    // Conecta ao banco usando as variáveis de ambiente do Railway
    $host = $_ENV['MYSQLHOST'] ?? $_ENV['DB_HOST'] ?? 'mysql.railway.internal';
    $port = $_ENV['MYSQLPORT'] ?? $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME'] ?? 'railway';
    $user = $_ENV['MYSQLUSER'] ?? $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
    
    echo "Conectando ao banco: {$host}:{$port}/{$dbname}<br>";
    echo "Usuário: {$user}<br>";
    
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "Conexão estabelecida com sucesso!<br>";
    
    // Lê o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    if (!$sql) {
        throw new Exception("Não foi possível ler o arquivo database/schema.sql");
    }
    
    // Divide em statements e executa
    $statements = explode(';', $sql);
    $executed = 0;
    
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt) && !preg_match('/^--/', $stmt)) {
            try {
                $pdo->exec($stmt);
                $executed++;
            } catch (PDOException $e) {
                // Ignora erros de tabela já existe
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "Aviso: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
    
    echo "Configuração do banco concluída! {$executed} statements executados.<br>";
    echo "<br><a href='/'>← Voltar para a aplicação</a>";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
    echo "<br><a href='/'>← Voltar para a aplicação</a>";
}
?>
