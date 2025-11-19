# Teacher Upload Study Materials - Backend Integration Tasks

## Feature: Upload PDF Notes and Question Papers

---

## IMPORTANT SECURITY RULES

1. **Department Restriction:**
   - Teacher can ONLY upload materials for their own department
   - Department is automatically set from teacher's profile (NOT selectable)
   - BCA teacher uploads → materials are for BCA department only
   - Teachers cannot access or upload materials for other departments

2. **File Storage:**
   - PDFs stored in XAMPP directory (e.g., `/xampp/htdocs/uploads/materials/`)
   - Store file path in database
   - Files organized by: `/{department}/{type}/{semester}/`
   - Example: `/uploads/materials/BCA/notes/semester-5/computer-networks.pdf`

---

## 1. Upload Study Material (Notes)
**API Endpoint:** `POST /api/teacher/materials/upload`

**Request:** `multipart/form-data`

**Form Fields:**
```javascript
{
  type: "notes",              // "notes" or "question_paper"
  subject: "Computer Networks",
  semester: "5",              // 1-6
  file: <PDF file>
  // department: NOT SENT - auto-set from teacher's profile
}
```

**Backend Logic:**
```javascript
// 1. Get teacher's department from JWT token
const teacherId = req.user.id;
const teacher = await Faculty.findById(teacherId);
const department = teacher.department; // e.g., "BCA"

// 2. Validate file
if (!file || file.mimetype !== 'application/pdf') {
  return error('Only PDF files allowed');
}

// 3. Create file path
const fileName = `${subject}-${Date.now()}.pdf`;
const filePath = `/uploads/materials/${department}/notes/semester-${semester}/${fileName}`;

// 4. Save file to XAMPP directory
await saveFile(file, filePath);

// 5. Save to database
await StudyMaterials.create({
  teacher_id: teacherId,
  department: department,
  type: 'notes',
  subject: subject,
  semester: semester,
  file_path: filePath,
  file_name: fileName,
  uploaded_at: new Date()
});
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Notes uploaded successfully",
  "material": {
    "id": 1,
    "type": "notes",
    "subject": "Computer Networks",
    "semester": "5",
    "department": "BCA",
    "file_path": "/uploads/materials/BCA/notes/semester-5/computer-networks-1234567890.pdf",
    "uploaded_at": "2025-01-20T10:00:00Z"
  }
}
```

---

## 2. Upload Question Paper
**API Endpoint:** `POST /api/teacher/materials/upload`

**Form Fields:**
```javascript
{
  type: "question_paper",
  subject: "Computer Networks",
  semester: "5",
  year: "2024",              // Academic year
  file: <PDF file>
  // department: NOT SENT - auto-set from teacher's profile
}
```

**File Path Structure:**
```
/uploads/materials/{department}/question_papers/{year}/semester-{semester}/{subject}.pdf

Example:
/uploads/materials/BCA/question_papers/2024/semester-5/computer-networks.pdf
```

---

## 3. Get Uploaded Materials (for teacher)
**API Endpoint:** `GET /api/teacher/materials`

**Query Parameters:**
- `type` (optional) - Filter by "notes" or "question_paper"
- `semester` (optional) - Filter by semester
- `subject` (optional) - Filter by subject

**Backend Logic:**
```javascript
// Get teacher's department
const teacher = await Faculty.findById(req.user.id);

// Query materials ONLY from teacher's department
const materials = await StudyMaterials.find({
  department: teacher.department,
  // Apply filters
});
```

**Expected Response:**
```json
{
  "success": true,
  "department": "BCA",
  "materials": [
    {
      "id": 1,
      "type": "notes",
      "subject": "Computer Networks",
      "semester": "5",
      "file_name": "computer-networks-1234567890.pdf",
      "file_path": "/uploads/materials/BCA/notes/semester-5/computer-networks-1234567890.pdf",
      "uploaded_at": "2025-01-20T10:00:00Z"
    },
    {
      "id": 2,
      "type": "question_paper",
      "subject": "Computer Networks",
      "semester": "5",
      "year": "2024",
      "file_name": "computer-networks-qp-2024.pdf",
      "file_path": "/uploads/materials/BCA/question_papers/2024/semester-5/computer-networks-qp-2024.pdf",
      "uploaded_at": "2025-01-19T14:00:00Z"
    }
  ]
}
```

