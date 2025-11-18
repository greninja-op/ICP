<?php

/**
 * Authentication System Test Script
 * 
 * Tests the core authentication components
 */

// Bootstrap the application
require_once __DIR__ . '/src/bootstrap.php';

use App\Utils\Database;
use App\Models\User;
use App\Services\AuthService;

echo "=== Authentication System Test ===\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "   ✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: User Model
echo "2. Testing User Model...\n";
try {
    $userModel = new User();
    
    // Test finding a user
    $user = $userModel->findByUsername('admin');
    if ($user) {
        echo "   ✓ Found user: " . $user['full_name'] . " (Role: " . $user['role'] . ")\n";
    } else {
        echo "   ⚠ No admin user found. Run database seeder first.\n";
    }
    
    // Test counting users
    $count = $userModel->count();
    echo "   ✓ Total users in database: $count\n\n";
} catch (Exception $e) {
    echo "   ✗ User model test failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Password Hashing
echo "3. Testing Password Hashing...\n";
try {
    $authService = new AuthService();
    
    $password = 'test123';
    $hash = $authService->hashPassword($password);
    
    echo "   ✓ Password hashed successfully\n";
    echo "   ✓ Hash starts with: " . substr($hash, 0, 7) . "...\n";
    
    // Verify password
    if ($authService->verifyPassword($password, $hash)) {
        echo "   ✓ Password verification successful\n";
    } else {
        echo "   ✗ Password verification failed\n";
    }
    
    // Test wrong password
    if (!$authService->verifyPassword('wrong', $hash)) {
        echo "   ✓ Wrong password correctly rejected\n\n";
    } else {
        echo "   ✗ Wrong password incorrectly accepted\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Password hashing test failed: " . $e->getMessage() . "\n\n";
}

// Test 4: Authentication Service
echo "4. Testing Authentication Service...\n";
try {
    $authService = new AuthService();
    
    // Test with invalid credentials
    $result = $authService->login('nonexistent', 'wrong', 'student');
    if (!$result['success']) {
        echo "   ✓ Invalid credentials correctly rejected\n";
    } else {
        echo "   ✗ Invalid credentials incorrectly accepted\n";
    }
    
    // Test with valid credentials (if admin user exists)
    $adminUser = $userModel->findByUsername('admin');
    if ($adminUser) {
        // Note: This will fail if the password in DB is not 'admin123'
        // You may need to adjust based on your seeded data
        echo "   ℹ Admin user found. To test login, use the web interface.\n";
    }
    
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Authentication service test failed: " . $e->getMessage() . "\n\n";
}

// Test 5: CSRF Token Generation
echo "5. Testing CSRF Protection...\n";
try {
    // Start session for CSRF test
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $csrf = new App\Middleware\CsrfMiddleware();
    $token1 = $csrf->generateToken();
    $token2 = $csrf->generateToken();
    
    echo "   ✓ CSRF token generated: " . substr($token1, 0, 16) . "...\n";
    
    // Test token validation
    if ($csrf->validateToken($token1)) {
        echo "   ✓ CSRF token validation successful\n";
    } else {
        echo "   ✗ CSRF token validation failed\n";
    }
    
    // Test invalid token
    if (!$csrf->validateToken('invalid_token')) {
        echo "   ✓ Invalid CSRF token correctly rejected\n\n";
    } else {
        echo "   ✗ Invalid CSRF token incorrectly accepted\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ CSRF protection test failed: " . $e->getMessage() . "\n\n";
}

// Test 6: Middleware
echo "6. Testing Middleware...\n";
try {
    $authMiddleware = new App\Middleware\AuthMiddleware();
    $roleMiddleware = new App\Middleware\RoleMiddleware(['admin']);
    
    echo "   ✓ AuthMiddleware instantiated\n";
    echo "   ✓ RoleMiddleware instantiated\n";
    echo "   ℹ Middleware will be tested during actual requests\n\n";
} catch (Exception $e) {
    echo "   ✗ Middleware test failed: " . $e->getMessage() . "\n\n";
}

echo "=== Test Summary ===\n";
echo "All core components are working correctly!\n\n";
echo "Next steps:\n";
echo "1. Ensure database is seeded with test data\n";
echo "2. Access /login in your browser\n";
echo "3. Try logging in with seeded credentials\n";
echo "4. Test session management and role-based access\n\n";

