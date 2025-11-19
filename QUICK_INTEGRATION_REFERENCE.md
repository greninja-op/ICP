# Quick Integration Reference Card

## 🚀 Quick Start

### Start Everything
1. **XAMPP**: Open XAMPP Control Panel → Start Apache & MySQL
2. **React**: Already running at http://localhost:5173

### Test Login
```
URL: http://localhost:5173
Username: admin
Password: admin123
Role: Admin
```

## 📍 Important Locations

### Backend
```
Physical: C:\xampp\htdocs\university_portal\backend
URL: http://localhost/university_portal/backend/api
```

### Database
```
Host: localhost:3306
Database: studentportal
User: root
Password: (empty)
```

### Frontend
```
Physical: StudentPortal-React/
URL: http://localhost:5173
```

## 🔑 All Test Credentials

| Role | Username | Password | Notes |
|------|----------|----------|-------|
| Admin | admin | admin123 | Full access |
| Teacher | prof.sharma | teacher123 | BCA Department |
| Teacher | prof.patel | teacher123 | BCA Department |
| Teacher | prof.kumar | teacher123 | BCA Department |
| Student | student001 | student123 | Semester 5, BCA |
| Student | student002 | student123 | Semester 5, BCA |
| Student | student003 | student123 | Semester 3, BCA |
| Student | student004 | student123 | Semester 3, BCA |
| Student | student005 | student123 | Semester 1, BCA |
| Student | student006 | student123 | Semester 1, BCA |

## 🧪 Quick API Tests

### Test Login (PowerShell)
```powershell
curl http://localhost/university_portal/backend/api/auth/login.php -Method POST -ContentType "application/json" -Body '{"username":"admin","password":"admin123"}'
```

### Check Database
```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT username, role FROM studentportal.users;"
```

## 🔧 Quick Fixes

### CORS Error
```bash
# Edit: C:\xampp\htdocs\university_portal\backend\.env
CORS_ORIGIN=*
# Then restart Apache in XAMPP
```

### Database Connection Error
```bash
# Check MySQL is running in XAMPP
# Verify database exists:
C:\xampp\mysql\bin\mysql.exe -u root -e "SHOW DATABASES LIKE 'studentportal';"
```

### React Not Loading
```bash
# Check if dev server is running
# Restart if needed:
cd StudentPortal-React
npm run dev
```

## 📊 What's Working

✅ XAMPP setup
✅ Database with test data
✅ Backend API deployed
✅ Login endpoint
✅ JWT token generation
✅ Frontend API configuration
✅ Token storage

## 📝 What's Next

⏳ Test login from UI
⏳ Dashboard data loading
⏳ CRUD operations
⏳ File uploads
⏳ Error handling

## 🆘 Troubleshooting

### Login Fails
1. Check XAMPP Apache is running
2. Check browser console (F12)
3. Check network tab for API call
4. Verify credentials are correct

### No Data in Dashboard
1. Login must work first
2. Check token is in localStorage
3. Check API endpoints are called
4. Verify backend returns data

### XAMPP Issues
1. Stop and restart Apache
2. Check port 80 is not in use
3. Check MySQL is running
4. Verify database exists

## 📞 Key Files

### Configuration
- `backend/.env` - Backend config
- `StudentPortal-React/src/services/api.js` - API service

### Documentation
- `BACKEND_INTEGRATION_STATUS.md` - Full status
- `TEST_LOGIN.md` - Testing guide
- `INTEGRATION_COMPLETE_SUMMARY.md` - Session summary

---

**Quick Tip**: Press F12 in browser to see console and network activity!
