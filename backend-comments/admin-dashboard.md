# Admin Dashboard - Backend Integration Tasks

## File: `StudentPortal-React/src/pages/AdminDashboard.jsx`

---

## 1. Get Recent Notices (Carousel)
**API Endpoint:** `GET /api/notices?exclude_fee=true&limit=5`

**Query Parameters:**
- `exclude_fee=true` - Exclude individual fee notices
- `limit=5` - Get latest 5 notices

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "College Holiday - Republic Day",
      "content": "The college will remain closed on January 26th...",
      "category": "urgent",
      "date": "2025-01-20T10:00:00Z"
    },
    {
      "id": 2,
      "title": "Annual Tech Fest 2025",
      "content": "Join us for the biggest tech event...",
      "category": "event",
      "date": "2025-01-18T09:00:00Z"
    }
  ]
}
```

**Categories:**
- `urgent` - Red dot (holidays, important announcements)
- `event` - Blue dot (tech fests, cultural events)
- `academic` - Purple dot (exam schedules, academic updates)
- `general` - Green dot (general information)

**Database Query:**
```sql
SELECT id, title, content, category, published_date as date
FROM notices
WHERE target_audience IN ('all', 'students', 'faculty')
  AND category != 'fee'
  AND is_active = TRUE
  AND (expiry_date IS NULL OR expiry_date > NOW())
ORDER BY published_date DESC
LIMIT 5
```

**Important Notes:**
- Exclude fee-related notices (those are sent individually to students)
- Only show active notices
- Don't show expired notices
- Order by most recent first

---

## 2. Get Dashboard Statistics
**API Endpoint:** `GET /api/admin/dashboard/stats`

**Expected Response:**
```json
{
  "success": true,
  "stats": {
    "totalStudents": 1250,
    "totalTeachers": 85,
    "totalCourses": 120,
    "activeNotices": 15,
    "studentsByYear": {
      "1": 320,
      "2": 310,
      "3": 305,
      "4": 315
    },
    "studentsByDepartment": {
      "BCA": 450,
      "BBA": 400,
      "B.Com": 400
    }
  }
}
```

**Database Queries:**
```sql
-- Total students
SELECT COUNT(*) FROM students WHERE is_active = TRUE;

-- Total teachers
SELECT COUNT(*) FROM faculty WHERE is_active = TRUE;

-- Total courses
SELECT COUNT(*) FROM courses WHERE is_active = TRUE;

-- Active notices
SELECT COUNT(*) FROM notices WHERE is_active = TRUE;

-- Students by year
SELECT year, COUNT(*) as count 
FROM students 
WHERE is_active = TRUE 
GROUP BY year;

-- Students by department
SELECT department, COUNT(*) as count 
FROM students 
WHERE is_active = TRUE 
GROUP BY department;
```

---

## 3. Get Students by Filter (for drill-down modal)
**API Endpoint:** `GET /api/admin/students/filter`

**Query Parameters:**
- `year` - Filter by year (1st Year, 2nd Year, etc.)
- `department` - Filter by department (BCA, BBA, B.Com)

**Expected Response:**
```json
{
  "success": true,
  "students": [
    {
      "student_id": "202501234567",
      "full_name": "John Doe",
      "department": "BCA",
      "year": 1,
      "semester": 1
    }
  ]
}
```

**Used For:**
- When admin clicks on a year/department card
- Shows filtered list of students
- Allows navigation to student management page with filters

---

## Frontend Mock Data
Currently using hardcoded mock data:
```javascript
const mockNotices = [
  {
    id: 1,
    title: "College Holiday - Republic Day",
    content: "The college will remain closed...",
    category: "urgent",
    date: "2025-01-20T10:00:00Z"
  },
  // ... 2 more notices
]
```

Replace with actual API call in `fetchDashboardData()` function.

---

## Status
- [ ] GET recent notices (carousel)
- [ ] GET dashboard statistics
- [ ] GET students by filter
