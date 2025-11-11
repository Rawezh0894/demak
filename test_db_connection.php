<?php
/**
 * Database Connection Test Script
 * This script tests MySQL server connection, database, username, and password
 */

// Load environment variables
require_once __DIR__ . '/config/env_loader.php';

echo "<h2>تاقیکردنەوەی پەیوەندی داتابەیس</h2>";
echo "<hr>";

$host = env('DB_HOST', 'localhost');
$db   = env('DB_NAME', 'u503479575_demak_db');
$user = env('DB_USER', 'u503479575_Rawezh_Jaza08');
$pass = env('DB_PASS', 'Rawezh.Jaza@0894');
$charset = env('DB_CHARSET', 'utf8mb4');
$port = env('DB_PORT', '3306');

echo "<h3>زانیاری پەیوەندی:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Port:</strong> $port</li>";
echo "<li><strong>Database:</strong> $db</li>";
echo "<li><strong>Username:</strong> $user</li>";
echo "<li><strong>Password:</strong> " . (empty($pass) ? '<span style="color:red;">هیچ</span>' : '<span style="color:green;">دروستە</span>') . "</li>";
echo "</ul>";
echo "<hr>";

// Test 1: Check if MySQL server is running
echo "<h3>تاقیکردنەوەی 1: پشکنینی MySQL سێرڤەر</h3>";
try {
    $dsn_no_db = "mysql:host=$host;port=$port;charset=$charset";
    $pdo_test = new PDO($dsn_no_db, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "<p style='color:green;'>✓ MySQL سێرڤەر کاردەکات</p>";
    echo "<p>MySQL Version: " . $pdo_test->query('SELECT VERSION()')->fetchColumn() . "</p>";
    $pdo_test = null;
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ MySQL سێرڤەر کارناکات</p>";
    echo "<p style='color:red;'>هەڵە: " . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<h3>کۆتایی</h3>";
    exit;
}
echo "<hr>";

// Test 2: Check if database exists
echo "<h3>تاقیکردنەوەی 2: پشکنینی ناوی داتابەیس</h3>";
try {
    $dsn_no_db = "mysql:host=$host;port=$port;charset=$charset";
    $pdo_test = new PDO($dsn_no_db, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    $stmt = $pdo_test->query("SHOW DATABASES LIKE '$db'");
    $database_exists = $stmt->fetch();
    
    if ($database_exists) {
        echo "<p style='color:green;'>✓ داتابەیس '$db' هەیە</p>";
    } else {
        echo "<p style='color:red;'>✗ داتابەیس '$db' نەدۆزرایەوە</p>";
        echo "<p>داتابەیسە بەردەستەکان:</p><ul>";
        $stmt = $pdo_test->query("SHOW DATABASES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    $pdo_test = null;
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ ناتوانرێت داتابەیسەکان بپشکنرێن</p>";
    echo "<p style='color:red;'>هەڵە: " . $e->getMessage() . "</p>";
}
echo "<hr>";

// Test 3: Check username and password
echo "<h3>تاقیکردنەوەی 3: پشکنینی ناوی بەکارهێنەر و تێپەڕەوشە</h3>";
try {
    $dsn_no_db = "mysql:host=$host;port=$port;charset=$charset";
    $pdo_test = new PDO($dsn_no_db, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    $stmt = $pdo_test->query("SELECT USER(), CURRENT_USER()");
    $user_info = $stmt->fetch(PDO::FETCH_NUM);
    echo "<p style='color:green;'>✓ ناوی بەکارهێنەر و تێپەڕەوشە دروستن</p>";
    echo "<p>بەکارهێنەری ئێستا: " . $user_info[0] . "</p>";
    echo "<p>بەکارهێنەری MySQL: " . $user_info[1] . "</p>";
    
    // Check privileges
    $stmt = $pdo_test->query("SHOW GRANTS FOR CURRENT_USER()");
    echo "<p><strong>دەسەڵاتەکان:</strong></p><ul>";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
    
    $pdo_test = null;
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ ناوی بەکارهێنەر یان تێپەڕەوشە هەڵەیە</p>";
    echo "<p style='color:red;'>هەڵە: " . $e->getMessage() . "</p>";
}
echo "<hr>";

// Test 4: Full connection test with database
echo "<h3>تاقیکردنەوەی 4: پەیوەندی تەواو لەگەڵ داتابەیس</h3>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5,
        PDO::ATTR_PERSISTENT          => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION wait_timeout=28800, interactive_timeout=28800",
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->query("SELECT 1");
    
    echo "<p style='color:green;'>✓ پەیوەندی بە سەرکەوتوویی دروست بوو</p>";
    
    // Get some database info
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $db_info = $stmt->fetch();
    echo "<p>داتابەیسی بەکارهاتوو: " . $db_info['db_name'] . "</p>";
    
    // List tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>خشتەکان (" . count($tables) . "):</strong></p>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:orange;'>ئاگاداری: هیچ خشتەیەک نەدۆزرایەوە</p>";
    }
    
    $pdo = null;
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ پەیوەندی ناکرێت</p>";
    echo "<p style='color:red;'>هەڵە: " . $e->getMessage() . "</p>";
}
echo "<hr>";

echo "<h3 style='color:green;'>تاقیکردنەوەکە تەواو بوو!</h3>";
?>

