# Authentication & Authorization Guide

## Overview

This document describes the authentication and authorization implementation for the University Student Portal backend.

## Authentication Strategy

We use **JWT (JSON Web Tokens)** for stateless authentication.

### Why JWT?

- **Stateless**: No server-side session storage required
- **Scalable**: Works well with load balancers
- **Cross-domain**: Can be used across multiple domains
- **Mobile-friendly**: Easy to implement in mobile apps

## Implementation Steps

### 1. User Registration (Future Scope)

Currently, users are created by administrators. Future implementation may include self-registration.

```php
// Example: User creation by admin
function createUser($data) {
    $hashedPassword = password_hash($data['password'], PASSWORD_ARGON2ID);
    
    $user = [
        'username' => $data['username'],
        'email' => $data['email'],
        'password_hash' => $hashedPassword,
        'role' => $data['role'],
        'is_active' => true,
        'email_verified' => false
    ];
    
    // Insert into database
    return insertUser($user);
}
```

### 2. Login Process

**Step-by-step flow:**

1. User submits credentials (username/email + password + role)
2. Backend validates credentials
3. Backend generates JWT token
4. Token sent to frontend
5. Frontend stores token (localStorage/sessionStorage)
6. Frontend includes token in subsequent requests

**PHP Implementation Example:**

```php
<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function login($username, $password, $role) {
    // 1. Validate input
    if (empty($username) || empty($password) || empty($role)) {
        return error('All fields are required', 400);
    }
    
    // 2. Find user in database
    $user = findUserByUsername($username);
    
    if (!$user) {
        return error('Invalid credentials', 401);
    }
    
    // 3. Verify role matches
    if ($user['role'] !== $role) {
        return error('Invalid role selected', 401);
    }
    
    // 4. Verify password
    if (!password_verify($password, $user['password_hash'])) {
        return error('Invalid credentials', 401);
    }
    
    // 5. Check if account is active
    if (!$user['is_active']) {
        return error('Account is deactivated', 403);
    }
    
    // 6. Generate JWT token
    $token = generateJWT($user);
    
    // 7. Update last login
    updateLastLogin($user['id']);
    
    // 8. Get user profile based on role
    $profile = getUserProfile($user['id'], $user['role']);
    
    // 9. Return response
    return success([
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'profile' => $profile
        ]
    ]);
}

function generateJWT($user) {
    $secretKey = getenv('JWT_SECRET_KEY'); // Store in .env file
    $issuedAt = time();
    $expirationTime = $issuedAt + (24 * 60 * 60); // 24 hours
    
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'iss' => 'university-portal',
        'data' => [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ];
    
    return JWT::encode($payload, $secretKey, 'HS256');
}
```

### 3. Token Verification Middleware

Every protected route must verify the JWT token.

```php
<?php
function verifyToken() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        return error('No token provided', 401);
    }
    
    $authHeader = $headers['Authorization'];
    
    // Extract token from "Bearer <token>"
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return error('Invalid token format', 401);
    }
    
    $token = $matches[1];
    
    try {
        $secretKey = getenv('JWT_SECRET_KEY');
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        
        // Token is valid, return user data
        return $decoded->data;
        
    } catch (Exception $e) {
        return error('Invalid or expired token', 401);
    }
}

// Usage in protected routes
function getProtectedData() {
    $user = verifyToken();
    
    if (isset($user['error'])) {
        return $user; // Return error
    }
    
    // User is authenticated, proceed with logic
    return success(['data' => 'Protected data']);
}
```

### 4. Role-Based Access Control (RBAC)

Different roles have different permissions.

```php
<?php
function checkPermission($requiredRole) {
    $user = verifyToken();
    
    if (isset($user['error'])) {
        return $user;
    }
    
    $roleHierarchy = [
        'admin' => 3,
        'faculty' => 2,
        'student' => 1
    ];
    
    $userLevel = $roleHierarchy[$user->role] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    if ($userLevel < $requiredLevel) {
        return error('Insufficient permissions', 403);
    }
    
    return $user;
}

// Usage
function adminOnlyRoute() {
    $user = checkPermission('admin');
    
    if (isset($user['error'])) {
        return $user;
    }
    
    // Admin-only logic here
}
```

### 5. Password Reset Flow

**Step 1: Request Password Reset**

```php
<?php
function requestPasswordReset($email) {
    $user = findUserByEmail($email);
    
    if (!$user) {
        // Don't reveal if email exists
        return success(['message' => 'If email exists, reset link sent']);
    }
    
    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Save token to database
    savePasswordResetToken($user['id'], $resetToken, $expiresAt);
    
    // Send email with reset link
    $resetLink = "https://portal.university.edu/reset-password?token=$resetToken";
    sendEmail($user['email'], 'Password Reset', "Click here: $resetLink");
    
    return success(['message' => 'Password reset link sent']);
}
```

**Step 2: Reset Password**

```php
<?php
function resetPassword($token, $newPassword, $confirmPassword) {
    // Validate passwords match
    if ($newPassword !== $confirmPassword) {
        return error('Passwords do not match', 400);
    }
    
    // Validate password strength
    if (strlen($newPassword) < 8) {
        return error('Password must be at least 8 characters', 400);
    }
    
    // Find token in database
    $resetRecord = findPasswordResetToken($token);
    
    if (!$resetRecord) {
        return error('Invalid or expired token', 400);
    }
    
    // Check if token is expired
    if (strtotime($resetRecord['expires_at']) < time()) {
        return error('Token has expired', 400);
    }
    
    // Check if token already used
    if ($resetRecord['used']) {
        return error('Token already used', 400);
    }
    
    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
    
    // Update user password
    updateUserPassword($resetRecord['user_id'], $hashedPassword);
    
    // Mark token as used
    markTokenAsUsed($token);
    
    return success(['message' => 'Password reset successful']);
}
```

