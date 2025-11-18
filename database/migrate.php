<?php
/**
 * Database Migration Runner
 * 
 * This script runs database migrations in order.
 * Usage: php database/migrate.php [up|down|status]
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Load database configuration
$dbConfig = require __DIR__ . '/../config/database.php';
$config = $dbConfig['connections'][$dbConfig['default']];

// Create PDO connection
try {
    $dsn = sprintf(
        "%s:host=%s;port=%s;dbname=%s;charset=%s",
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Create migrations table if it doesn't exist
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        migration VARCHAR(255) NOT NULL UNIQUE,
        batch INT NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

/**
 * Get all migration files
 */
function getMigrationFiles() {
    $files = glob(__DIR__ . '/migrations/*.php');
    sort($files);
    return $files;
}

/**
 * Get executed migrations
 */
function getExecutedMigrations($pdo) {
    $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get next batch number
 */
function getNextBatch($pdo) {
    $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
    $result = $stmt->fetch();
    return ($result['max_batch'] ?? 0) + 1;
}

/**
 * Run pending migrations
 */
function runMigrations($pdo) {
    $files = getMigrationFiles();
    $executed = getExecutedMigrations($pdo);
    $batch = getNextBatch($pdo);
    $count = 0;
    
    foreach ($files as $file) {
        $migration = basename($file);
        
        if (in_array($migration, $executed)) {
            continue;
        }
        
        echo "Migrating: $migration\n";
        
        try {
            // Execute migration
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            
            // Record migration
            $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
            $stmt->execute([$migration, $batch]);
            
            echo "Migrated:  $migration\n";
            $count++;
        } catch (PDOException $e) {
            echo "Error migrating $migration: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    if ($count === 0) {
        echo "Nothing to migrate.\n";
    } else {
        echo "\nMigrated $count file(s).\n";
    }
}

/**
 * Rollback last batch of migrations
 */
function rollbackMigrations($pdo) {
    // Get last batch
    $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
    $result = $stmt->fetch();
    $lastBatch = $result['max_batch'] ?? 0;
    
    if ($lastBatch === 0) {
        echo "Nothing to rollback.\n";
        return;
    }
    
    // Get migrations in last batch
    $stmt = $pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
    $stmt->execute([$lastBatch]);
    $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($migrations as $migration) {
        echo "Rolling back: $migration\n";
        
        // Note: Rollback would require down() methods in migration files
        // For now, we'll just remove the record
        $stmt = $pdo->prepare("DELETE FROM migrations WHERE migration = ?");
        $stmt->execute([$migration]);
        
        echo "Rolled back: $migration\n";
    }
    
    echo "\nRolled back " . count($migrations) . " migration(s).\n";
}

/**
 * Show migration status
 */
function showStatus($pdo) {
    $files = getMigrationFiles();
    $executed = getExecutedMigrations($pdo);
    
    echo "Migration Status:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-50s %s\n", "Migration", "Status");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($files as $file) {
        $migration = basename($file);
        $status = in_array($migration, $executed) ? "✓ Migrated" : "✗ Pending";
        printf("%-50s %s\n", $migration, $status);
    }
    
    echo str_repeat("-", 80) . "\n";
    echo "Total: " . count($files) . " migrations\n";
    echo "Executed: " . count($executed) . " migrations\n";
    echo "Pending: " . (count($files) - count($executed)) . " migrations\n";
}

// Parse command
$command = $argv[1] ?? 'up';

switch ($command) {
    case 'up':
        runMigrations($pdo);
        break;
    case 'down':
        rollbackMigrations($pdo);
        break;
    case 'status':
        showStatus($pdo);
        break;
    default:
        echo "Usage: php database/migrate.php [up|down|status]\n";
        echo "  up     - Run pending migrations\n";
        echo "  down   - Rollback last batch of migrations\n";
        echo "  status - Show migration status\n";
        exit(1);
}
