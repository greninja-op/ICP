<?php

namespace App\Services;

use App\Models\User;
use Exception;

/**
 * Authentication Service
 * 
 * Handles user authentication, password hashing, and session management
 */
class AuthService
{
    private User $userModel;
    private int $sessionLifetime;
    private int $bcryptCost;

    public function __construct()
    {
        $this->userModel = new User();
        $this->sessionLifetime = (int) (getenv('SESSION_LIFETIME') ?: 30); // minutes
        $this->bcryptCost = (int) (getenv('PASSWORD_BCRYPT_COST') ?: 12);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            $this->startSession();
        }
    }

    /**
     * Start a secure session
     */
    private function startSession(): void
    {
        $sessionName = 'UNIVERSITY_PORTAL_SESSION';
        $secure = getenv('SESSION_SECURE_COOKIE') === 'true';
        $httponly = true;
        $samesite = 'Lax';

        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => $this->sessionLifetime * 60,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ]);

        session_name($sessionName);
        session_start();

        // Regenerate session ID periodically for security
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }

    /**
     * Authenticate a user
     * 
     * @param string $username Username
     * @param string $password Password
     * @param string $role User role
     * @return array Authentication result
     */
    public function login(string $username, string $password, string $role): array
    {
        try {
            // Validate inputs
            if (empty($username) || empty($password) || empty($role)) {
                return [
                    'success' => false,
                    'message' => 'All fields are required',
                    'errors' => [
                        'username' => empty($username) ? 'Username is required' : null,
                        'password' => empty($password) ? 'Password is required' : null,
                        'role' => empty($role) ? 'Role is required' : null,
                    ]
                ];
            }

            // Find user by username and role
            $user = $this->userModel->findByUsernameAndRole($username, $role);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => []
                ];
            }

            // Check if user is active
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => 'Account is inactive. Please contact administrator.',
                    'errors' => []
                ];
            }

            // Verify password
            if (!$this->verifyPassword($password, $user['password_hash'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => []
                ];
            }

            // Create session
            $this->createSession($user);

            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => $user['full_name']
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during login',
                'errors' => []
            ];
        }
    }

    /**
     * Create a session for authenticated user
     * 
     * @param array $user User data
     */
    private function createSession(array $user): void
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['authenticated'] = true;
        $_SESSION['last_activity'] = time();
        $_SESSION['created'] = time();

        // Get additional role-specific data
        if ($user['role'] === 'student') {
            $this->setStudentSessionData($user['id']);
        } elseif ($user['role'] === 'teacher') {
            $this->setTeacherSessionData($user['id']);
        }
    }

    /**
     * Set student-specific session data
     * 
     * @param int $userId User ID
     */
    private function setStudentSessionData(int $userId): void
    {
        $sql = "SELECT department, semester, student_id FROM students WHERE user_id = ? LIMIT 1";
        $student = $this->userModel->raw($sql, [$userId]);
        
        if (!empty($student)) {
            $_SESSION['department'] = $student[0]['department'];
            $_SESSION['semester'] = $student[0]['semester'];
            $_SESSION['student_id'] = $student[0]['student_id'];
        }
    }

    /**
     * Set teacher-specific session data
     * 
     * @param int $userId User ID
     */
    private function setTeacherSessionData(int $userId): void
    {
        $sql = "SELECT department, teacher_id FROM teachers WHERE user_id = ? LIMIT 1";
        $teacher = $this->userModel->raw($sql, [$userId]);
        
        if (!empty($teacher)) {
            $_SESSION['department'] = $teacher[0]['department'];
            $_SESSION['teacher_id'] = $teacher[0]['teacher_id'];
        }
    }

    /**
     * Log out the current user
     * 
     * @return array Logout result
     */
    public function logout(): array
    {
        // Destroy session data
        $_SESSION = [];

        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool True if authenticated
     */
    public function isAuthenticated(): bool
    {
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            return false;
        }

        // Check session timeout
        if ($this->isSessionExpired()) {
            $this->logout();
            return false;
        }

        // Update last activity
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Check if session has expired
     * 
     * @return bool True if expired
     */
    private function isSessionExpired(): bool
    {
        if (!isset($_SESSION['last_activity'])) {
            return true;
        }

        $inactiveTime = time() - $_SESSION['last_activity'];
        $maxInactiveTime = $this->sessionLifetime * 60; // Convert to seconds

        return $inactiveTime > $maxInactiveTime;
    }

    /**
     * Get current authenticated user
     * 
     * @return array|null User data or null
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'full_name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
            'department' => $_SESSION['department'] ?? null,
            'semester' => $_SESSION['semester'] ?? null,
            'student_id' => $_SESSION['student_id'] ?? null,
            'teacher_id' => $_SESSION['teacher_id'] ?? null,
        ];
    }

    /**
     * Check if user has a specific role
     * 
     * @param string $role Role to check
     * @return bool True if user has role
     */
    public function hasRole(string $role): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    /**
     * Check if user has any of the specified roles
     * 
     * @param array $roles Roles to check
     * @return bool True if user has any of the roles
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], $roles);
    }

    /**
     * Hash a password using bcrypt
     * 
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->bcryptCost]);
    }

    /**
     * Verify a password against a hash
     * 
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if password matches
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehashing (cost factor changed)
     * 
     * @param string $hash Hashed password
     * @return bool True if needs rehashing
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $this->bcryptCost]);
    }

    /**
     * Get session lifetime in minutes
     * 
     * @return int Session lifetime
     */
    public function getSessionLifetime(): int
    {
        return $this->sessionLifetime;
    }

    /**
     * Get remaining session time in seconds
     * 
     * @return int Remaining time
     */
    public function getRemainingSessionTime(): int
    {
        if (!isset($_SESSION['last_activity'])) {
            return 0;
        }

        $elapsed = time() - $_SESSION['last_activity'];
        $maxTime = $this->sessionLifetime * 60;
        $remaining = $maxTime - $elapsed;

        return max(0, $remaining);
    }
}