### 6. Logout

For JWT, logout is handled client-side by removing the token. Optionally, maintain a token blacklist.

```php
<?php
function logout() {
    $user = verifyToken();
    
    if (isset($user['error'])) {
        return $user;
    }
    
    // Optional: Add token to blacklist
    $headers = getallheaders();
    preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches);
    $token = $matches[1];
    
    addTokenToBlacklist($token);
    
    return success(['message' => 'Logged out successfully']);
}

// Check blacklist in verifyToken
function isTokenBlacklisted($token) {
    // Query database for blacklisted tokens
    return checkBlacklist($token);
}
```

### 7. Token Refresh

Allow users to refresh their token before it expires.

```php
<?php
function refreshToken() {
    $user = verifyToken();
    
    if (isset($user['error'])) {
        return $user;
    }
    
    // Generate new token
    $newToken = generateJWT([
        'id' => $user->user_id,
        'username' => $user->username,
        'email' => $user->email,
        'role' => $user->role
    ]);
    
    return success(['token' => $newToken]);
}
```

## Security Best Practices

### 1. Password Hashing

```php
// Use Argon2id (most secure) or bcrypt
$hash = password_hash($password, PASSWORD_ARGON2ID);

// Verify
if (password_verify($inputPassword, $hash)) {
    // Password correct
}
```

### 2. JWT Secret Key

```bash
# Generate strong secret key
openssl rand -base64 64
```

Store in `.env` file:
```
JWT_SECRET_KEY=your_generated_secret_key_here
JWT_EXPIRATION=86400
```

### 3. HTTPS Only

Always use HTTPS in production. Never send tokens over HTTP.

```php
// Force HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
```

### 4. Input Validation

```php
function validateInput($data) {
    return [
        'username' => filter_var($data['username'], FILTER_SANITIZE_STRING),
        'email' => filter_var($data['email'], FILTER_VALIDATE_EMAIL),
        'password' => $data['password'] // Don't sanitize passwords
    ];
}
```

### 5. Rate Limiting

Prevent brute force attacks on login.

```php
function checkRateLimit($identifier) {
    $key = "login_attempts:$identifier";
    $attempts = getFromCache($key) ?? 0;
    
    if ($attempts >= 5) {
        $ttl = getCacheTTL($key);
        return error("Too many attempts. Try again in $ttl seconds", 429);
    }
    
    incrementCache($key, 300); // 5 minutes expiry
    return true;
}
```

### 6. SQL Injection Prevention

Always use prepared statements.

```php
// BAD - Vulnerable to SQL injection
$query = "SELECT * FROM users WHERE username = '$username'";

// GOOD - Using prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

### 7. XSS Prevention

```php
// Escape output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

### 8. CORS Configuration

```php
header('Access-Control-Allow-Origin: https://your-frontend-domain.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
```

## Database Schema for Authentication

```sql
-- Users table (already defined in DATABASE_SCHEMA.md)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Password reset tokens
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token)
);

-- Token blacklist (optional)
CREATE TABLE token_blacklist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    token VARCHAR(500) NOT NULL,
    blacklisted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    INDEX idx_token (token(255))
);
```

## Testing Authentication

### Test Cases

1. **Valid Login**
   - Input: Valid credentials
   - Expected: JWT token returned

2. **Invalid Password**
   - Input: Wrong password
   - Expected: 401 error

3. **Invalid Role**
   - Input: Student credentials with faculty role
   - Expected: 401 error

4. **Inactive Account**
   - Input: Deactivated user credentials
   - Expected: 403 error

5. **Token Expiration**
   - Input: Expired token
   - Expected: 401 error

6. **Invalid Token**
   - Input: Malformed token
   - Expected: 401 error

7. **Password Reset**
   - Input: Valid email
   - Expected: Reset email sent

8. **Rate Limiting**
   - Input: 6 failed login attempts
   - Expected: 429 error

## Environment Variables

Create a `.env` file:

```env
# JWT Configuration
JWT_SECRET_KEY=your_64_character_secret_key_here
JWT_EXPIRATION=86400

# Database
DB_HOST=localhost
DB_NAME=university_portal
DB_USER=root
DB_PASS=password

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=noreply@university.edu
SMTP_PASS=your_email_password
SMTP_FROM=noreply@university.edu

# Application
APP_URL=https://portal.university.edu
APP_ENV=production
```

## PHP Libraries Required

```bash
composer require firebase/php-jwt
composer require vlucas/phpdotenv
composer require phpmailer/phpmailer
```

## Complete Login Example (Laravel)

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:student,faculty,admin'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AUTH_FAILED',
                    'message' => 'Invalid credentials'
                ]
            ], 401);
        }

        if ($user->role !== $request->role) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_ROLE',
                    'message' => 'Invalid role selected'
                ]
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ACCOUNT_INACTIVE',
                    'message' => 'Account is deactivated'
                ]
            ], 403);
        }

        $token = $this->generateToken($user);
        $user->last_login = now();
        $user->save();

        $profile = $this->getUserProfile($user);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'profile' => $profile
                ]
            ],
            'message' => 'Login successful'
        ]);
    }

    private function generateToken($user)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60),
            'iss' => 'university-portal',
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ]
        ];

        return JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');
    }

    private function getUserProfile($user)
    {
        switch ($user->role) {
            case 'student':
                return $user->student;
            case 'faculty':
                return $user->faculty;
            default:
                return null;
        }
    }
}
```

---

**Document Version**: 1.0.0  
**Last Updated**: November 19, 2025
