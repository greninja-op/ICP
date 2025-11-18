<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Models\User;
use Exception;

/**
 * Authentication Controller
 * 
 * Handles authentication requests, CSRF protection, and rate limiting
 */
class AuthController extends BaseController
{
    private AuthService $authService;
    private User $userModel;
    private int $maxAttempts;
    private int $lockoutWindow;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
        $this->userModel = new User();
        $this->maxAttempts = (int) (getenv('RATE_LIMIT_LOGIN_ATTEMPTS') ?: 5);
        $this->lockoutWindow = (int) (getenv('RATE_LIMIT_LOGIN_WINDOW') ?: 900); // 15 minutes in seconds
    }

    /**
     * Display login page
     */
    public function showLogin(): void
    {
        // Redirect if already authenticated
        if ($this->authService->isAuthenticated()) {
            $this->redirectToDashboard();
            return;
        }

        // Generate CSRF token
        $csrfToken = $this->generateCsrfToken();

        $this->view('auth/login', [
            'csrf_token' => $csrfToken,
            'error' => $_SESSION['login_error'] ?? null
        ]);

        // Clear error message
        unset($_SESSION['login_error']);
    }

    /**
     * Handle login request
     */
    public function login(): void
    {
        // Check if already authenticated
        if ($this->authService->isAuthenticated()) {
            $this->json([
                'success' => true,
                'redirect' => $this->getDashboardUrl()
            ]);
            return;
        }

        // Validate CSRF token
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json([
                'success' => false,
                'message' => 'Invalid request. Please refresh the page and try again.',
                'errors' => []
            ], 403);
            return;
        }

        // Check rate limiting
        if ($this->isRateLimited()) {
            $remainingTime = $this->getRemainingLockoutTime();
            $this->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in " . ceil($remainingTime / 60) . " minutes.",
                'errors' => []
            ], 429);
            return;
        }

        // Get login credentials
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        // Attempt login
        $result = $this->authService->login($username, $password, $role);

        if ($result['success']) {
            // Clear login attempts
            $this->clearLoginAttempts();

            // Set remember me cookie if requested
            if ($rememberMe) {
                $this->setRememberMeCookie($role);
            }

            // Log successful login
            $this->logActivity('login_success', 'user', $result['user']['id'], 'User logged in successfully');

            $this->json([
                'success' => true,
                'message' => $result['message'],
                'redirect' => $this->getDashboardUrl($role)
            ]);
        } else {
            // Increment login attempts
            $this->incrementLoginAttempts();

            // Log failed login
            $this->logActivity('login_failed', null, null, "Failed login attempt for username: $username");

            $this->json($result, 401);
        }
    }

    /**
     * Handle logout request
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        // Log logout
        if ($userId) {
            $this->logActivity('logout', 'user', $userId, 'User logged out');
        }

        // Perform logout
        $result = $this->authService->logout();

        // Clear remember me cookie
        $this->clearRememberMeCookie();

        $this->redirect('/login');
    }

    /**
     * Display forgot password page
     */
    public function showForgotPassword(): void
    {
        $csrfToken = $this->generateCsrfToken();

        $this->view('auth/forgot-password', [
            'csrf_token' => $csrfToken,
            'message' => $_SESSION['reset_message'] ?? null
        ]);

        unset($_SESSION['reset_message']);
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(): void
    {
        // Validate CSRF token
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json([
                'success' => false,
                'message' => 'Invalid request. Please refresh the page and try again.',
                'errors' => []
            ], 403);
            return;
        }

        $email = trim($_POST['email'] ?? '');

        // Validate email
        if (empty($email)) {
            $this->json([
                'success' => false,
                'message' => 'Email is required',
                'errors' => ['email' => 'Email is required']
            ], 400);
            return;
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);

        // Always return success to prevent email enumeration
        // In production, send actual password reset email here
        $this->json([
            'success' => true,
            'message' => 'If an account exists with this email, a password reset link has been sent.'
        ]);

        // Log password reset request
        if ($user) {
            $this->logActivity('password_reset_requested', 'user', $user['id'], 'Password reset requested');
        }
    }

    /**
     * Generate CSRF token
     * 
     * @return string CSRF token
     */
    private function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     * 
     * @param string $token Token to validate
     * @return bool True if valid
     */
    private function validateCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        // Check token expiry (1 hour)
        $tokenExpiry = (int) (getenv('CSRF_TOKEN_EXPIRY') ?: 3600);
        if (time() - $_SESSION['csrf_token_time'] > $tokenExpiry) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }

        // Validate token
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if user is rate limited
     * 
     * @return bool True if rate limited
     */
    private function isRateLimited(): bool
    {
        $attempts = $this->getLoginAttempts();
        return $attempts >= $this->maxAttempts;
    }

    /**
     * Get number of login attempts
     * 
     * @return int Number of attempts
     */
    private function getLoginAttempts(): int
    {
        if (!isset($_SESSION['login_attempts'])) {
            return 0;
        }

        // Check if lockout window has expired
        if (time() - $_SESSION['login_attempts_time'] > $this->lockoutWindow) {
            $this->clearLoginAttempts();
            return 0;
        }

        return $_SESSION['login_attempts'];
    }

    /**
     * Increment login attempts
     */
    private function incrementLoginAttempts(): void
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempts_time'] = time();
        }

        $_SESSION['login_attempts']++;
    }

    /**
     * Clear login attempts
     */
    private function clearLoginAttempts(): void
    {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['login_attempts_time']);
    }

    /**
     * Get remaining lockout time in seconds
     * 
     * @return int Remaining time
     */
    private function getRemainingLockoutTime(): int
    {
        if (!isset($_SESSION['login_attempts_time'])) {
            return 0;
        }

        $elapsed = time() - $_SESSION['login_attempts_time'];
        $remaining = $this->lockoutWindow - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Set remember me cookie
     * 
     * @param string $role User role
     */
    private function setRememberMeCookie(string $role): void
    {
        setcookie(
            'remember_role',
            $role,
            time() + (86400 * 30), // 30 days
            '/',
            '',
            false,
            true
        );
    }

    /**
     * Clear remember me cookie
     */
    private function clearRememberMeCookie(): void
    {
        setcookie(
            'remember_role',
            '',
            time() - 3600,
            '/',
            '',
            false,
            true
        );
    }

    /**
     * Get dashboard URL based on role
     * 
     * @param string|null $role User role
     * @return string Dashboard URL
     */
    private function getDashboardUrl(?string $role = null): string
    {
        if ($role === null) {
            $role = $_SESSION['user_role'] ?? 'student';
        }

        switch ($role) {
            case 'admin':
                return '/admin/dashboard';
            case 'teacher':
                return '/teacher/dashboard';
            case 'student':
            default:
                return '/dashboard';
        }
    }

    /**
     * Redirect to appropriate dashboard
     */
    private function redirectToDashboard(): void
    {
        $url = $this->getDashboardUrl();
        $this->redirect($url);
    }

    /**
     * Log activity
     * 
     * @param string $action Action performed
     * @param string|null $entityType Entity type
     * @param int|null $entityId Entity ID
     * @param string $description Description
     */
    private function logActivity(string $action, ?string $entityType, ?int $entityId, string $description): void
    {
        // This will be implemented when we create the ActivityLog model
        // For now, just log to file
        $logPath = dirname(__DIR__, 2) . '/logs/auth.log';
        $logDir = dirname($logPath);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'] ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] [%s] [user:%s] [ip:%s] %s\n",
            $timestamp,
            strtoupper($action),
            $userId,
            $ip,
            $description
        );

        file_put_contents($logPath, $logEntry, FILE_APPEND);
    }
}