---

## 4. Delete Material
**API Endpoint:** `DELETE /api/teacher/materials/:materialId`

**Security Check:**
```javascript
// Verify material belongs to teacher's department
const teacher = await Faculty.findById(req.user.id);
const material = await StudyMaterials.findById(materialId);

if (material.department !== teacher.department) {
  return res.status(403).json({
    success: false,
    error: "You can only delete materials from your department"
  });
}

// Delete file from XAMPP directory
await deleteFile(material.file_path);

// Delete from database
await StudyMaterials.delete(materialId);
```

---

## 5. Download/View Material
**API Endpoint:** `GET /api/materials/download/:materialId`

**Note:** Students can also access this endpoint

**Backend Logic:**
```javascript
const material = await StudyMaterials.findById(materialId);

// For teachers: check department match
if (req.user.role === 'teacher') {
  const teacher = await Faculty.findById(req.user.id);
  if (material.department !== teacher.department) {
    return error('Access denied');
  }
}

// For students: check department match
if (req.user.role === 'student') {
  const student = await Students.findById(req.user.id);
  if (material.department !== student.department) {
    return error('Access denied');
  }
}

// Serve PDF file
res.sendFile(material.file_path);
```

---

## Database Schema

**Table: `study_materials`**
```sql
CREATE TABLE study_materials (
  id INT PRIMARY KEY AUTO_INCREMENT,
  teacher_id INT NOT NULL,
  department VARCHAR(50) NOT NULL,
  type ENUM('notes', 'question_paper') NOT NULL,
  subject VARCHAR(100) NOT NULL,
  semester INT NOT NULL,
  year VARCHAR(10) NULL,  -- Only for question papers
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  file_size INT,  -- in bytes
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (teacher_id) REFERENCES faculty(id) ON DELETE CASCADE,
  INDEX idx_department (department),
  INDEX idx_type (type),
  INDEX idx_semester (semester)
);
```

---

## File Validation Rules

**PDF Upload:**
- File type: PDF only (`application/pdf`)
- Max size: 10MB
- File name: Sanitize to remove special characters
- Allowed characters: letters, numbers, hyphens, underscores

**Validation Code:**
```javascript
function validatePDF(file) {
  if (file.mimetype !== 'application/pdf') {
    throw new Error('Only PDF files are allowed');
  }
  
  if (file.size > 10 * 1024 * 1024) {  // 10MB
    throw new Error('File size must be less than 10MB');
  }
  
  return true;
}
```

---

## XAMPP File Storage Structure

```
/xampp/htdocs/uploads/materials/
├── BCA/
│   ├── notes/
│   │   ├── semester-1/
│   │   ├── semester-2/
│   │   ├── semester-5/
│   │   │   ├── computer-networks-1234567890.pdf
│   │   │   └── java-programming-1234567891.pdf
│   │   └── semester-6/
│   └── question_papers/
│       ├── 2024/
│       │   ├── semester-5/
│       │   │   └── computer-networks-qp-2024.pdf
│       │   └── semester-6/
│       └── 2023/
├── BBA/
│   ├── notes/
│   └── question_papers/
└── B.Com/
    ├── notes/
    └── question_papers/
```

---

## Frontend Upload Form Fields

**For Notes:**
1. Type: "Notes" (radio button)
2. Subject: Dropdown (list of subjects for teacher's department)
3. Semester: Dropdown (1-6)
4. File: PDF upload

**For Question Papers:**
1. Type: "Question Paper" (radio button)
2. Subject: Dropdown
3. Semester: Dropdown (1-6)
4. Year: Input field (e.g., 2024)
5. File: PDF upload

**NOT SHOWN:**
- Department (automatically set to teacher's department)

---

## Status
- [ ] POST upload notes
- [ ] POST upload question paper
- [ ] GET list materials
- [ ] DELETE material
- [ ] GET download/view material
- [ ] Create database table
- [ ] Set up XAMPP file storage directories
- [ ] Implement file validation
- [ ] Add department-based access control
