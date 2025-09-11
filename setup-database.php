<?php
// Script para configurar o banco de dados no Railway
echo "Configurando banco de dados...\n";

try {
    // Conecta ao banco usando as variáveis do Railway (já disponíveis no ambiente)
    $host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
    $port = getenv('MYSQLPORT') ?: '3306';
    $dbname = getenv('MYSQLDATABASE') ?: 'railway';
    $user = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    
    echo "Conectando ao banco: {$host}:{$port}/{$dbname}\n";
    
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "Conexão estabelecida com sucesso!\n";
    
    // Lê o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
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
                    echo "Aviso: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Configuração do banco concluída! {$executed} statements executados.\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
