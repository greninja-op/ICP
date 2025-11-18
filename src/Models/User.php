<?php

namespace App\Models;

/**
 * User Model
 * 
 * Handles user data and authentication-related operations
 */
class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'username',
        'email',
        'password_hash',
        'role',
        'full_name',
        'phone',
        'profile_image',
        'is_active',
        'last_login'
    ];
    
    protected array $hidden = [
        'password_hash'
    ];

    /**
     * Find a user by username
     * 
     * @param string $username Username to search for
     * @return array|null User data or null
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1";
        $result = $this->db->queryOne($sql, [$username]);
        
        if ($result) {
            // Don't hide password_hash for authentication purposes
            return $result;
        }
        
        return null;
    }

    /**
     * Find a user by email
     * 
     * @param string $email Email to search for
     * @return array|null User data or null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $result = $this->db->queryOne($sql, [$email]);
        
        if ($result) {
            return $this->hideAttributes($result);
        }
        
        return null;
    }

    /**
     * Find a user by username and role
     * 
     * @param string $username Username to search for
     * @param string $role User role
     * @return array|null User data or null
     */
    public function findByUsernameAndRole(string $username, string $role): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? AND role = ? LIMIT 1";
        $result = $this->db->queryOne($sql, [$username, $role]);
        
        if ($result) {
            // Don't hide password_hash for authentication purposes
            return $result;
        }
        
        return null;
    }

    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return string Last inserted ID
     */
    public function create(array $data): string
    {
        // Validate required fields
        $errors = $this->validateRequired($data, ['username', 'email', 'password_hash', 'role', 'full_name']);
        
        if (!empty($errors)) {
            throw new \Exception("Validation failed: " . implode(', ', $errors));
        }

        // Validate email format
        if (!$this->validateEmail($data['email'])) {
            throw new \Exception("Invalid email format");
        }

        // Validate username length
        if (!$this->validateLength($data['username'], 3, 50)) {
            throw new \Exception("Username must be between 3 and 50 characters");
        }

        // Validate role
        $validRoles = ['student', 'teacher', 'admin'];
        if (!in_array($data['role'], $validRoles)) {
            throw new \Exception("Invalid role. Must be one of: " . implode(', ', $validRoles));
        }

        // Check if username already exists
        if ($this->exists(['username' => $data['username']])) {
            throw new \Exception("Username already exists");
        }

        // Check if email already exists
        if ($this->exists(['email' => $data['email']])) {
            throw new \Exception("Email already exists");
        }

        // Set default values
        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }

        return parent::create($data);
    }

    /**
     * Update a user
     * 
     * @param int $id User ID
     * @param array $data Data to update
     * @return int Number of affected rows
     */
    public function update(int $id, array $data): int
    {
        // Validate email if provided
        if (isset($data['email']) && !$this->validateEmail($data['email'])) {
            throw new \Exception("Invalid email format");
        }

        // Validate username length if provided
        if (isset($data['username']) && !$this->validateLength($data['username'], 3, 50)) {
            throw new \Exception("Username must be between 3 and 50 characters");
        }

        // Validate role if provided
        if (isset($data['role'])) {
            $validRoles = ['student', 'teacher', 'admin'];
            if (!in_array($data['role'], $validRoles)) {
                throw new \Exception("Invalid role. Must be one of: " . implode(', ', $validRoles));
            }
        }

        // Check if username already exists (excluding current user)
        if (isset($data['username'])) {
            $existing = $this->findByUsername($data['username']);
            if ($existing && $existing['id'] != $id) {
                throw new \Exception("Username already exists");
            }
        }

        // Check if email already exists (excluding current user)
        if (isset($data['email'])) {
            $existing = $this->findByEmail($data['email']);
            if ($existing && $existing['id'] != $id) {
                throw new \Exception("Email already exists");
            }
        }

        return parent::update($id, $data);
    }

    /**
     * Update last login timestamp
     * 
     * @param int $id User ID
     * @return int Number of affected rows
     */
    public function updateLastLogin(int $id): int
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Get active users by role
     * 
     * @param string $role User role
     * @return array Array of users
     */
    public function getActiveByRole(string $role): array
    {
        return $this->findAll([
            'role' => $role,
            'is_active' => 1
        ], 'full_name ASC');
    }

    /**
     * Deactivate a user
     * 
     * @param int $id User ID
     * @return int Number of affected rows
     */
    public function deactivate(int $id): int
    {
        return $this->update($id, ['is_active' => 0]);
    }

    /**
     * Activate a user
     * 
     * @param int $id User ID
     * @return int Number of affected rows
     */
    public function activate(int $id): int
    {
        return $this->update($id, ['is_active' => 1]);
    }

    /**
     * Search users by name or email
     * 
     * @param string $query Search query
     * @param string|null $role Filter by role
     * @return array Array of users
     */
    public function search(string $query, ?string $role = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE (full_name LIKE ? OR email LIKE ? OR username LIKE ?)";
        $params = ["%$query%", "%$query%", "%$query%"];

        if ($role !== null) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        $sql .= " ORDER BY full_name ASC";

        $results = $this->db->query($sql, $params);
        return array_map([$this, 'hideAttributes'], $results);
    }
}

