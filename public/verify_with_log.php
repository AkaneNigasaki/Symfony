<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$logFile = __DIR__ . '/db_verification.log';
file_put_contents($logFile, "Starting verification...\n");

try {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
    $url = $_ENV['DATABASE_URL'];

    $parts = parse_url($url);
    $host = $parts['host'];
    $port = $parts['port'] ?? 3306;
    $user = $parts['user'];
    $pass = $parts['pass'] ?? '';
    $db = ltrim($parts['path'], '/');

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        file_put_contents($logFile, "SUCCESS: Table 'users' exists.\n", FILE_APPEND);

        $stmt = $pdo->query("SHOW COLUMNS FROM schools LIKE 'owner_id'");
        if ($stmt->rowCount() > 0) {
            file_put_contents($logFile, "SUCCESS: Column 'owner_id' in 'schools' exists.\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "FAILURE: Column 'owner_id' in 'schools' MISSING.\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "FAILURE: Table 'users' does NOT exist.\n", FILE_APPEND);
    }
} catch (Exception $e) {
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
