<?php

/**
 * Setup Verification Script
 * 
 * Run this script to verify that all requirements are met
 * Usage: php verify-setup.php
 */

echo "=== University Portal - Setup Verification ===\n\n";

$errors = [];
$warnings = [];

// Check PHP version
echo "Checking PHP version... ";
if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
    echo "✓ PHP " . PHP_VERSION . "\n";
} else {
    echo "✗ FAILED\n";
    $errors[] = "PHP 8.1 or higher is required. Current version: " . PHP_VERSION;
}

// Check required PHP extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'curl'];
echo "\nChecking PHP extensions:\n";
foreach ($requiredExtensions as $ext) {
    echo "  - $ext... ";
    if (extension_loaded($ext)) {
        echo "✓\n";
    } else {
        echo "✗ MISSING\n";
        $errors[] = "Required PHP extension '$ext' is not loaded";
    }
}

// Check Composer
echo "\nChecking Composer... ";
exec('composer --version 2>&1', $output, $returnCode);
if ($returnCode === 0) {
    echo "✓ Installed\n";
} else {
    echo "✗ NOT FOUND\n";
    $warnings[] = "Composer is not installed or not in PATH. Install from https://getcomposer.org/";
}

// Check vendor directory
echo "\nChecking dependencies... ";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✓ Installed\n";
} else {
    echo "✗ NOT INSTALLED\n";
    $warnings[] = "PHP dependencies not installed. Run: composer install";
}

// Check .env file
echo "\nChecking environment configuration... ";
if (file_exists(__DIR__ . '/.env')) {
    echo "✓ .env file exists\n";
} else {
    echo "⚠ .env file not found\n";
    $warnings[] = ".env file not found. Copy .env.example to .env and configure it";
}

// Check directory permissions
echo "\nChecking directory permissions:\n";
$writableDirs = ['public/uploads', 'logs'];
foreach ($writableDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    echo "  - $dir... ";
    
    if (!file_exists($path)) {
        @mkdir($path, 0755, true);
    }
    
    if (is_writable($path)) {
        echo "✓ Writable\n";
    } else {
        echo "✗ NOT WRITABLE\n";
        $warnings[] = "Directory '$dir' is not writable. Run: chmod -R 755 $dir";
    }
}

// Check MySQL connection (if .env exists)
if (file_exists(__DIR__ . '/.env')) {
    echo "\nChecking database connection... ";
    
    // Load .env
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
    }
    
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Connected to database '$database'\n";
        } else {
            echo "⚠ Connected to MySQL, but database '$database' does not exist\n";
            $warnings[] = "Database '$database' not found. Create it with: CREATE DATABASE $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
        }
    } catch (PDOException $e) {
        echo "✗ FAILED\n";
        $warnings[] = "Cannot connect to MySQL: " . $e->getMessage();
    }
}

// Summary
echo "\n=== Summary ===\n";

if (count($errors) > 0) {
    echo "\n❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $i => $error) {
        echo ($i + 1) . ". $error\n";
    }
}

if (count($warnings) > 0) {
    echo "\n⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $i => $warning) {
        echo ($i + 1) . ". $warning\n";
    }
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "\n✅ All checks passed! Your environment is ready.\n";
    echo "\nNext steps:\n";
    echo "1. Start the development server: php -S localhost:8000 -t public\n";
    echo "2. Visit: http://localhost:8000\n";
    echo "3. Continue with database migrations (Task 2)\n";
} elseif (count($errors) === 0) {
    echo "\n⚠️  Setup is mostly complete, but there are some warnings to address.\n";
} else {
    echo "\n❌ Setup is incomplete. Please fix the errors above.\n";
    exit(1);
}

echo "\n";
