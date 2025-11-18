<?php

namespace App\Middleware;

use App\Services\AuthService;

/**
 * Role Middleware
 * 
 * Checks if user has required role(s) before allowing access to routes
 */
class RoleMiddleware
{
    private AuthService $authService;
    private array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->authService = new AuthService();
        $this->allowedRoles = $allowedRoles;
    }

    /**
     * Handle the request
     * 
     * @param callable $next Next middleware or controller
     * @return mixed
     */
    public function handle(callable $next)
    {
        // First check if user is authenticated
        if (!$this->authService->isAuthenticated()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: /login');
            exit;
        }

        // Check if user has required role
        if (!$this->authService->hasAnyRole($this->allowedRoles)) {
            // User doesn't have required role, show forbidden page
            http_response_code(403);
            header('Location: /403');
            exit;
        }

        // User has required role, continue
        return $next();
    }

    /**
     * Check if user has specific role
     * 
     * @param string $role Role to check
     * @return bool True if user has role
     */
    public function hasRole(string $role): bool
    {
        return $this->authService->hasRole($role);
    }

    /**
     * Check if user has any of the specified roles
     * 
     * @param array $roles Roles to check
     * @return bool True if user has any role
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->authService->hasAnyRole($roles);
    }

    /**
     * Get user's role
     * 
     * @return string|null User role or null
     */
    public function getUserRole(): ?string
    {
        $user = $this->authService->getCurrentUser();
        return $user['role'] ?? null;
    }

    /**
     * Set allowed roles
     * 
     * @param array $roles Allowed roles
     */
    public function setAllowedRoles(array $roles): void
    {
        $this->allowedRoles = $roles;
    }

    /**
     * Get allowed roles
     * 
     * @return array Allowed roles
     */
    public function getAllowedRoles(): array
    {
        return $this->allowedRoles;
    }
}

