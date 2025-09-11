<?php
echo "=== DEBUG RAILWAY ===<br><br>";

echo "1. PHP Version: " . phpversion() . "<br>";
echo "2. Current Directory: " . getcwd() . "<br>";
echo "3. File exists check:<br>";

$files = [
    'vendor/autoload.php',
    'app/Controllers/QuestionController.php',
    'app/Controllers/AdminController.php',
    'app/Services/QuestionService.php',
    'app/Support/Database.php'
];

foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/../' . $file);
    echo "   - {$file}: " . ($exists ? "✅" : "❌") . "<br>";
}

echo "<br>4. Testing autoloader:<br>";
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "   - Autoloader: ✅<br>";
} catch (Exception $e) {
    echo "   - Autoloader: ❌ " . $e->getMessage() . "<br>";
}

echo "<br>5. Testing classes:<br>";
try {
    if (class_exists('App\\Controllers\\QuestionController')) {
        echo "   - QuestionController: ✅<br>";
    } else {
        echo "   - QuestionController: ❌<br>";
    }
} catch (Exception $e) {
    echo "   - QuestionController: ❌ " . $e->getMessage() . "<br>";
}

echo "<br>6. Testing database connection:<br>";
try {
    $host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
    $port = getenv('MYSQLPORT') ?: '3306';
    $dbname = getenv('MYSQLDATABASE') ?: 'railway';
    $user = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname}", $user, $password);
    echo "   - Database: ✅<br>";
} catch (Exception $e) {
    echo "   - Database: ❌ " . $e->getMessage() . "<br>";
}

echo "<br>7. Testing router:<br>";
try {
    $router = new \Pecee\SimpleRouter\SimpleRouter();
    echo "   - Router: ✅<br>";
} catch (Exception $e) {
    echo "   - Router: ❌ " . $e->getMessage() . "<br>";
}

echo "<br>=== END DEBUG ===";
?>
