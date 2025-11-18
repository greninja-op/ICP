<?php

namespace App\Middleware;

/**
 * CSRF Middleware
 * 
 * Provides CSRF token generation and validation for form submissions
 */
class CsrfMiddleware
{
    private int $tokenExpiry;

    public function __construct()
    {
        $this->tokenExpiry = (int) (getenv('CSRF_TOKEN_EXPIRY') ?: 3600); // 1 hour default
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Handle the request
     * 
     * @param callable $next Next middleware or controller
     * @return mixed
     */
    public function handle(callable $next)
    {
        // Only validate CSRF for POST, PUT, DELETE requests
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            // Get token from request
            $token = $this->getTokenFromRequest();
            
            // Validate token
            if (!$this->validateToken($token)) {
                // CSRF validation failed
                http_response_code(403);
                
                // Return JSON for AJAX requests
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'CSRF token validation failed. Please refresh the page and try again.',
                        'errors' => []
                    ]);
                    exit;
                }
                
                // Redirect for regular requests
                header('Location: /403');
                exit;
            }
        }

        // CSRF validation passed or not required, continue
        return $next();
    }

    /**
     * Generate a new CSRF token
     * 
     * @return string CSRF token
     */
    public function generateToken(): string
    {
        // Generate new token if not exists or expired
        if (!isset($_SESSION['csrf_token']) || $this->isTokenExpired()) {
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
    public function validateToken(string $token): bool
    {
        // Check if token exists in session
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        // Check if token is expired
        if ($this->isTokenExpired()) {
            $this->clearToken();
            return false;
        }

        // Validate token using timing-safe comparison
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get token from request
     * 
     * @return string Token from request
     */
    private function getTokenFromRequest(): string
    {
        // Check POST data
        if (isset($_POST['csrf_token'])) {
            return $_POST['csrf_token'];
        }

        // Check headers (for AJAX requests)
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return $_SERVER['HTTP_X_CSRF_TOKEN'];
        }

        return '';
    }

    /**
     * Check if token is expired
     * 
     * @return bool True if expired
     */
    private function isTokenExpired(): bool
    {
        if (!isset($_SESSION['csrf_token_time'])) {
            return true;
        }

        $elapsed = time() - $_SESSION['csrf_token_time'];
        return $elapsed > $this->tokenExpiry;
    }

    /**
     * Clear CSRF token from session
     */
    public function clearToken(): void
    {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }

    /**
     * Regenerate CSRF token
     * 
     * @return string New CSRF token
     */
    public function regenerateToken(): string
    {
        $this->clearToken();
        return $this->generateToken();
    }

    /**
     * Get CSRF token for forms
     * 
     * @return string HTML hidden input with CSRF token
     */
    public function getTokenField(): string
    {
        $token = $this->generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Get CSRF token for meta tag
     * 
     * @return string HTML meta tag with CSRF token
     */
    public function getTokenMeta(): string
    {
        $token = $this->generateToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Check if request is AJAX
     * 
     * @return bool True if AJAX request
     */
    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get token expiry time in seconds
     * 
     * @return int Token expiry time
     */
    public function getTokenExpiry(): int
    {
        return $this->tokenExpiry;
    }

    /**
     * Get remaining token time in seconds
     * 
     * @return int Remaining time
     */
    public function getRemainingTokenTime(): int
    {
        if (!isset($_SESSION['csrf_token_time'])) {
            return 0;
        }

        $elapsed = time() - $_SESSION['csrf_token_time'];
        $remaining = $this->tokenExpiry - $elapsed;

        return max(0, $remaining);
    }
}

