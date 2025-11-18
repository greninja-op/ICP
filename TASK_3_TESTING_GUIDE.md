# Task 3 Testing Guide

## Authentication System Testing

This guide explains how to test the authentication system implemented in Task 3.

### Prerequisites

1. **Database Setup**: Ensure Task 2 is completed and the database is seeded
2. **Composer Dependencies**: Run `composer install` to install required packages
3. **Environment Configuration**: Copy `.env.example` to `.env` and configure database credentials
4. **Web Server**: Ensure Apache/Nginx is running and pointing to the `public/` directory

### Testing Methods

#### Method 1: Command Line Test (Recommended)

Run the test script to verify core components:

```bash
php test-auth-system.php
```

This will test:
- Database connection
- User model operations
- Password hashing and verification
- Authentication service
- CSRF token generation
- Middleware instantiation

#### Method 2: Web Browser Test

1. **Start the web server** (if using the built-in PHP server):
   ```bash
   php -S localhost:8000 -t public
   ```

2. **Access the login page**:
   - Open browser to `http://localhost:8000/login`
   - You should see the modern glassmorphism login page

3. **Test login with seeded credentials**:
   
   **Admin User:**
   - Username: `admin`
   - Password: `admin123`
   - Role: Admin
   
   **Student User:**
   - Username: `student1`
   - Password: `password123`
   - Role: Student
   
   **Teacher User:**
   - Username: `teacher1`
   - Password: `password123`
   - Role: Teacher

4. **Test authentication features**:
   - ✅ Valid credentials should log you in
   - ✅ Invalid credentials should show error message
   - ✅ Wrong role selection should fail
   - ✅ CSRF token should be validated
   - ✅ Remember me should save role preference

5. **Test rate limiting**:
   - Try logging in with wrong password 5 times
   - You should be locked out for 15 minutes
   - Error message should show remaining time

6. **Test session timeout**:
   - Log in successfully
   - Wait 30 minutes (or change `SESSION_LIFETIME` in .env to 1 minute for testing)
   - Try to access a protected page
   - You should be redirected to login

7. **Test logout**:
   - Log in successfully
   - Access `/logout`
   - You should be redirected to login page
   - Session should be destroyed

### Testing Checklist

#### Database Connection
- [ ] Database connection established successfully
- [ ] Prepared statements work correctly
- [ ] Error logging works (check `logs/database.log`)

#### User Model
- [ ] Can find user by username
- [ ] Can find user by email
- [ ] Can find user by username and role
- [ ] Can create new user
- [ ] Can update user
- [ ] Validation works (email, username length, role)
- [ ] Duplicate username/email prevented

#### Password Security
- [ ] Passwords hashed with bcrypt cost 12
- [ ] Password verification works
- [ ] Wrong passwords rejected
- [ ] Hash format starts with `$2y$12$`

#### Authentication Service
- [ ] Login with valid credentials succeeds
- [ ] Login with invalid credentials fails
- [ ] Login with wrong role fails
- [ ] Inactive users cannot log in
- [ ] Session created on successful login
- [ ] Session contains correct user data
- [ ] Session timeout works (30 minutes)
- [ ] Logout destroys session

#### CSRF Protection
- [ ] CSRF token generated
- [ ] CSRF token validated correctly
- [ ] Invalid tokens rejected
- [ ] Token expiry works (1 hour)
- [ ] Token included in forms

#### Rate Limiting
- [ ] Login attempts tracked
- [ ] Lockout after 5 failed attempts
- [ ] Lockout lasts 15 minutes
- [ ] Attempts cleared on successful login
- [ ] Remaining time displayed

#### Middleware
- [ ] AuthMiddleware blocks unauthenticated users
- [ ] RoleMiddleware blocks unauthorized roles
- [ ] CsrfMiddleware validates tokens
- [ ] Intended URL preserved for redirect

#### Controllers
- [ ] AuthController handles login
- [ ] AuthController handles logout
- [ ] AuthController handles forgot password
- [ ] BaseController provides common functionality
- [ ] JSON responses formatted correctly
- [ ] Redirects work correctly

#### Views
- [ ] Login page displays correctly
- [ ] Role selector works
- [ ] Form validation works
- [ ] AJAX submission works
- [ ] Remember me works
- [ ] Forgot password page displays

### Common Issues and Solutions

