<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

echo "<!DOCTYPE html><html><head><title>Configurar Banco - Railway</title><style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";
echo "<h1>🔧 Configuração do Banco de Dados - Railway</h1>";

try {
    // Conecta ao banco usando as variáveis de ambiente do Railway
    $host = $_ENV['MYSQLHOST'] ?? $_ENV['DB_HOST'] ?? null;
    $port = $_ENV['MYSQLPORT'] ?? $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME'] ?? null;
    $user = $_ENV['MYSQLUSER'] ?? $_ENV['DB_USER'] ?? null;
    $password = $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
    
    echo "<div class='section'>";
    echo "<h2>📋 Configuração de Conexão</h2>";
    echo "<span class='info'>Host: " . ($host ?: 'NÃO DEFINIDO') . "</span><br>";
    echo "<span class='info'>Porta: {$port}</span><br>";
    echo "<span class='info'>Banco: " . ($dbname ?: 'NÃO DEFINIDO') . "</span><br>";
    echo "<span class='info'>Usuário: " . ($user ?: 'NÃO DEFINIDO') . "</span><br>";
    echo "<span class='info'>Senha: " . (empty($password) ? 'VAZIA' : 'DEFINIDA') . "</span><br>";
    echo "</div>";
    
    if (!$host || !$dbname || !$user) {
        throw new Exception("Configuração de banco incompleta. Verifique as variáveis de ambiente no Railway.");
    }
    
    echo "<div class='section'>";
    echo "<h2>🔌 Conectando ao Banco</h2>";
    
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "<span class='success'>✅ Conexão estabelecida com sucesso!</span><br>";
    echo "</div>";
    
    // Lê o arquivo SQL
    echo "<div class='section'>";
    echo "<h2>📄 Executando Schema</h2>";
    
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    if (!$sql) {
        throw new Exception("Não foi possível ler o arquivo database/schema.sql");
    }
    
    // Divide em statements e executa
    $statements = explode(';', $sql);
    $executed = 0;
    $errors = 0;
    
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt) && !preg_match('/^--/', $stmt)) {
            try {
                $pdo->exec($stmt);
                $executed++;
            } catch (PDOException $e) {
                // Ignora erros de tabela já existe ou dados já inseridos
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate entry') === false) {
                    echo "<span class='warning'>⚠️ Aviso: " . htmlspecialchars($e->getMessage()) . "</span><br>";
                    $errors++;
                }
            }
        }
    }
    
    echo "<span class='success'>✅ Schema executado com sucesso!</span><br>";
    echo "<span class='info'>📊 Statements executados: {$executed}</span><br>";
    if ($errors > 0) {
        echo "<span class='warning'>⚠️ Avisos: {$errors}</span><br>";
    }
    echo "</div>";
    
    // Verificar dados inseridos
    echo "<div class='section'>";
    echo "<h2>📊 Verificação de Dados</h2>";
    
    try {
        // Verificar perguntas
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
        $result = $stmt->fetch();
        echo "<span class='info'>📝 Perguntas criadas: " . $result['count'] . "</span><br>";
        
        // Verificar admins
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
        $result = $stmt->fetch();
        echo "<span class='info'>👤 Admins criados: " . $result['count'] . "</span><br>";
        
        // Verificar configurações
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM settings");
        $result = $stmt->fetch();
        echo "<span class='info'>⚙️ Configurações: " . $result['count'] . "</span><br>";
        
        if ($result['count'] > 0) {
            echo "<span class='success'>✅ Banco configurado com sucesso!</span><br>";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erro na verificação: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>🔗 Próximos Passos</h2>";
    echo "<a href='/railway-debug.php' target='_blank'>🔧 Verificar Status Completo</a><br>";
    echo "<a href='/test-admin.php' target='_blank'>👤 Testar Admin</a><br>";
    echo "<a href='/' target='_blank'>🏠 Ir para Aplicação</a><br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2>❌ Erro</h2>";
    echo "<span class='error'>" . htmlspecialchars($e->getMessage()) . "</span><br>";
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>🛠️ Solução</h2>";
    echo "<span class='info'>1. Acesse o painel do Railway: https://railway.app/dashboard</span><br>";
    echo "<span class='info'>2. Selecione seu projeto</span><br>";
    echo "<span class='info'>3. Clique em '+ New' → 'Database' → 'MySQL'</span><br>";
    echo "<span class='info'>4. Aguarde o MySQL ser criado</span><br>";
    echo "<span class='info'>5. Execute este script novamente</span><br>";
    echo "</div>";
}

echo "</body></html>";
?>
