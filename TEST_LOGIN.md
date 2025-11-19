# Login Integration Test Guide

## 🧪 Testing the Backend Integration

### Step 1: Verify Services are Running

1. **Check XAMPP**
   - Open XAMPP Control Panel
   - Ensure Apache is running (green)
   - Ensure MySQL is running (green)

2. **Check React Dev Server**
   - Should be running on `http://localhost:5173`
   - Check terminal for any errors

### Step 2: Test Login Flow

1. **Open the React App**
   ```
   http://localhost:5173
   ```

2. **Try Admin Login**
   - Username: `admin`
   - Password: `admin123`
   - Role: Select "Admin"
   - Click Login

3. **Expected Behavior**
   - ✅ Login should succeed
   - ✅ JWT token stored in localStorage
   - ✅ User data stored in localStorage
   - ✅ Redirect to Admin Dashboard

4. **Check Browser Console**
   - Press F12 to open Developer Tools
   - Go to Console tab
   - Look for any errors (should be none)
   - Go to Application > Local Storage
   - Verify `user` and `token` are stored

### Step 3: Test Other Credentials

**Teacher Login:**
- Username: `prof.sharma`
- Password: `teacher123`
- Role: Staff/Teacher

**Student Login:**
- Username: `student001`
- Password: `student123`
- Role: Student

### Step 4: Verify API Calls

Open Browser Network Tab (F12 > Network):

1. **Login Request**
   - URL: `http://localhost/university_portal/backend/api/auth/login.php`
   - Method: POST
   - Status: 200 OK
   - Response should contain:
     ```json
     {
       "success": true,
       "message": "Login successful",
       "data": {
         "user": {...},
         "token": "eyJ0eXAi..."
       }
     }
     ```

### Common Issues & Solutions

#### Issue 1: CORS Error
**Error**: "Access to fetch blocked by CORS policy"

**Solution**:
1. Check backend `.env` file has `CORS_ORIGIN=*`
2. Restart Apache in XAMPP
3. Clear browser cache

#### Issue 2: Network Error
**Error**: "Failed to fetch" or "Network error"

**Solution**:
1. Verify XAMPP Apache is running
2. Test API directly: `http://localhost/university_portal/backend/api/auth/login.php`
3. Check firewall settings

#### Issue 3: Invalid Credentials
**Error**: "Invalid username or password"

**Solution**:
1. Verify you're using correct credentials
2. Check database has correct password hash:
   ```sql
   SELECT username, LEFT(password, 20) FROM studentportal.users WHERE username='admin';
   ```

#### Issue 4: Token Not Stored
**Error**: Login succeeds but token not in localStorage

**Solution**:
1. Check browser console for JavaScript errors
2. Verify `api.js` is saving token correctly
3. Check browser localStorage is enabled

### Manual API Test (Backup)

If React login doesn't work, test API directly:

```powershell
curl http://localhost/university_portal/backend/api/auth/login.php -Method POST -ContentType "application/json" -Body '{"username":"admin","password":"admin123"}'
```

Expected response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "1",
      "username": "admin",
      "email": "admin@studentportal.edu",
      "role": "admin",
      "status": "active"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

### Success Criteria

- ✅ Login form submits without errors
- ✅ API returns 200 status
- ✅ Token is stored in localStorage
- ✅ User data is stored in localStorage
- ✅ User is redirected to appropriate dashboard
- ✅ No console errors

### Next Steps After Successful Login

Once login works:
1. Test dashboard data loading
2. Integrate other API endpoints
3. Test CRUD operations
4. Add error handling
5. Implement token refresh

---

**Test Date**: November 19, 2025
**Status**: Ready for Testing
