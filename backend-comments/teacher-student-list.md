# Teacher Student List - Backend Integration Tasks

## File: `StudentPortal-React/src/pages/TeacherStudentList.jsx`

---

## IMPORTANT SECURITY RULE
**Teachers can ONLY view students from their own department.**

When a teacher logs in, the backend must:
1. Get the teacher's department from the `faculty` table
2. Filter all student queries to ONLY show students from that department
3. Never allow teachers to see students from other departments

---

## 1. Get Students for Teacher
**API Endpoint:** `GET /api/teacher/students`

**Authentication:** JWT token required (teacher role)

**Query Parameters:**
- `semester` (optional) - Filter by semester (1-6)
- `batch` (optional) - Filter by admission year (2024, 2023, etc.)
- `search` (optional) - Search by name or roll number

**Backend Logic:**
```javascript
// 1. Get teacher's department from JWT token
const teacherId = req.user.id;
const teacher = await Faculty.findById(teacherId);
const teacherDepartment = teacher.department_id;

// 2. Query students ONLY from teacher's department
const students = await Students.find({
  department_id: teacherDepartment,
  // Apply other filters (semester, batch, search)
});
```

**Expected Response:**
```json
{
  "success": true,
  "teacherDepartment": "BCA",
  "totalStudents": 5,
  "students": [
    {
      "id": 1,
      "rollNo": "BCA2023001",
      "name": "Aarav Sharma",
      "email": "aarav@university.edu",
      "phone": "+91 98765 43210",
      "department": "BCA",
      "semester": 5,
      "section": "A",
      "profileImage": "/uploads/students/aarav.jpg",
      "attendance": 87.5,
      "cgpa": 8.4,
      "address": "123 MG Road, Bangalore",
      "dateOfBirth": "2004-05-15",
      "guardianName": "Rajesh Sharma",
      "guardianPhone": "+91 98765 43211",
      "bloodGroup": "O+"
    }
  ]
}
```

**Database Query:**
```sql
SELECT 
  s.id,
  s.student_id as rollNo,
  CONCAT(s.first_name, ' ', s.last_name) as name,
  u.email,
  s.phone,
  d.department_name as department,
  s.current_semester as semester,
  s.profile_photo as profileImage,
  -- Calculate attendance
  (SELECT AVG(attendance_percentage) FROM attendance WHERE student_id = s.id) as attendance,
  -- Get CGPA
  (SELECT cgpa FROM semester_summary WHERE student_id = s.id ORDER BY semester DESC LIMIT 1) as cgpa,
  s.address,
  s.date_of_birth as dateOfBirth,
  s.parent_name as guardianName,
  s.parent_phone as guardianPhone,
  s.blood_group as bloodGroup
FROM students s
JOIN users u ON s.user_id = u.id
JOIN departments d ON s.department_id = d.id
JOIN faculty f ON f.id = ?
WHERE s.department_id = f.department_id
  AND s.is_active = TRUE
  -- Apply filters
  AND (? IS NULL OR s.current_semester = ?)
  AND (? IS NULL OR s.enrollment_year = ?)
  AND (? IS NULL OR s.student_id LIKE ? OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?)
ORDER BY s.student_id ASC
```

---

## 2. Get Student Details (for modal)
**API Endpoint:** `GET /api/teacher/students/:studentId`

**Security Check:**
```javascript
// Verify student belongs to teacher's department
const teacher = await Faculty.findById(req.user.id);
const student = await Students.findById(studentId);

if (student.department_id !== teacher.department_id) {
  return res.status(403).json({
    success: false,
    error: "You can only view students from your department"
  });
}
```

**Expected Response:**
```json
{
  "success": true,
  "student": {
    "id": 1,
    "rollNo": "BCA2023001",
    "name": "Aarav Sharma",
    "email": "aarav@university.edu",
    "phone": "+91 98765 43210",
    "department": "BCA",
    "semester": 5,
    "section": "A",
    "attendance": 87.5,
    "cgpa": 8.4,
    "courses": [
      "Computer Networks",
      "IT and Environment",
      "Java Programming Using Linux"
    ],
    "recentMarks": [
      {
        "subject": "Computer Networks",
        "marks": 85,
        "total": 100
      }
    ],
    "address": "123 MG Road, Bangalore",
    "dateOfBirth": "2004-05-15",
    "guardianName": "Rajesh Sharma",
    "guardianPhone": "+91 98765 43211",
    "bloodGroup": "O+"
  }
}
```

---

## Frontend Changes Made
✅ Removed "All Departments" filter dropdown
- Teachers can only see their own department students
- Department is automatically filtered by backend based on teacher's department

---

## Security Notes
⚠️ **CRITICAL:** Always verify teacher can only access students from their department
- Check on every API call
- Use teacher's department_id from JWT token
- Never trust department parameter from frontend
- Log any unauthorized access attempts

---

## Frontend Mock Data Location
**File:** `StudentPortal-React/src/pages/TeacherStudentList.jsx`
**Line:** ~11-100

Currently showing hardcoded students:
- Aarav Sharma (BCA2023001)
- Diya Patel (BCA2023002)
- Arjun Kumar (BBA2023003)
- Ananya Singh (COM2023004)
- Vihaan Reddy (BCA2024005)

**Action Required:**
Replace `const [students] = useState([...])` with API call:
```javascript
useEffect(() => {
  const fetchStudents = async () => {
    const response = await api.getTeacherStudents({
      semester: filterSemester,
      batch: filterBatch,
      search: searchQuery
    });
    if (response.success) {
      setStudents(response.students);
    }
  };
  fetchStudents();
}, [filterSemester, filterBatch, searchQuery]);
```

---

## Status
- [ ] GET students for teacher (with department restriction)
- [ ] GET student details (with security check)
- [ ] Implement department-based filtering
- [ ] Add security logging for unauthorized access
- [ ] Replace mock student data with real API call
