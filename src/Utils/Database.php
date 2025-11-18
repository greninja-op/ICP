<?php

namespace App\Utils;

use PDO;
use PDOException;
use Exception;

/**
 * Database Connection Class
 * 
 * Provides PDO connection pooling and prepared statement wrapper methods
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private array $config;
    private string $logPath;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->loadConfig();
        $this->logPath = $this->getBasePath() . '/logs/database.log';
        $this->ensureLogDirectory();
    }

    /**
     * Get singleton instance (connection pooling)
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Load database configuration
     */
    private function loadConfig(): void
    {
        $configPath = $this->getBasePath() . '/config/database.php';
        
        if (!file_exists($configPath)) {
            throw new Exception("Database configuration file not found");
        }

        $dbConfig = require $configPath;
        $this->config = $dbConfig['connections'][$dbConfig['default']];
    }

    /**
     * Get base path of the application
     */
    private function getBasePath(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * Ensure log directory exists
     */
    private function ensureLogDirectory(): void
    {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Establish database connection
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['driver'],
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );

        } catch (PDOException $e) {
            $this->logError('Connection failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new Exception("Database connection failed. Please check your configuration.");
        }
    }

    /**
     * Execute a SELECT query with prepared statements
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array Query results
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError('Query failed', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw new Exception("Database query failed");
        }
    }

    /**
     * Execute a SELECT query and return a single row
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return array|null Single row or null
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            $this->logError('Query failed', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw new Exception("Database query failed");
        }
    }

    /**
     * Execute an INSERT, UPDATE, or DELETE query
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return int Number of affected rows
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError('Execute failed', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw new Exception("Database execute failed");
        }
    }

    /**
     * Execute an INSERT query and return the last inserted ID
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return string Last inserted ID
     */
    public function insert(string $sql, array $params = []): string
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $stmt->execute();
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logError('Insert failed', [
                'sql' => $sql,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw new Exception("Database insert failed");
        }
    }

    /**
     * Prepare a statement with parameter binding
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return \PDOStatement Prepared statement
     */
    private function prepare(string $sql, array $params = []): \PDOStatement
    {
        $connection = $this->getConnection();
        $stmt = $connection->prepare($sql);

        foreach ($params as $key => $value) {
            $paramKey = is_int($key) ? $key + 1 : $key;
            $paramType = $this->getParamType($value);
            $stmt->bindValue($paramKey, $value, $paramType);
        }

        return $stmt;
    }

    /**
     * Determine PDO parameter type
     * 
     * @param mixed $value Value to check
     * @return int PDO parameter type constant
     */
    private function getParamType($value): int
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Check if currently in a transaction
     */
    public function inTransaction(): bool
    {
        return $this->getConnection()->inTransaction();
    }

    /**
     * Log database errors
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    private function logError(string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'] ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] [ERROR] [user:%s] [ip:%s] %s %s\n",
            $timestamp,
            $userId,
            $ip,
            $message,
            json_encode($context)
        );

        file_put_contents($this->logPath, $logEntry, FILE_APPEND);
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

