<?php

namespace App\Middleware;

use App\Services\AuthService;

/**
 * Authentication Middleware
 * 
 * Checks if user is authenticated before allowing access to protected routes
 */
class AuthMiddleware
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Handle the request
     * 
     * @param callable $next Next middleware or controller
     * @return mixed
     */
    public function handle(callable $next)
    {
        // Check if user is authenticated
        if (!$this->authService->isAuthenticated()) {
            // Store intended URL for redirect after login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            
            // Redirect to login page
            header('Location: /login');
            exit;
        }

        // User is authenticated, continue to next middleware or controller
        return $next();
    }

    /**
     * Check authentication without redirecting
     * 
     * @return bool True if authenticated
     */
    public function check(): bool
    {
        return $this->authService->isAuthenticated();
    }

    /**
     * Get authenticated user
     * 
     * @return array|null User data or null
     */
    public function user(): ?array
    {
        return $this->authService->getCurrentUser();
    }
}

