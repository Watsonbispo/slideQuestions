<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

echo "<!DOCTYPE html><html><head><title>Teste Redirecionamento</title><style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} .section{margin:20px 0;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";
echo "<h1>🔗 Teste de Configuração de Redirecionamento</h1>";

// 1. Testar conexão com banco
echo "<div class='section'>";
echo "<h2>🔌 Teste de Conexão</h2>";
try {
    $pdo = App\Support\Database::getConnection();
    echo "<span class='success'>✅ Conexão com banco: SUCESSO</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro de conexão: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    exit;
}
echo "</div>";

// 2. Testar SettingsService
echo "<div class='section'>";
echo "<h2>⚙️ Teste do SettingsService</h2>";
try {
    $settings = new App\Services\SettingsService();
    $redirectUrl = $settings->getRedirectUrl();
    echo "<span class='info'>URL de redirecionamento atual: " . htmlspecialchars($redirectUrl) . "</span><br>";
    
    // Testar atualização
    $testUrl = 'https://teste.com/checkout/123';
    $updateResult = $settings->updateRedirectUrl($testUrl);
    
    if ($updateResult) {
        echo "<span class='success'>✅ Atualização de URL: SUCESSO</span><br>";
        
        // Verificar se foi salvo
        $newUrl = $settings->getRedirectUrl();
        echo "<span class='info'>Nova URL: " . htmlspecialchars($newUrl) . "</span><br>";
        
        if ($newUrl === $testUrl) {
            echo "<span class='success'>✅ URL salva corretamente!</span><br>";
        } else {
            echo "<span class='error'>❌ URL não foi salva corretamente</span><br>";
        }
        
        // Restaurar URL original
        $settings->updateRedirectUrl('https://example.com/checkout/ABC123');
        echo "<span class='info'>URL restaurada para o padrão</span><br>";
    } else {
        echo "<span class='error'>❌ Erro ao atualizar URL</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro no SettingsService: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 3. Testar rotas admin
echo "<div class='section'>";
echo "<h2>🌐 Teste de Rotas Admin</h2>";

// Simular login
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_user_id'] = 1;

echo "<h3>GET /admin/redirect-url</h3>";
try {
    $adminController = new App\Controllers\AdminController();
    
    // Capturar output
    ob_start();
    $adminController->getRedirectUrl();
    $output = ob_get_clean();
    
    echo "<span class='info'>Resposta: " . htmlspecialchars($output) . "</span><br>";
    
    $data = json_decode($output, true);
    if ($data && $data['success']) {
        echo "<span class='success'>✅ GET /admin/redirect-url: SUCESSO</span><br>";
    } else {
        echo "<span class='error'>❌ GET /admin/redirect-url: FALHOU</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro na rota GET: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

echo "<h3>POST /admin/redirect-url</h3>";
try {
    // Simular POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $input = json_encode(['redirect_url' => 'https://teste-post.com/checkout']);
    
    // Simular input stream
    $temp = tmpfile();
    fwrite($temp, $input);
    rewind($temp);
    
    // Backup original input
    $originalInput = 'php://input';
    
    // Simular file_get_contents('php://input')
    $GLOBALS['mock_input'] = $input;
    
    // Criar função mock
    if (!function_exists('file_get_contents_mock')) {
        function file_get_contents_mock($filename) {
            if ($filename === 'php://input' && isset($GLOBALS['mock_input'])) {
                return $GLOBALS['mock_input'];
            }
            return file_get_contents($filename);
        }
    }
    
    $adminController = new App\Controllers\AdminController();
    
    // Capturar output
    ob_start();
    $adminController->updateRedirectUrl();
    $output = ob_get_clean();
    
    echo "<span class='info'>Resposta: " . htmlspecialchars($output) . "</span><br>";
    
    $data = json_decode($output, true);
    if ($data && $data['success']) {
        echo "<span class='success'>✅ POST /admin/redirect-url: SUCESSO</span><br>";
    } else {
        echo "<span class='error'>❌ POST /admin/redirect-url: FALHOU</span><br>";
    }
    
    fclose($temp);
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro na rota POST: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// 4. Verificar tabela settings
echo "<div class='section'>";
echo "<h2>📊 Verificação da Tabela Settings</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM settings WHERE `key` = 'redirect_url'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<span class='success'>✅ Configuração encontrada na tabela</span><br>";
        echo "<span class='info'>Key: " . htmlspecialchars($result['key']) . "</span><br>";
        echo "<span class='info'>Value: " . htmlspecialchars($result['value']) . "</span><br>";
    } else {
        echo "<span class='error'>❌ Configuração NÃO encontrada na tabela</span><br>";
    }
    
    // Mostrar todas as configurações
    echo "<h3>Todas as configurações:</h3>";
    $stmt = $pdo->query("SELECT * FROM settings");
    $allSettings = $stmt->fetchAll();
    
    foreach ($allSettings as $setting) {
        echo "<span class='info'>{$setting['key']}: {$setting['value']}</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro ao verificar tabela: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>🛠️ Instruções</h2>";
echo "<p>1. Verifique se todas as seções mostram ✅</p>";
echo "<p>2. Se houver ❌, verifique os logs do Railway</p>";
echo "<p>3. Teste a funcionalidade no painel admin</p>";
echo "</div>";

echo "</body></html>";
?>
