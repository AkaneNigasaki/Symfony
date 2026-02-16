<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$logFile = __DIR__ . '/add_user_direct.log';
file_put_contents($logFile, "Starting direct user addition...\n");

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

    $email = 'test@example.com';
    $password = 'password123';

    // We need to hash the password exactly like Symfony would do with 'auto' (BCrypt or Argon2)
    // password_hash() with default options is compatible with Symfony's 'auto'
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() == 0) {
        $stmtInsert = $pdo->prepare("INSERT INTO users (email, roles, password, name) VALUES (?, ?, ?, ?)");
        $stmtInsert->execute([$email, '["ROLE_USER"]', $hashedPassword, 'Test User']);
        file_put_contents($logFile, "SUCCESS: User $email created.\n", FILE_APPEND);
    } else {
        file_put_contents($logFile, "User $email already exists.\n", FILE_APPEND);
    }
} catch (Exception $e) {
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
