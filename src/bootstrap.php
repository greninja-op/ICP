<?php

/**
 * Application Bootstrap
 * 
 * Initializes the application environment
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Skip lines without =
        if (strpos($line, '=') === false) {
            continue;
        }
        
        // Parse and set environment variable
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes from value
        $value = trim($value, '"\'');
        
        // Set environment variable
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}

// Set timezone
$timezone = getenv('APP_TIMEZONE') ?: 'UTC';
date_default_timezone_set($timezone);

// Set error reporting based on environment
$appEnv = getenv('APP_ENV') ?: 'production';
$appDebug = getenv('APP_DEBUG') === 'true';

if ($appEnv === 'development' || $appDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

// Set up error handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set up exception handler
set_exception_handler(function ($exception) {
    $logPath = __DIR__ . '/../logs/exceptions.log';
    $logDir = dirname($logPath);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $message = $exception->getMessage();
    $file = $exception->getFile();
    $line = $exception->getLine();
    $trace = $exception->getTraceAsString();
    
    $logEntry = sprintf(
        "[%s] %s in %s:%d\nStack trace:\n%s\n\n",
        $timestamp,
        $message,
        $file,
        $line,
        $trace
    );
    
    file_put_contents($logPath, $logEntry, FILE_APPEND);
    
    // Show error page
    http_response_code(500);
    
    if (getenv('APP_DEBUG') === 'true') {
        echo "<h1>Application Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($message) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($file) . "</p>";
        echo "<p><strong>Line:</strong> " . htmlspecialchars($line) . "</p>";
        echo "<pre>" . htmlspecialchars($trace) . "</pre>";
    } else {
        echo "<h1>500 - Internal Server Error</h1>";
        echo "<p>An error occurred. Please try again later.</p>";
    }
    
    exit(1);
});

// Return true to indicate successful bootstrap
return true;

