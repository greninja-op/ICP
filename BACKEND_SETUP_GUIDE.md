# Backend Setup Guide - Quick Start

## ✅ Step 1: Backend Moved to Root
The backend folder has been moved from `Backend Integration/backend` to `./backend`

## 🗄️ Step 2: Database Setup

### Option A: Using MySQL Command Line
```bash
# 1. Create database
mysql -u root -p -e "CREATE DATABASE studentportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Import schema
mysql -u root -p studentportal < database/schema.sql

# 3. Import seed data (in order!)
mysql -u root -p studentportal < database/seeds/01_sessions.sql
mysql -u root -p studentportal < database/seeds/02_admin.sql
mysql -u root -p studentportal < database/seeds/03_teachers.sql
mysql -u root -p studentportal < database/seeds/04_students.sql
mysql -u root -p studentportal < database/seeds/05_subjects.sql
mysql -u root -p studentportal < database/seeds/06_fees.sql
mysql -u root -p studentportal < database/seeds/07_marks.sql
mysql -u root -p studentportal < database/seeds/08_attendance.sql
mysql -u root -p studentportal < database/seeds/09_notices.sql
```

### Option B: Using phpMyAdmin
1. Open phpMyAdmin (usually http://localhost/phpmyadmin)
2. Create new database: `studentportal`
3. Set collation: `utf8mb4_unicode_ci`
4. Import `database/schema.sql`
5. Import each seed file from `database/seeds/` in order (01-09)

### Option C: Using XAMPP/WAMP
1. Start MySQL from XAMPP/WAMP control panel
2. Open phpMyAdmin
3. Follow Option B steps

## ⚙️ Step 3: Configure Environment

Edit `backend/.env` file with your MySQL credentials:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=studentportal
DB_USER=root
DB_PASS=your_mysql_password_here

# JWT Configuration
JWT_SECRET=your_random_secret_key_here_make_it_long_and_random
JWT_EXPIRATION=86400

# Application
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# CORS
CORS_ORIGIN=http://localhost:5173
```

**Important**: 
- Replace `your_mysql_password_here` with your actual MySQL password
- Generate a random JWT_SECRET (at least 32 characters)

## 📦 Step 4: Install PHP Dependencies

```bash
cd backend
composer install
```

If you don't have Composer:
- Windows: Download from https://getcomposer.org/
- Mac: `brew install composer`
- Linux: `sudo apt install composer`

## 🚀 Step 5: Start Backend Server

```bash
cd backend
php -S localhost:8000
```

You should see:
```
PHP 8.x Development Server (http://localhost:8000) started
```

**Keep this terminal window open!**

## 🧪 Step 6: Test Backend

Open a new terminal and test:

```bash
curl -X POST http://localhost:8000/api/auth/login.php -H "Content-Type: application/json" -d "{\"username\":\"admin\",\"password\":\"admin123\"}"
```

Expected response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "username": "admin",
      "role": "admin"
    }
  }
}
```

## 🔗 Step 7: Connect React Frontend

The React frontend API service is already configured in:
`StudentPortal-React/src/services/api.js`

Just update the API_BASE_URL:
```javascript
const API_BASE_URL = 'http://localhost:8000/api';
```

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

## ✅ Success Checklist

- [ ] Database created
- [ ] Schema imported
- [ ] Seed data imported (all 9 files)
- [ ] .env configured with correct credentials
- [ ] Composer dependencies installed
- [ ] PHP server running on port 8000
- [ ] Test login works (returns token)
- [ ] React frontend API_BASE_URL updated

## 🐛 Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify credentials in backend/.env
- Test: `mysql -u root -p` (should connect)

### "composer: command not found"
- Install Composer: https://getcomposer.org/

### "Port 8000 already in use"
- Stop other services on port 8000
- Or use different port: `php -S localhost:8001`
- Update CORS_ORIGIN in .env and API_BASE_URL in React

### "CORS error in browser"
- Ensure backend .env has: `CORS_ORIGIN=http://localhost:5173`
- Ensure React runs on port 5173 (Vite default)

### "404 on all endpoints"
- Ensure you're in backend folder when starting server
- Check: `php -S localhost:8000` (not from root)

## 📝 Next Steps

1. Start React dev server: `npm run dev` (in StudentPortal-React folder)
2. Open browser: http://localhost:5173
3. Try logging in with test credentials
4. Check browser console for any errors
5. Check backend terminal for request logs

## 🎯 You're Done!

Once you see the login working and data loading from the backend, you're fully integrated!

The backend provides:
- ✅ Authentication (JWT)
- ✅ Student APIs (profile, marks, attendance, fees)
- ✅ Teacher APIs (students, marks, attendance)
- ✅ Admin APIs (manage students, teachers, subjects, fees)
- ✅ File uploads (images, PDFs)
- ✅ PDF generation (receipts, reports)

Happy coding! 🚀
