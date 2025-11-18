<?php

namespace App\Utils;

/**
 * Router Class
 * 
 * Handles URL routing and dispatching to controllers with middleware support
 * Implements clean URLs without .php extensions
 */
class Router
{
    private array $routes = [];
    private array $params = [];

    /**
     * Add a GET route
     * 
     * @param string $path Route path (supports parameters like /user/{id})
     * @param callable|string $handler Controller method or callable
     * @param array $middleware Middleware classes to apply
     */
    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Add a POST route
     * 
     * @param string $path Route path (supports parameters like /user/{id})
     * @param callable|string $handler Controller method or callable
     * @param array $middleware Middleware classes to apply
     */
    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Add a PUT route
     * 
     * @param string $path Route path
     * @param callable|string $handler Controller method or callable
     * @param array $middleware Middleware classes to apply
     */
    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Add a DELETE route
     * 
     * @param string $path Route path
     * @param callable|string $handler Controller method or callable
     * @param array $middleware Middleware classes to apply
     */
    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Add a route
     * 
     * @param string $method HTTP method
     * @param string $path Route path
     * @param callable|string $handler Controller method or callable
     * @param array $middleware Middleware classes to apply
     */
    private function addRoute(string $method, string $path, $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Dispatch the request
     * 
     * @param string $method HTTP method
     * @param string $uri Request URI
     */
    public function dispatch(string $method, string $uri): void
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $params = [];
                if ($this->matchPath($route['path'], $uri, $params)) {
                    $this->params = $params;
                    
                    // Execute middleware chain
                    $this->executeMiddleware($route['middleware'], function() use ($route) {
                        $this->executeHandler($route['handler']);
                    });
                    return;
                }
            }
        }

        // No route found
        $this->notFound();
    }

    /**
     * Match path pattern with parameter support
     * 
     * @param string $pattern Route pattern (e.g., /user/{id})
     * @param string $uri Request URI
     * @param array &$params Extracted parameters
     * @return bool True if matches
     */
    private function matchPath(string $pattern, string $uri, array &$params): bool
    {
        // Convert pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            // Extract named parameters
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }
        
        return false;
    }

    /**
     * Execute middleware chain
     * 
     * @param array $middlewareClasses Middleware classes
     * @param callable $next Next handler
     */
    private function executeMiddleware(array $middlewareClasses, callable $next): void
    {
        if (empty($middlewareClasses)) {
            $next();
            return;
        }

        $middlewareClass = array_shift($middlewareClasses);
        
        if (!class_exists($middlewareClass)) {
            throw new \Exception("Middleware class not found: $middlewareClass");
        }

        $middleware = new $middlewareClass();
        
        // Execute middleware with next handler
        $middleware->handle(function() use ($middlewareClasses, $next) {
            $this->executeMiddleware($middlewareClasses, $next);
        });
    }

    /**
     * Execute handler
     * 
     * @param callable|string $handler Handler to execute
     */
    private function executeHandler($handler): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $this->params);
        } elseif (is_string($handler)) {
            // Parse Controller@method format
            if (strpos($handler, '@') === false) {
                throw new \Exception("Invalid handler format: $handler");
            }
            
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\$controller";
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller not found: $controllerClass");
            }
            
            $instance = new $controllerClass();
            
            if (!method_exists($instance, $method)) {
                throw new \Exception("Method not found: $controllerClass::$method");
            }
            
            $instance->$method($this->params);
        }
    }

    /**
     * Get route parameters
     * 
     * @return array Route parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get specific route parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed Parameter value
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Check if user can access a route (for testing)
     * 
     * @param string $route Route path
     * @param array $user User data
     * @return bool True if user can access
     */
    public static function canAccess(string $route, array $user): bool
    {
        // Define route permissions
        $permissions = [
            '/admin/students' => ['admin'],
            '/admin/teachers' => ['admin'],
            '/admin/dashboard' => ['admin'],
            '/admin/fees' => ['admin'],
            '/admin/courses' => ['admin'],
            '/teacher/dashboard' => ['teacher'],
            '/teacher/attendance' => ['teacher'],
            '/teacher/marks' => ['teacher'],
            '/teacher/students' => ['teacher'],
            '/dashboard' => ['student'],
            '/subjects' => ['student'],
            '/results' => ['student'],
            '/payments' => ['student'],
            '/notices' => ['student', 'teacher', 'admin'],
            '/analysis' => ['student'],
        ];

        // Check if route has permissions defined
        if (!isset($permissions[$route])) {
            return true; // Public route
        }

        // Check if user has required role
        return in_array($user['role'] ?? '', $permissions[$route]);
    }

    /**
     * Handle 404 Not Found
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo "<!DOCTYPE html>\n";
        echo "<html lang=\"en\">\n";
        echo "<head>\n";
        echo "    <meta charset=\"UTF-8\">\n";
        echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        echo "    <title>404 - Page Not Found</title>\n";
        echo "    <style>\n";
        echo "        * { margin: 0; padding: 0; box-sizing: border-box; }\n";
        echo "        body { \n";
        echo "            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;\n";
        echo "            background: linear-gradient(135deg, #e0f2ff 0%, #ffffff 100%);\n";
        echo "            min-height: 100vh;\n";
        echo "            display: flex;\n";
        echo "            align-items: center;\n";
        echo "            justify-content: center;\n";
        echo "            padding: 2rem;\n";
        echo "        }\n";
        echo "        .error-container {\n";
        echo "            background: rgba(255, 255, 255, 0.7);\n";
        echo "            backdrop-filter: blur(10px);\n";
        echo "            border-radius: 1.5rem;\n";
        echo "            padding: 3rem;\n";
        echo "            text-align: center;\n";
        echo "            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);\n";
        echo "            max-width: 500px;\n";
        echo "        }\n";
        echo "        h1 { \n";
        echo "            color: #137fec;\n";
        echo "            font-size: 4rem;\n";
        echo "            font-weight: 900;\n";
        echo "            margin-bottom: 1rem;\n";
        echo "        }\n";
        echo "        h2 {\n";
        echo "            color: #333;\n";
        echo "            font-size: 1.5rem;\n";
        echo "            font-weight: 700;\n";
        echo "            margin-bottom: 1rem;\n";
        echo "        }\n";
        echo "        p { \n";
        echo "            color: #666;\n";
        echo "            font-size: 1rem;\n";
        echo "            margin-bottom: 2rem;\n";
        echo "            line-height: 1.6;\n";
        echo "        }\n";
        echo "        a { \n";
        echo "            display: inline-block;\n";
        echo "            background: #137fec;\n";
        echo "            color: white;\n";
        echo "            padding: 0.75rem 2rem;\n";
        echo "            border-radius: 0.5rem;\n";
        echo "            text-decoration: none;\n";
        echo "            font-weight: 500;\n";
        echo "            transition: all 0.3s ease;\n";
        echo "        }\n";
        echo "        a:hover {\n";
        echo "            background: #0d6efd;\n";
        echo "            transform: translateY(-2px);\n";
        echo "            box-shadow: 0 4px 12px rgba(19, 127, 236, 0.3);\n";
        echo "        }\n";
        echo "    </style>\n";
        echo "</head>\n";
        echo "<body>\n";
        echo "    <div class=\"error-container\">\n";
        echo "        <h1>404</h1>\n";
        echo "        <h2>Page Not Found</h2>\n";
        echo "        <p>The page you are looking for doesn't exist or has been moved.</p>\n";
        echo "        <a href=\"/\">Go to Home</a>\n";
        echo "    </div>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
    }
}

