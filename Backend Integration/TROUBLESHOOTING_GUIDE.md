# Troubleshooting Guide

## 🔍 Quick Diagnostics

Run these commands to check your setup:

```bash
# Check PHP version
php --version

# Check if PHP server is running
curl http://localhost:8000/api/auth/login.php

# Check MySQL connection
mysql -u root -p -e "USE studentportal; SHOW TABLES;"

# Check backend logs
tail -f backend/logs/error.log
```

---

## 🚨 Common Errors & Solutions

### 1. "Database connection failed"

**Symptoms:**
- 500 error on all endpoints
- "Could not connect to database" message

**Causes:**
- MySQL not running
- Wrong credentials in .env
- Database doesn't exist

**Solutions:**
```bash
# Check if MySQL is running
# Windows (XAMPP): Start MySQL from XAMPP Control Panel
# Mac: brew services start mysql
# Linux: sudo systemctl start mysql

# Verify credentials
mysql -u root -p

# Create database if missing
mysql -u root -p -e "CREATE DATABASE studentportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Check .env file
cat backend/.env
# Ensure DB_HOST, DB_NAME, DB_USER, DB_PASS are correct
```

---

### 2. "CORS policy: No 'Access-Control-Allow-Origin' header"

**Symptoms:**
- Browser console shows CORS error
- Request blocked by browser

**Causes:**
- Backend not running
- Frontend using wrong port
- CORS not configured for your domain

**Solutions:**
```bash
# 1. Ensure backend is running on port 8000
php -S localhost:8000

# 2. Check frontend .env
cat .env
# Should be: VITE_API_URL=http://localhost:8000/api

# 3. If using different port, update backend/includes/cors.php
# Add your frontend URL to allowed origins
```

---

### 3. "401 Unauthorized" on protected endpoints

**Symptoms:**
- Login works but other endpoints return 401
- "Invalid or missing token" error

**Causes:**
- Token not included in request
- Wrong Authorization header format
- Token expired or blacklisted

**Solutions:**
```javascript
// ❌ WRONG
headers: {
  'Authorization': token
}

// ✅ CORRECT
headers: {
  'Authorization': `Bearer ${token}`
}

// Check if token exists
console.log(localStorage.getItem('token'));

// Test token validity
fetch('http://localhost:8000/api/auth/verify.php', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('token')}`
  }
})
.then(res => res.json())
.then(data => console.log(data));
```

---

### 4. "404 Not Found" on all endpoints

**Symptoms:**
- All API calls return 404
- "File not found" error

**Causes:**
- PHP server not running
- Wrong base URL
- Incorrect file paths

**Solutions:**
```bash
# 1. Check if PHP server is running
ps aux | grep php
# or on Windows: tasklist | findstr php

# 2. Start PHP server from backend directory
cd backend
php -S localhost:8000

# 3. Test with curl
curl http://localhost:8000/api/auth/login.php

# 4. Verify frontend API URL
console.log(import.meta.env.VITE_API_URL);
# Should be: http://localhost:8000/api
```

---

### 5. "500 Internal Server Error"

**Symptoms:**
- Generic 500 error
- No specific error message

**Causes:**
- PHP syntax error
- Database query error
- Missing dependencies
- File permission issues

**Solutions:**
```bash
# 1. Check error log (MOST IMPORTANT!)
tail -f backend/logs/error.log

# 2. Enable PHP error display (development only)
# Edit php.ini:
display_errors = On
error_reporting = E_ALL

# 3. Check file permissions
chmod -R 755 backend/
chmod -R 777 backend/uploads/
chmod -R 777 backend/logs/

# 4. Verify composer dependencies
cd backend
composer install

# 5. Test PHP syntax
php -l backend/api/auth/login.php
```

---

### 6. "Too Many Requests (429)"

**Symptoms:**
- Login fails after multiple attempts
- "Too many requests" error

**Causes:**
- Rate limit exceeded (5 login attempts per minute)

**Solutions:**
```bash
# Wait 60 seconds and try again

# OR restart PHP server to reset rate limiter
# Stop server (Ctrl+C)
php -S localhost:8000

# OR clear rate limit from database
mysql -u root -p studentportal -e "DELETE FROM rate_limits WHERE ip = 'your_ip';"
```

---

### 7. File Upload Fails

**Symptoms:**
- "File upload failed" error
- 413 Request Entity Too Large
- 500 error on upload

**Causes:**
- File too large (>5MB)
- Wrong file type
- Upload directory not writable
- PHP upload limits too low

**Solutions:**
```bash
# 1. Check file size (must be < 5MB)
ls -lh your_file.jpg

# 2. Check file type (must be JPG, PNG, GIF, WEBP)
file your_file.jpg

# 3. Check directory permissions
chmod 777 backend/uploads/profiles/
chmod 777 backend/uploads/assignments/
chmod 777 backend/uploads/receipts/

# 4. Check PHP upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# If too low, edit php.ini:
upload_max_filesize = 10M
post_max_size = 10M

