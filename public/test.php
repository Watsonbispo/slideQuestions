<?php
echo "PHP está funcionando!<br>";
echo "Versão do PHP: " . phpversion() . "<br>";
echo "Data/Hora: " . date('Y-m-d H:i:s') . "<br>";

// Testa se o autoloader está funcionando
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "Autoloader carregado com sucesso!<br>";
} catch (Exception $e) {
    echo "Erro no autoloader: " . $e->getMessage() . "<br>";
}

// Testa variáveis de ambiente
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'não definido') . "<br>";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ?: 'não definido') . "<br>";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'não definido') . "<br>";
?>
