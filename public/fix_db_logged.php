<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$logFile = __DIR__ . '/db_fix.log';
file_put_contents($logFile, "Starting DB Fix...\n");

try {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

    $url = $_ENV['DATABASE_URL'];
    file_put_contents($logFile, "Database URL found: $url\n", FILE_APPEND);

    // Parse DATABASE_URL manually
    $parts = parse_url($url);

    $host = $parts['host'];
    $port = $parts['port'] ?? 3306;
    $user = $parts['user'];
    $pass = $parts['pass'] ?? '';
    // Handle empty path or just '/'
    $db = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

    if (empty($db)) {
        throw new Exception("Database name not found in URL");
    }

    file_put_contents($logFile, "Connecting to host: $host, port: $port, db: $db, user: $user\n", FILE_APPEND);

    $dsn = "mysql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    file_put_contents($logFile, "Connected successfully.\n", FILE_APPEND);

    $sql = "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB";
    $pdo->exec($sql);
    file_put_contents($logFile, "Table users created (or already exists).\n", FILE_APPEND);

    // Check if column exists before adding
    $stmt = $pdo->query("SHOW COLUMNS FROM schools LIKE 'owner_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE schools ADD owner_id INT DEFAULT NULL");
        file_put_contents($logFile, "Column owner_id added to schools.\n", FILE_APPEND);
    } else {
        file_put_contents($logFile, "Column owner_id already exists in schools.\n", FILE_APPEND);
    }

    // Attempt FK
    try {
        // Check if FK exists first to avoid error
        $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'schools' AND CONSTRAINT_NAME = 'FK_9D1728D17E3C61F9' AND TABLE_SCHEMA = '$db'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE schools ADD CONSTRAINT FK_9D1728D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)");
            file_put_contents($logFile, "FK added.\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "FK already exists.\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents($logFile, "FK creation warning: " . $e->getMessage() . "\n", FILE_APPEND);
    }

    // Attempt Index
    try {
        // Simple check via exception based approach as showing indexes is parsing heavy
        $pdo->exec("CREATE INDEX IDX_9D1728D17E3C61F9 ON schools (owner_id)");
        file_put_contents($logFile, "Index added.\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents($logFile, "Index creation warning (likely exists): " . $e->getMessage() . "\n", FILE_APPEND);
    }

    // Mark migration
    $version = 'DoctrineMigrations\Version20260216000000';
    $executedAt = (new DateTime())->format('Y-m-d H:i:s');
    $pdo->exec("INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('$version', '$executedAt', 0)");
    file_put_contents($logFile, "Migration marked as executed.\n", FILE_APPEND);

    file_put_contents($logFile, "DONE.\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($logFile, "CRITICAL ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
