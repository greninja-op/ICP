# Admin Students - Backend Integration Tasks

## File: `StudentPortal-React/src/pages/admin/AdminStudents.jsx`

---

## 1. Get All Students
**API Endpoint:** `GET /api/admin/students`

**Query Parameters:**
- `department` (optional) - Filter by department (BCA, BBA, B.Com)
- `semester` (optional) - Filter by semester (1-6)
- `year` (optional) - Filter by year
- `page` (optional) - Page number for pagination
- `limit` (optional) - Items per page

**Expected Response:**
```json
{
  "success": true,
  "students": [
    {
      "id": 1,
      "student_id": "202501234567",
      "full_name": "John Doe",
      "username": "john.doe",
      "email": "john@university.edu",
      "department": "BCA",
      "semester": "1",
      "year": 2025,
      "phone": "9876543210",
      "date_of_birth": "2004-05-15",
      "address": "123 Main St",
      "profile_image": "/uploads/students/john.jpg"
    }
  ]
}
```

**Database Query:**
```sql
SELECT * FROM students 
LEFT JOIN users ON students.user_id = users.id
WHERE department = ? AND semester = ?
ORDER BY student_id ASC
```

---

## 2. Add New Student
**API Endpoint:** `POST /api/admin/students`

**Request Body:**
```json
{
  "student_id": "202501234567",
  "full_name": "John Doe",
  "username": "john.doe",
  "email": "john@university.edu",
  "password": "password123",
  "department": "BCA",
  "semester": "1",
  "year": 2025,
  "phone": "9876543210",
  "date_of_birth": "2004-05-15",
  "address": "123 Main St",
  "profile_image": "/uploads/students/john.jpg"
}
```

**Validation Rules:**
- `student_id`: Required, exactly 12 digits, unique
- `full_name`: Required, min 2 characters
- `username`: Required, unique, alphanumeric with dots/underscores
- `email`: Required, valid email format, unique
- `password`: Required, min 8 characters
- `department`: Required, must be one of: BCA, BBA, B.Com
- `semester`: Required, must be 1-6
- `phone`: Optional, exactly 10 digits if provided
- `date_of_birth`: Optional, valid date format

**Database Operations:**
1. Hash password using bcrypt/argon2
2. Insert into `users` table (username, email, password_hash, role='student')
3. Get inserted user_id
4. Insert into `students` table with user_id reference

**Expected Response:**
```json
{
  "success": true,
  "message": "Student added successfully",
  "student_id": "202501234567"
}
```

---

## 3. Update Student
**API Endpoint:** `PUT /api/admin/students/:student_id`

**Request Body:** (Same as Add, but password is optional)

**Important Notes:**
- When fetching student for edit, ensure `semester` is returned as STRING not number
- Department must match exactly (BCA, BBA, B.Com)
- If password is empty, don't update it
- Use original student_id from URL to find record, allow updating to new student_id

**Database Operations:**
1. Find student by original student_id
2. Update users table (username, email, password if provided)
3. Update students table (all other fields)

---

## 4. Delete Student
**API Endpoint:** `DELETE /api/admin/students/:student_id`

**Expected Response:**
```json
{
  "success": true,
  "message": "Student deleted successfully"
}
```

**Database Operations:**
1. Delete from students table (CASCADE will delete user record)
2. Delete profile image file if exists

---

## 5. Upload Profile Image
**API Endpoint:** `POST /api/admin/students/upload-image`

**Request:** `multipart/form-data` with image file

**Validation:**
- File type: JPG, PNG only
- Max size: 2MB
- Image will be cropped to circular format on frontend

**Expected Response:**
```json
{
  "success": true,
  "image_url": "/uploads/students/1234567890.jpg"
}
```

**File Storage:**
- Save to `/uploads/students/` directory
- Generate unique filename (timestamp + random)
- Return relative URL path

---

## Frontend Mock Data Location
Currently using mock data in `api.js` service file.

## Status
- [ ] GET all students
- [ ] POST add student
- [ ] PUT update student
- [ ] DELETE student
- [ ] POST upload image
