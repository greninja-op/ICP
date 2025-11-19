# 🚀 Quick Start - Student Portal

## Step 1: Setup Database (One Time Only)

**Double-click:** `SETUP_DATABASE.bat`

This will:
- Create the `studentportal` database
- Import all tables
- Import test data

**Note:** You'll be prompted for your MySQL password (usually empty for XAMPP)

## Step 2: Start Backend Server

**Double-click:** `START_BACKEND.bat`

Keep this window open! Backend runs on http://localhost:8000

## Step 3: Start Frontend (React)

**Double-click:** `START_DEV_SERVER.ps1` (in StudentPortal-React folder)

Or manually:
```bash
cd StudentPortal-React
npm run dev
```

Frontend runs on http://localhost:5173

## Step 4: Login

Open browser: http://localhost:5173

**Test Credentials:**

**Admin:**
- Username: `admin`
- Password: `admin123`

**Student:**
- Username: `STU001`
- Password: `password123`

**Teacher:**
- Username: `TCH001`
- Password: `password123`

## ✅ You're Done!

Both servers should be running:
- ✅ Backend: http://localhost:8000 (keep terminal open)
- ✅ Frontend: http://localhost:5173 (keep terminal open)

## 🐛 Troubleshooting

**"MySQL not found"**
- Install XAMPP or MySQL
- Make sure MySQL service is running

**"Database connection failed"**
- Check MySQL is running
- Edit `backend/.env` if your MySQL has a password

**"CORS error"**
- Make sure backend is running on port 8000
- Make sure frontend is running on port 5173

**"Port already in use"**
- Close other applications using ports 8000 or 5173
- Or change ports in configuration

## 📝 Daily Workflow

1. Start MySQL (if not auto-started)
2. Run `START_BACKEND.bat`
3. Run `START_DEV_SERVER.ps1`
4. Start coding!

Happy coding! 🎉
