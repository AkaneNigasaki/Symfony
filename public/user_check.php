<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$logFile = __DIR__ . '/user_check.log';
file_put_contents($logFile, "Checking for users...\n");

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

    $stmt = $pdo->query("SELECT id, email, name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        file_put_contents($logFile, "Found " . count($users) . " users:\n", FILE_APPEND);
        foreach ($users as $u) {
            file_put_contents($logFile, "- ID: {$u['id']}, Email: {$u['email']}, Name: {$u['name']}\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "No users found in the database.\n", FILE_APPEND);
    }
} catch (Exception $e) {
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