#### Issue: "Database connection failed"
**Solution**: 
- Check `.env` file has correct database credentials
- Ensure MySQL service is running
- Verify database exists: `CREATE DATABASE university_portal;`

#### Issue: "No admin user found"
**Solution**:
- Run database seeder: `php database/seeds/seed.php`
- Or manually create a user in the database

#### Issue: "CSRF token validation failed"
**Solution**:
- Ensure session is started
- Check that form includes CSRF token
- Verify token hasn't expired (1 hour default)

#### Issue: "Class not found"
**Solution**:
- Run `composer install` to install dependencies
- Run `composer dump-autoload` to regenerate autoloader

#### Issue: "Session not working"
**Solution**:
- Ensure `session_start()` is called
- Check session directory is writable
- Verify session cookie settings in `.env`

### Manual Testing Scenarios

#### Scenario 1: Successful Login Flow
1. Navigate to `/login`
2. Select "Student" role
3. Enter username: `student1`
4. Enter password: `password123`
5. Click "Sign In"
6. **Expected**: Redirect to `/dashboard` (will be implemented in later tasks)

#### Scenario 2: Failed Login - Wrong Password
1. Navigate to `/login`
2. Select any role
3. Enter valid username
4. Enter wrong password
5. Click "Sign In"
6. **Expected**: Error message "Invalid credentials"

#### Scenario 3: Failed Login - Wrong Role
1. Navigate to `/login`
2. Select "Admin" role
3. Enter student username and password
4. Click "Sign In"
5. **Expected**: Error message "Invalid credentials"

#### Scenario 4: Rate Limiting
1. Navigate to `/login`
2. Enter wrong password 5 times
3. Try to login again
4. **Expected**: Error message with lockout time remaining

#### Scenario 5: Session Timeout
1. Log in successfully
2. Wait 30 minutes (or change `SESSION_LIFETIME` to 1 for testing)
3. Try to access any protected route
4. **Expected**: Redirect to login page

#### Scenario 6: Remember Me
1. Navigate to `/login`
2. Select "Teacher" role
3. Check "Remember my role"
4. Log in
5. Log out
6. Navigate to `/login` again
7. **Expected**: "Teacher" role should be pre-selected

### Security Testing

#### Test 1: SQL Injection Prevention
Try entering SQL injection payloads in username field:
- `admin' OR '1'='1`
- `admin'; DROP TABLE users; --`
- **Expected**: All should be safely handled by prepared statements

#### Test 2: XSS Prevention
Try entering XSS payloads:
- `<script>alert('XSS')</script>`
- **Expected**: Should be escaped and displayed as text

#### Test 3: CSRF Protection
Try submitting login form without CSRF token:
- Remove CSRF token from form
- Submit form
- **Expected**: 403 Forbidden error

#### Test 4: Session Fixation
Try reusing old session ID:
- Get session ID before login
- Log in
- Check session ID after login
- **Expected**: Session ID should change (regenerated)

### Performance Testing

#### Test 1: Database Query Performance
- Check `logs/database.log` for slow queries
- All queries should execute in < 100ms

#### Test 2: Password Hashing Performance
- Bcrypt with cost 12 should take ~100-300ms
- This is intentional for security

#### Test 3: Session Performance
- Session operations should be fast (< 10ms)
- Check session file size is reasonable

### Logs to Monitor

1. **Database Errors**: `logs/database.log`
2. **Authentication Events**: `logs/auth.log`
3. **Application Exceptions**: `logs/exceptions.log`
4. **PHP Errors**: `logs/php-errors.log`

### Next Steps After Testing

Once all tests pass:
1. Proceed to Task 4: Checkpoint - Ensure all tests pass
2. Implement Task 5: Routing and Request Handling
3. Continue with remaining tasks in the implementation plan

### Troubleshooting Commands

```bash
# Check PHP version (should be 8.1+)
php -v

# Check if Composer is installed
composer --version

# Install dependencies
composer install

# Regenerate autoloader
composer dump-autoload

# Check database connection
php database/test-connection.php

# Run database migrations
php database/migrate.php

# Seed database with test data
php database/seeds/seed.php

# Test authentication system
php test-auth-system.php
```

### Support

If you encounter issues:
1. Check the logs in the `logs/` directory
2. Verify all prerequisites are met
3. Review the error messages carefully
4. Check the TASK_3_COMPLETION.md for implementation details

