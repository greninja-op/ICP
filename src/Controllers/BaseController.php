<?php

namespace App\Controllers;

use App\Services\AuthService;

/**
 * Base Controller
 * 
 * Provides common functionality for all controllers
 */
class BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Render a view
     * 
     * @param string $view View name (relative to views directory)
     * @param array $data Data to pass to view
     */
    protected function view(string $view, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        // Get base path
        $basePath = dirname(__DIR__, 2);
        $viewPath = $basePath . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: $view");
        }

        require $viewPath;
    }

    /**
     * Return JSON response
     * 
     * @param array $data Data to return
     * @param int $statusCode HTTP status code
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * Get current authenticated user
     * 
     * @return array|null User data or null
     */
    protected function getUser(): ?array
    {
        return $this->authService->getCurrentUser();
    }

    /**
     * Require authentication
     * 
     * @param string|null $redirectUrl URL to redirect to if not authenticated
     */
    protected function requireAuth(?string $redirectUrl = '/login'): void
    {
        if (!$this->authService->isAuthenticated()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Require specific role
     * 
     * @param string|array $roles Required role(s)
     * @param string|null $redirectUrl URL to redirect to if unauthorized
     */
    protected function requireRole($roles, ?string $redirectUrl = '/login'): void
    {
        $this->requireAuth($redirectUrl);

        $roles = is_array($roles) ? $roles : [$roles];

        if (!$this->authService->hasAnyRole($roles)) {
            $this->redirect('/403');
        }
    }

    /**
     * Get input from request
     * 
     * @param string $key Input key
     * @param mixed $default Default value
     * @return mixed Input value
     */
    protected function input(string $key, $default = null)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }

    /**
     * Get all input from request
     * 
     * @return array All input data
     */
    protected function allInput(): array
    {
        return array_merge($_GET, $_POST);
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
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required";
            }
        }

        return $errors;
    }

    /**
     * Sanitize input string
     * 
     * @param string $input Input to sanitize
     * @return string Sanitized input
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get base URL
     * 
     * @return string Base URL
     */
    protected function getBaseUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "$protocol://$host";
    }

    /**
     * Get current URL
     * 
     * @return string Current URL
     */
    protected function getCurrentUrl(): string
    {
        return $this->getBaseUrl() . ($_SERVER['REQUEST_URI'] ?? '');
    }

    /**
     * Check if request is AJAX
     * 
     * @return bool True if AJAX request
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request method is POST
     * 
     * @return bool True if POST request
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request method is GET
     * 
     * @return bool True if GET request
     */
    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Flash a message to session
     * 
     * @param string $key Message key
     * @param string $message Message content
     */
    protected function flash(string $key, string $message): void
    {
        $_SESSION["flash_$key"] = $message;
    }

    /**
     * Get and clear a flash message
     * 
     * @param string $key Message key
     * @return string|null Message content
     */
    protected function getFlash(string $key): ?string
    {
        $message = $_SESSION["flash_$key"] ?? null;
        unset($_SESSION["flash_$key"]);
        return $message;
    }
}

