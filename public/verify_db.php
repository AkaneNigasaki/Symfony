<?php
// ... (previous setup code) ...
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
$url = $_ENV['DATABASE_URL'];
$parts = parse_url($url);
$host = $parts['host'];
$port = $parts['port'] ?? 3306;
$user = $parts['user'];
$pass = $parts['pass'] ?? '';
$db = ltrim($parts['path'], '/');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Table 'users' exists.\n";
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Columns: " . implode(", ", $columns) . "\n";
    } else {
        echo "Table 'users' does NOT exist.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
