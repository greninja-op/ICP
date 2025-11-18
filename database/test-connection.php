<?php
/**
 * Database Connection Test
 * 
 * This script tests the database connection and displays configuration.
 * Usage: php database/test-connection.php
 */

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Load database configuration
$dbConfig = require __DIR__ . '/../config/database.php';
$config = $dbConfig['connections'][$dbConfig['default']];

echo "Database Connection Test\n";
echo str_repeat("=", 50) . "\n\n";

echo "Configuration:\n";
echo "  Driver:   " . $config['driver'] . "\n";
echo "  Host:     " . $config['host'] . "\n";
echo "  Port:     " . $config['port'] . "\n";
echo "  Database: " . $config['database'] . "\n";
echo "  Username: " . $config['username'] . "\n";
echo "  Charset:  " . $config['charset'] . "\n\n";

// Test connection
try {
    $dsn = sprintf(
        "%s:host=%s;port=%s;charset=%s",
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['charset']
    );
    
    echo "Testing connection to MySQL server...\n";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    echo "✓ Connected to MySQL server successfully!\n\n";
    
    // Check if database exists
    echo "Checking if database exists...\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . $config['database'] . "'");
    $dbExists = $stmt->rowCount() > 0;
    
    if ($dbExists) {
        echo "✓ Database '" . $config['database'] . "' exists!\n\n";
        
        // Connect to the database
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );
        $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        
        // Check for tables
        echo "Checking for tables...\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "✓ Found " . count($tables) . " table(s):\n";
            foreach ($tables as $table) {
                echo "  - $table\n";
            }
        } else {
            echo "⚠ No tables found. Run migrations to create tables.\n";
        }
    } else {
        echo "✗ Database '" . $config['database'] . "' does not exist!\n";
        echo "\nTo create the database, run:\n";
        echo "  mysql -u " . $config['username'] . " -p -e \"CREATE DATABASE " . $config['database'] . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Connection test completed!\n";
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "  1. MySQL service is running\n";
    echo "  2. Database credentials in .env are correct\n";
    echo "  3. User has necessary permissions\n";
    exit(1);
}
