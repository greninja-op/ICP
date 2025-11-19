# 🧪 Comprehensive Testing Guide

## 🎯 Test Scenarios - Complete Walkthrough

### Prerequisites
- ✅ XAMPP running (Apache + MySQL)
- ✅ React dev server running (http://localhost:5173)
- ✅ Browser with Developer Tools (F12)

---

## Test Suite 1: Authentication

### Test 1.1: Admin Login
**Steps:**
1. Open http://localhost:5173
2. Enter credentials:
   - Username: `admin`
   - Password: `admin123`
   - Role: Admin
3. Click Login

**Expected Results:**
- ✅ No console errors
- ✅ Token stored in localStorage
- ✅ User data stored in localStorage
- ✅ Redirects to `/admin-dashboard`
- ✅ Shows "Admin Dashboard" header
- ✅ Displays admin name

**Verify in Browser:**
- F12 → Application → Local Storage
- Check `token` key exists
- Check `user` key exists with role: "admin"

### Test 1.2: Teacher Login
**Steps:**
1. Logout if logged in
2. Login with:
   - Username: `prof.sharma`
   - Password: `teacher123`
   - Role: Staff/Teacher

**Expected Results:**
- ✅ Redirects to `/teacher-dashboard`
- ✅ Shows "Teacher Dashboard" header
- ✅ Displays teacher name and department

### Test 1.3: Student Login
**Steps:**
1. Logout if logged in
2. Login with:
   - Username: `student001`
   - Password: `student123`
   - Role: Student

**Expected Results:**
- ✅ Redirects to `/dashboard`
- ✅ Shows "Dashboard" header
- ✅ Displays student name

---

## Test Suite 2: Admin Dashboard

### Test 2.1: Dashboard Statistics
**Steps:**
1. Login as admin
2. Observe dashboard cards

**Expected Results:**
- ✅ Total Students: **6**
- ✅ Total Teachers: **3**
- ✅ Total Courses: **30**
- ✅ Active Notices: **3**

**Verify in Network Tab:**
- F12 → Network
- See API calls to:
  - `/admin/students/list.php?limit=1`
  - `/admin/teachers/list.php?limit=1`
  - `/admin/subjects/list.php?limit=1`
  - `/notices/get_all.php`

### Test 2.2: Notices Carousel
**Steps:**
1. On admin dashboard
2. Observe notices section
3. Click left/right arrows
4. Click dots at bottom

**Expected Results:**
- ✅ Shows 3 notices
- ✅ Carousel auto-advances every 5 seconds
- ✅ Arrows navigate between notices
- ✅ Dots indicate current notice
- ✅ Smooth animations

**Notice Titles:**
1. "College Holiday - Republic Day"
2. "Annual Tech Fest 2025"
3. "Semester 6 Registration Open"

---

## Test Suite 3: Admin Student Management

### Test 3.1: View Students List
**Steps:**
1. Login as admin
2. Click "Manage Students" or navigate to `/admin/students`

**Expected Results:**
- ✅ Shows list of 6 students
- ✅ Each student card shows:
  - Student ID
  - Full name
  - Department
  - Semester
  - Email
  - Phone
- ✅ Edit and Delete buttons visible

**Verify Data:**
- Student IDs: STU2024001 to STU2024006
- Departments: All BCA
- Semesters: Mix of 1, 3, and 5

### Test 3.2: Add New Student
**Steps:**
1. On students page
2. Click "Add Student" button
3. Fill form:
   - Student ID: `STU2024007`
   - Full Name: `Test Student`
   - Username: `test.student`
   - Email: `test@studentportal.edu`
   - Password: `test123`
   - Department: BCA
   - Semester: 1
   - Phone: `9876543210`
   - Date of Birth: `2005-01-01`
   - Address: `Test Address`
4. Click Submit

**Expected Results:**
- ✅ Success toast appears
- ✅ Student list refreshes
- ✅ New student appears in list
- ✅ Form closes

**Verify in Network Tab:**
- POST to `/admin/students/create.php`
- Status: 200
- Response: `{"success": true}`

### Test 3.3: Edit Student
**Steps:**
1. Click Edit button on any student
2. Modify phone number
3. Click Update

**Expected Results:**
- ✅ Form pre-fills with student data
- ✅ Success toast on update
- ✅ List refreshes with new data
- ✅ Changes persist

**Verify in Network Tab:**
- POST to `/admin/students/update.php`
- Payload includes student_id

### Test 3.4: Delete Student
**Steps:**
1. Click Delete button on test student
2. Confirm deletion in modal

**Expected Results:**
- ✅ Confirmation modal appears
- ✅ Success toast on delete
- ✅ Student removed from list
- ✅ Count updates

**Verify in Network Tab:**
- POST to `/admin/students/delete.php`
- Payload: `{"student_id": "STU2024007"}`

---

## Test Suite 4: Student Dashboard

### Test 4.1: Dashboard Data
**Steps:**
1. Login as student001
2. Observe dashboard

**Expected Results:**
- ✅ Shows student name
- ✅ Shows profile picture or initial
- ✅ Displays notices (3 items)
- ✅ Shows quick stats (if available)

**Verify in Network Tab:**
- GET to `/student/get_profile.php`
- GET to `/student/get_marks.php`
- GET to `/student/get_attendance.php`
- GET to `/student/get_fees.php`
- GET to `/notices/get_all.php`

### Test 4.2: View Results
**Steps:**
1. Navigate to Results page
2. Observe marks display

**Expected Results:**
- ✅ Shows subject-wise marks
- ✅ Displays GPA/CGPA
- ✅ Shows semester breakdown

---

## Test Suite 5: Teacher Dashboard

### Test 5.1: View Students
**Steps:**
1. Login as prof.sharma
2. Navigate to student list

**Expected Results:**
- ✅ Shows students from teacher's department
- ✅ Filtered by BCA department
- ✅ Can search students

**Verify in Network Tab:**
- GET to `/teacher/get_students.php`
- Query params include department filter

---

## Test Suite 6: Error Handling

### Test 6.1: Invalid Login
**Steps:**
1. Try login with wrong password

**Expected Results:**
- ✅ Error message displayed
- ✅ No redirect
- ✅ Form stays on page

### Test 6.2: Network Error
**Steps:**
1. Stop XAMPP Apache
2. Try any operation

**Expected Results:**
- ✅ Error toast appears
- ✅ User-friendly message
- ✅ No crash

### Test 6.3: Token Expiration
**Steps:**
1. Manually delete token from localStorage
2. Try any authenticated operation

**Expected Results:**
- ✅ Redirects to login
- ✅ Shows "Unauthorized" message

---

## Test Suite 7: Cross-Browser Testing

### Test 7.1: Chrome
- ✅ All features work
- ✅ No console errors
- ✅ Smooth animations

### Test 7.2: Firefox
- ✅ All features work
- ✅ No console errors
- ✅ Smooth animations

### Test 7.3: Edge
- ✅ All features work
- ✅ No console errors
- ✅ Smooth animations

---

## 🐛 Known Issues to Watch For

### Issue 1: Rate Limiting
**Symptom:** "Too many requests" error
**Solution:** Wait 60 seconds between login attempts
**Limit:** 5 attempts per minute

### Issue 2: CORS Errors
**Symptom:** "Access blocked by CORS policy"
**Solution:** 
1. Check backend `.env` has `CORS_ORIGIN=*`
2. Restart Apache in XAMPP

### Issue 3: Empty Data
**Symptom:** Dashboard shows 0 for all stats
**Solution:**
1. Check token is stored
2. Verify API endpoints are accessible
3. Check database has data

---

## 📊 Test Results Template

```
Test Suite 1: Authentication
✅ Test 1.1: Admin Login - PASS
✅ Test 1.2: Teacher Login - PASS
✅ Test 1.3: Student Login - PASS

Test Suite 2: Admin Dashboard
✅ Test 2.1: Dashboard Statistics - PASS
✅ Test 2.2: Notices Carousel - PASS

Test Suite 3: Admin Student Management
✅ Test 3.1: View Students List - PASS
⏳ Test 3.2: Add New Student - PENDING
⏳ Test 3.3: Edit Student - PENDING
⏳ Test 3.4: Delete Student - PENDING

Test Suite 4: Student Dashboard
⏳ Test 4.1: Dashboard Data - PENDING
⏳ Test 4.2: View Results - PENDING

Test Suite 5: Teacher Dashboard
⏳ Test 5.1: View Students - PENDING

Test Suite 6: Error Handling
⏳ Test 6.1: Invalid Login - PENDING
⏳ Test 6.2: Network Error - PENDING
⏳ Test 6.3: Token Expiration - PENDING
```

---

## 🎯 Success Criteria

**Integration is successful if:**
- ✅ All logins work (3/3 roles)
- ✅ Admin dashboard shows real data
- ✅ Student list loads from database
- ✅ CRUD operations work (add/edit/delete)
- ✅ No console errors
- ✅ Smooth user experience

**Current Status: 75% Complete**

---

**Happy Testing! 🚀**
