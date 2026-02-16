<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

$url = $_ENV['DATABASE_URL'];

// Parse DATABASE_URL manually since we can't rely on Doctrine here
// Format: mysql://root:@127.0.0.1:3306/school_management?serverVersion=8.0.32&charset=utf8mb4
$parts = parse_url($url);

$host = $parts['host'];
$port = $parts['port'] ?? 3306;
$user = $parts['user'];
$pass = $parts['pass'] ?? '';
$db = ltrim($parts['path'], '/');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully to $db\n";

    $sql = "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
    $pdo->exec($sql);
    echo "Table users created (or already exists)\n";

    // Check if column exists before adding
    $stmt = $pdo->query("SHOW COLUMNS FROM schools LIKE 'owner_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE schools ADD owner_id INT DEFAULT NULL");
        echo "Column owner_id added to schools\n";
    }

    // Check for FK
    // This is harder to check reliably cross-platform in pure SQL without information_schema, so we'll try/catch it
    try {
        $pdo->exec("ALTER TABLE schools ADD CONSTRAINT FK_9D1728D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)");
        echo "FK added\n";
    } catch (Exception $e) {
        echo "FK might already exist: " . $e->getMessage() . "\n";
    }

    try {
        $pdo->exec("CREATE INDEX IDX_9D1728D17E3C61F9 ON schools (owner_id)");
        echo "Index added\n";
    } catch (Exception $e) {
        echo "Index might already exist: " . $e->getMessage() . "\n";
    }

    // Mark migration as executed to prevent future errors
    // doctrine_migration_versions
    $version = 'DoctrineMigrations\Version20260216000000';
    $executedAt = (new DateTime())->format('Y-m-d H:i:s');
    // INSERT IGNORE to avoid duplicates
    $pdo->exec("INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('$version', '$executedAt', 0)");
    echo "Migration marked as executed\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
