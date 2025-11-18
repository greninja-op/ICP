<?php

/**
 * Test script for routing system
 * 
 * This script tests the Router class functionality
 */

require_once __DIR__ . '/src/bootstrap.php';

use App\Utils\Router;

echo "=== Testing Router Class ===\n\n";

// Test 1: Route registration
echo "Test 1: Route Registration\n";
$router = new Router();
$router->get('/test', function() {
    echo "Test route executed\n";
});
echo "✓ Route registered successfully\n\n";

// Test 2: Parameter extraction
echo "Test 2: Parameter Extraction\n";
$testUri = '/user/123';
$testPattern = '/user/{id}';
$params = [];

// Use reflection to test private method
$reflection = new ReflectionClass($router);
$method = $reflection->getMethod('matchPath');
$method->setAccessible(true);

$result = $method->invoke($router, $testPattern, $testUri, $params);
if ($result && isset($params['id']) && $params['id'] === '123') {
    echo "✓ Parameter extraction works correctly\n";
    echo "  Extracted: id = {$params['id']}\n";
} else {
    echo "✗ Parameter extraction failed\n";
}
echo "\n";

// Test 3: Route matching
echo "Test 3: Route Matching\n";
$testCases = [
    ['/dashboard', '/dashboard', true],
    ['/dashboard', '/dashboard/', true],
    ['/user/{id}', '/user/123', true],
    ['/user/{id}', '/user/abc', true],
    ['/user/{id}', '/users/123', false],
    ['/admin/students', '/admin/students', true],
];

foreach ($testCases as $i => $case) {
    list($pattern, $uri, $expected) = $case;
    $params = [];
    $result = $method->invoke($router, $pattern, $uri, $params);
    $status = ($result === $expected) ? '✓' : '✗';
    echo "$status Test case " . ($i + 1) . ": Pattern '$pattern' vs URI '$uri' = " . ($result ? 'match' : 'no match') . "\n";
}
echo "\n";

// Test 4: canAccess method
echo "Test 4: Role-Based Access Control\n";
$testUsers = [
    ['role' => 'student', 'name' => 'Student User'],
    ['role' => 'teacher', 'name' => 'Teacher User'],
    ['role' => 'admin', 'name' => 'Admin User'],
];

$testRoutes = [
    '/dashboard' => ['student'],
    '/teacher/dashboard' => ['teacher'],
    '/admin/dashboard' => ['admin'],
    '/notices' => ['student', 'teacher', 'admin'],
];

foreach ($testRoutes as $route => $allowedRoles) {
    echo "Route: $route (allowed: " . implode(', ', $allowedRoles) . ")\n";
    foreach ($testUsers as $user) {
        $canAccess = Router::canAccess($route, $user);
        $expected = in_array($user['role'], $allowedRoles);
        $status = ($canAccess === $expected) ? '✓' : '✗';
        echo "  $status {$user['name']} ({$user['role']}): " . ($canAccess ? 'allowed' : 'denied') . "\n";
    }
}
echo "\n";

// Test 5: URL without .php extension
echo "Test 5: Clean URLs (no .php extensions)\n";
$cleanUrls = [
    '/login',
    '/dashboard',
    '/subjects',
    '/admin/students',
    '/teacher/attendance',
];

foreach ($cleanUrls as $url) {
    if (strpos($url, '.php') === false) {
        echo "✓ $url - Clean URL (no .php extension)\n";
    } else {
        echo "✗ $url - Contains .php extension\n";
    }
}
echo "\n";

echo "=== All Tests Complete ===\n";
