<?php
// Load environment variables
require_once __DIR__ . '/env_loader.php';
require_once __DIR__ . '/timezone_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ماوەی بەسەرچوونی سێشن (بە چرکە) - 24 کاتژمێر = 86400
$session_timeout = env('SESSION_TIMEOUT', 86400);

// هەرکات session هاتەوە، کاتی دوا جووڵەی بەکارهێنەر نوێ بکەوە
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    // سێشن بەسەرچووە
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();



$host = env('DB_HOST', 'localhost');
$db   = env('DB_NAME', 'u503479575_demak_db');
$user = env('DB_USER', 'u503479575_Rawezh_Jaza08');
$pass = env('DB_PASS', 'Rawezh.Jaza@0894');
$charset = env('DB_CHARSET', 'utf8mb4');
$port = env('DB_PORT', '3306');

// Connection timeout settings (in seconds)
$connection_timeout = env('DB_CONNECTION_TIMEOUT', 30);
$query_timeout = env('DB_QUERY_TIMEOUT', 300);

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => $connection_timeout,
    PDO::ATTR_PERSISTENT          => false, // Don't use persistent connections to avoid "gone away" issues
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION wait_timeout=28800, interactive_timeout=28800, max_allowed_packet=67108864",
];

// Function to create database connection with retry logic
function createDatabaseConnection($dsn, $user, $pass, $options, $max_retries = 3) {
    $attempt = 0;
    $last_error = null;
    
    while ($attempt < $max_retries) {
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            
            // Test the connection
            $pdo->query("SELECT 1");
            
            // Set timezone for MySQL connection
            setMySQLTimezone($pdo);
            
            return $pdo;
            
        } catch (PDOException $e) {
            $last_error = $e;
            $attempt++;
            
            // If it's a "gone away" error, wait a bit before retrying
            if (strpos($e->getMessage(), 'gone away') !== false || strpos($e->getMessage(), '2006') !== false) {
                if ($attempt < $max_retries) {
                    // Wait 1 second before retrying
                    usleep(1000000); // 1 second in microseconds
                    continue;
                }
            }
            
            // For other errors, break immediately
            break;
        }
    }
    
    // If all retries failed, throw the last error
    throw $last_error;
}

try {
    $pdo = createDatabaseConnection($dsn, $user, $pass, $options);
    
} catch (PDOException $e) {
    // بڵاوکردنەوەی هەڵەی ڕاستی PDO بۆ تاقیکردنەوە
    error_log("Database connection error: " . $e->getMessage());
    die("DB ERROR: " . $e->getMessage() . "<br><br>تکایە دڵنیا ببەوە:<br>1. MySQL سێرڤەر کاردەکات<br>2. ناوی داتابەیس دروستە<br>3. ناوی بەکارهێنەر و تێپەڕەوشە دروستن");
}

/**
 * Helper function to check and reconnect if connection is lost
 * Returns new PDO connection if reconnection is needed
 */
function ensureConnection(&$pdo) {
    try {
        $pdo->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'gone away') !== false || strpos($e->getMessage(), '2006') !== false) {
            // Connection lost, try to reconnect
            global $dsn, $user, $pass, $options;
            try {
                $pdo = createDatabaseConnection($dsn, $user, $pass, $options);
                return true;
            } catch (PDOException $reconnect_error) {
                error_log("Reconnection failed: " . $reconnect_error->getMessage());
                return false;
            }
        }
        return false;
    }
}

/**
 * Helper function to create detailed notifications
 */
function createDetailedNotification($pdo, $user_id, $action, $table_name, $record_id, $description, $old_values = null, $new_values = null, $additional_info = null, $ip_address = null) {
    try {
        // Ensure connection is alive
        ensureConnection($pdo);
        
        $sql = "INSERT INTO notifications (user_id, action, table_name, record_id, description, old_values, new_values, additional_info, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id,
            $action,
            $table_name,
            $record_id,
            $description,
            $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null,
            $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null,
            $additional_info ? json_encode($additional_info, JSON_UNESCAPED_UNICODE) : null,
            $ip_address ?: $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Helper function to get user IP address
 */
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
?>
