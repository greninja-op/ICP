<?php

namespace App\Models;

use App\Utils\Database;
use Exception;

/**
 * Base Model Class
 * 
 * Provides CRUD operations and query builder methods for all models
 */
abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $attributes = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a record by ID
     * 
     * @param int $id Record ID
     * @return array|null Record data or null
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        $result = $this->db->queryOne($sql, [$id]);
        
        if ($result) {
            return $this->hideAttributes($result);
        }
        
        return null;
    }

    /**
     * Find all records
     * 
     * @param array $conditions WHERE conditions
     * @param string $orderBy ORDER BY clause
     * @param int|null $limit LIMIT clause
     * @return array Array of records
     */
    public function findAll(array $conditions = [], string $orderBy = '', ?int $limit = null): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }

        $results = $this->db->query($sql, $params);
        
        return array_map([$this, 'hideAttributes'], $results);
    }

    /**
     * Find a single record by conditions
     * 
     * @param array $conditions WHERE conditions
     * @return array|null Record data or null
     */
    public function findOne(array $conditions): ?array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " LIMIT 1";

        $result = $this->db->queryOne($sql, $params);
        
        if ($result) {
            return $this->hideAttributes($result);
        }
        
        return null;
    }

    /**
     * Create a new record
     * 
     * @param array $data Record data
     * @return string Last inserted ID
     */
    public function create(array $data): string
    {
        $data = $this->filterFillable($data);
        
        if (empty($data)) {
            throw new Exception("No fillable fields provided");
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        return $this->db->insert($sql, array_values($data));
    }

    /**
     * Update a record by ID
     * 
     * @param int $id Record ID
     * @param array $data Data to update
     * @return int Number of affected rows
     */
    public function update(int $id, array $data): int
    {
        $data = $this->filterFillable($data);
        
        if (empty($data)) {
            throw new Exception("No fillable fields provided");
        }

        $setClauses = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClauses[] = "$column = ?";
            $params[] = $value;
        }
        
        $params[] = $id;

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $setClauses),
            $this->primaryKey
        );

        return $this->db->execute($sql, $params);
    }

    /**
     * Delete a record by ID
     * 
     * @param int $id Record ID
     * @return int Number of affected rows
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Count records
     * 
     * @param array $conditions WHERE conditions
     * @return int Record count
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $result = $this->db->queryOne($sql, $params);
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Check if a record exists
     * 
     * @param array $conditions WHERE conditions
     * @return bool True if exists
     */
    public function exists(array $conditions): bool
    {
        return $this->count($conditions) > 0;
    }

    /**
     * Execute a raw query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array Query results
     */
    public function raw(string $sql, array $params = []): array
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Filter data to only include fillable fields
     * 
     * @param array $data Input data
     * @return array Filtered data
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Hide specified attributes from result
     * 
     * @param array $data Record data
     * @return array Filtered data
     */
    protected function hideAttributes(array $data): array
    {
        if (empty($this->hidden)) {
            return $data;
        }

        foreach ($this->hidden as $attribute) {
            unset($data[$attribute]);
        }

        return $data;
    }

    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $required Required fields
     * @return array Validation errors
     */
    protected function validateRequired(array $data, array $required): array
    {
        $errors = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = ucfirst($field) . " is required";
            }
        }

        return $errors;
    }

    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @return bool True if valid
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate field length
     * 
     * @param string $value Value to validate
     * @param int $min Minimum length
     * @param int|null $max Maximum length
     * @return bool True if valid
     */
    protected function validateLength(string $value, int $min, ?int $max = null): bool
    {
        $length = strlen($value);
        
        if ($length < $min) {
            return false;
        }
        
        if ($max !== null && $length > $max) {
            return false;
        }
        
        return true;
    }

    /**
     * Begin a database transaction
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit a database transaction
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback a database transaction
     */
    public function rollback(): bool
    {
        return $this->db->rollback();
    }
}

