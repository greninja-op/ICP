# Developer Quick Start Guide

## 🚀 Get Started in 15 Minutes

### Step 1: Install Requirements (5 mins)
```bash
# Check if you have these installed:
php --version    # Need 7.4+
mysql --version  # Need 8.0+
node --version   # Need 16+
composer --version
```

If missing, install:
- **Windows**: Download XAMPP (includes PHP + MySQL)
- **Mac**: `brew install php mysql composer`
- **Linux**: `sudo apt install php mysql-server composer`

### Step 2: Setup Backend (5 mins)
```bash
# 1. Navigate to backend
cd backend

# 2. Copy environment file
cp .env.example .env

# 3. Edit .env (use your MySQL credentials)
# DB_HOST=localhost
# DB_NAME=studentportal
# DB_USER=root
# DB_PASS=your_password
# JWT_SECRET=your_random_secret_key_here

# 4. Install dependencies
composer install

# 5. Create database
mysql -u root -p -e "CREATE DATABASE studentportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Import schema
mysql -u root -p studentportal < ../database/schema.sql

# 7. Import seed data (in order!)
mysql -u root -p studentportal < ../database/seeds/01_sessions.sql
mysql -u root -p studentportal < ../database/seeds/02_admin.sql
mysql -u root -p studentportal < ../database/seeds/03_teachers.sql
mysql -u root -p studentportal < ../database/seeds/04_students.sql
mysql -u root -p studentportal < ../database/seeds/05_subjects.sql
mysql -u root -p studentportal < ../database/seeds/06_fees.sql
mysql -u root -p studentportal < ../database/seeds/07_marks.sql
mysql -u root -p studentportal < ../database/seeds/08_attendance.sql
mysql -u root -p studentportal < ../database/seeds/09_notices.sql

# 8. Start PHP server
php -S localhost:8000
```

### Step 3: Test Backend (5 mins)
```bash
# Test login endpoint
curl -X POST http://localhost:8000/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# Should return:
# {"success":true,"message":"Login successful","data":{"token":"...","user":{...}}}
```

If you see the success response, **backend is working!** ✅

---

## 🎨 Frontend Integration

### Setup Your React Project
```bash
# Install axios
npm install axios

# Create .env file
echo "VITE_API_URL=http://localhost:8000/api" > .env
```

### Create API Service File
Create `src/services/api.js` - **Copy from FRONTEND_INTEGRATION.md Section 18**

### Test API Connection
```javascript
// In your component
import { authAPI } from './services/api';

authAPI.login('admin', 'admin123')
  .then(res => console.log(res.data))
  .catch(err => console.error(err));
```

---

## 🔑 Test Credentials

**Admin:**
- Username: `admin`
- Password: `admin123`

**Student:**
- Username: `STU001`
- Password: `password123`

**Teacher:**
- Username: `TCH001`
- Password: `password123`

---

## 📚 Key Documents to Read

1. **FRONTEND_INTEGRATION.md** - Complete API reference (READ THIS FIRST!)
2. **structure.md** - Project structure
3. **tech.md** - Technology stack
4. **product.md** - Product requirements

---

## 🔧 Common Issues & Fixes

### Issue: "Database connection failed"
**Fix:** Check .env credentials, ensure MySQL is running

### Issue: "CORS error"
**Fix:** Backend must run on port 8000, frontend on 5173

### Issue: "Token doesn't work"
**Fix:** Use `Authorization: Bearer <token>` format

### Issue: "404 on all endpoints"
**Fix:** Ensure PHP server is running: `php -S localhost:8000`

### Issue: "500 Internal Server Error"
**Fix:** Check `backend/logs/error.log` for details

---

## 📋 API Endpoints Quick Reference

### Authentication
- `POST /auth/login.php` - Login
- `POST /auth/logout.php` - Logout
- `GET /auth/verify.php` - Verify token

### Student
- `GET /student/get_profile.php` - Get profile
- `GET /student/get_marks.php?semester=1` - Get marks
- `GET /student/get_attendance.php` - Get attendance
- `GET /student/get_fees.php` - Get fees
- `GET /student/get_payments.php` - Get payments

### Teacher
- `GET /teacher/get_students.php` - List students
- `POST /teacher/mark_attendance.php` - Mark attendance
- `POST /teacher/enter_marks.php` - Enter marks

### Admin
- `GET /admin/students/list.php` - List students
- `POST /admin/students/create.php` - Create student
- `GET /admin/teachers/list.php` - List teachers
- `GET /admin/subjects/list.php` - List subjects
- `GET /admin/fees/list.php` - List fees
- `POST /admin/payments/process.php` - Process payment

**Full list in FRONTEND_INTEGRATION.md Section 6**

---

## ✅ Integration Checklist

- [ ] Backend running on port 8000
- [ ] Database imported with seed data
- [ ] Test login with curl (works)
- [ ] Frontend .env configured
- [ ] API service file created
- [ ] Test API call from frontend (works)
- [ ] Build login page
- [ ] Implement token storage
- [ ] Create protected routes
- [ ] Test all three roles

---

## 🆘 Need Help?

1. Check `backend/logs/error.log`
2. Test endpoint with Postman/curl
3. Review FRONTEND_INTEGRATION.md
4. Contact project owner with:
   - Specific error message
   - Request ID (from response header)
   - What you were trying to do

---

## 🎯 Success Criteria

You're on track if:
- ✅ Login works for all roles
- ✅ Token authentication works
- ✅ Data displays from API
- ✅ CRUD operations work
- ✅ No CORS errors
- ✅ Error messages are clear

**Expected completion: 1-2 weeks for full frontend**

Good luck! 🚀