# Then restart PHP server
```

---

### 8. "Invalid credentials" but password is correct

**Symptoms:**
- Login fails with correct credentials
- "Invalid username or password" error

**Causes:**
- Seed data not imported
- Password hash mismatch
- Wrong username format

**Solutions:**
```bash
# 1. Verify user exists in database
mysql -u root -p studentportal -e "SELECT username, role FROM users;"

# 2. Check if seed data was imported
mysql -u root -p studentportal -e "SELECT COUNT(*) FROM users;"
# Should return at least 3 (admin, student, teacher)

# 3. Re-import seed data
mysql -u root -p studentportal < database/seeds/02_admin.sql
mysql -u root -p studentportal < database/seeds/03_teachers.sql
mysql -u root -p studentportal < database/seeds/04_students.sql

# 4. Test with default credentials:
# admin / admin123
# STU001 / password123
# TCH001 / password123
```

---

### 9. Date Format Errors

**Symptoms:**
- "Invalid date format" error
- Dates not saving correctly

**Causes:**
- Wrong date format sent to API
- Backend expects YYYY-MM-DD

**Solutions:**
```javascript
// ❌ WRONG
const date = new Date().toString(); // "Wed Nov 19 2025..."

// ✅ CORRECT
const date = new Date().toISOString().split('T')[0]; // "2025-11-19"

// Helper function
const formatDate = (date) => {
  const d = new Date(date);
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};
```

---

### 10. "Role mismatch" error on login

**Symptoms:**
- Login fails with "role mismatch" message
- User exists but can't login

**Causes:**
- Trying to login as wrong role
- Frontend sending role parameter that doesn't match user's actual role

**Solutions:**
```javascript
// Option 1: Don't send role parameter
fetch('/api/auth/login.php', {
  method: 'POST',
  body: JSON.stringify({
    username: 'admin',
    password: 'admin123'
    // Don't include role
  })
});

// Option 2: Send correct role
// Check user's actual role in database first
fetch('/api/auth/login.php', {
  method: 'POST',
  body: JSON.stringify({
    username: 'admin',
    password: 'admin123',
    role: 'admin' // Must match user's actual role
  })
});
```

---

## 🔧 Advanced Debugging

### Enable Detailed Error Logging

Edit `backend/includes/functions.php`:

```php
// Find logError function and add:
error_log(print_r($context, true));
```

### Test Individual Endpoints

```bash
# Save token to variable
TOKEN="your_jwt_token_here"

# Test student profile
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/student/get_profile.php

# Test with verbose output
curl -v -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/student/get_profile.php
```

### Check Database Queries

```bash
# Enable MySQL query log
mysql -u root -p -e "SET GLOBAL general_log = 'ON';"

# View queries in real-time
tail -f /var/log/mysql/mysql.log

# Disable when done
mysql -u root -p -e "SET GLOBAL general_log = 'OFF';"
```

### Monitor Network Requests

In browser DevTools:
1. Open Network tab
2. Filter by "Fetch/XHR"
3. Click on request
4. Check:
   - Request URL
   - Request Headers (Authorization?)
   - Request Payload
   - Response Status
   - Response Headers
   - Response Body

---

## 📊 Health Check Script

Create `backend/health_check.php`:

```php
<?php
require_once 'config/database.php';

$checks = [
    'PHP Version' => phpversion(),
    'MySQL Extension' => extension_loaded('pdo_mysql') ? 'OK' : 'MISSING',
    'Database Connection' => 'Checking...',
    'Uploads Directory' => is_writable('uploads/') ? 'Writable' : 'Not Writable',
    'Logs Directory' => is_writable('logs/') ? 'Writable' : 'Not Writable',
];

try {
    $db = new Database();
    $conn = $db->getConnection();
    $checks['Database Connection'] = $conn ? 'OK' : 'FAILED';
} catch (Exception $e) {
    $checks['Database Connection'] = 'FAILED: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($checks, JSON_PRETTY_PRINT);
```

Access: `http://localhost:8000/health_check.php`

---

## 🆘 Still Stuck?

### Collect This Information:

1. **Error Message:**
   ```
   Copy exact error from browser console or backend logs
   ```

2. **Request Details:**
   ```
   URL: http://localhost:8000/api/...
   Method: POST/GET
   Headers: { ... }
   Body: { ... }
   ```

3. **Backend Logs:**
   ```bash
   tail -20 backend/logs/error.log
   ```

4. **Environment:**
   ```bash
   php --version
   mysql --version
   node --version
   ```

5. **What You Were Trying To Do:**
   ```
   Step-by-step description
   ```

### Contact Project Owner With Above Information

---

## ✅ Verification Checklist

Run through this to verify everything works:

```bash
# 1. Backend running
curl http://localhost:8000/api/auth/login.php
# Should NOT return 404

# 2. Database accessible
mysql -u root -p studentportal -e "SELECT COUNT(*) FROM users;"
# Should return number > 0

# 3. Login works
curl -X POST http://localhost:8000/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
# Should return success:true with token

# 4. Protected endpoint works
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/student/get_profile.php
# Should return profile data

# 5. File permissions
ls -la backend/uploads/
ls -la backend/logs/
# Should show writable directories
```

If all 5 checks pass, your backend is working correctly! ✅

The issue is likely in your frontend code.
