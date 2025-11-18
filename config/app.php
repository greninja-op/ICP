<?php

return [
    'name' => getenv('APP_NAME') ?: 'University Portal',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
    
    'session' => [
        'lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 30),
        'driver' => getenv('SESSION_DRIVER') ?: 'file',
        'secure' => filter_var(getenv('SESSION_SECURE_COOKIE'), FILTER_VALIDATE_BOOLEAN),
        'http_only' => true,
        'same_site' => 'lax',
    ],
    
    'security' => [
        'csrf_token_expiry' => (int) (getenv('CSRF_TOKEN_EXPIRY') ?: 3600),
        'password_bcrypt_cost' => (int) (getenv('PASSWORD_BCRYPT_COST') ?: 12),
        'rate_limit' => [
            'login_attempts' => (int) (getenv('RATE_LIMIT_LOGIN_ATTEMPTS') ?: 5),
            'login_window' => (int) (getenv('RATE_LIMIT_LOGIN_WINDOW') ?: 900),
        ],
    ],
    
    'upload' => [
        'max_size' => (int) (getenv('UPLOAD_MAX_SIZE') ?: 2097152), // 2MB
        'allowed_types' => explode(',', getenv('UPLOAD_ALLOWED_TYPES') ?: 'jpg,jpeg,png,webp,pdf'),
        'path' => getenv('UPLOAD_PATH') ?: 'public/uploads',
    ],
    
    'logging' => [
        'channel' => getenv('LOG_CHANNEL') ?: 'file',
        'level' => getenv('LOG_LEVEL') ?: 'debug',
        'path' => getenv('LOG_PATH') ?: 'logs/app.log',
    ],
];
