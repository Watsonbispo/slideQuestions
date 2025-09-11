<?php
echo "=== VARIÁVEIS DE AMBIENTE ===<br><br>";

$env_vars = [
    'MYSQLHOST',
    'MYSQLPORT', 
    'MYSQLDATABASE',
    'MYSQLUSER',
    'MYSQLPASSWORD'
];

foreach ($env_vars as $var) {
    $value = getenv($var);
    if ($value) {
        echo "✅ {$var}: " . (strpos($var, 'PASSWORD') !== false ? '***' : $value) . "<br>";
    } else {
        echo "❌ {$var}: não definido<br>";
    }
}

echo "<br>=== TODAS AS VARIÁVEIS ===<br>";
$all_env = getenv();
foreach ($all_env as $key => $value) {
    if (strpos($key, 'MYSQL') !== false) {
        echo "{$key}: " . (strpos($key, 'PASSWORD') !== false ? '***' : $value) . "<br>";
    }
}
?>
