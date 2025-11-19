# Admin Upload Study Materials - Backend Implementation Guide

## Overview
Admin can upload study materials (notes and question papers) for specific departments, semesters, and subjects. Materials are accessible to teachers (view-only, department-restricted) and students (view-only, department and semester-restricted).

## Database Schema

### Table: `study_materials`
```sql
CREATE TABLE study_materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    material_type ENUM('notes', 'question_papers') NOT NULL,
    unit VARCHAR(10) NULL,  -- For notes: Unit 1, Unit 2, etc.
    year VARCHAR(4) NULL,   -- For question papers: 2024, 2023, etc.
    description TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_url VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_department (department),
    INDEX idx_semester (semester),
    INDEX idx_material_type (material_type),
    INDEX idx_unit (unit),
    INDEX idx_year (year)
);
```

**Note:** 
- For **Notes**: `unit` field is used (e.g., "1", "2", "3"), `year` is NULL
- For **Question Papers**: `year` field is used (e.g., "2024", "2023"), `unit` is NULL

## API Endpoints

### 1. Get Subjects by Department and Semester
**Endpoint:** `GET /api/subjects/by-dept-sem?department={department}&semester={semester}`

**Purpose:** Load subjects for the selected department and semester in the upload form

**Parameters:**
- `department`: Department code (BCA, BBA, B.Com)
- `semester`: Semester number (1-6)

**Response:**
```json
{
  "success": true,
  "subjects": [
    { "subject_name": "Computer Networks" },
    { "subject_name": "IT and Environment" },
    { "subject_name": "Java Programming Using Linux" },
    { "subject_name": "Open Course" },
    { "subject_name": "Mini Project" }
  ]
}
```

**Database Query:**
```sql
SELECT DISTINCT subject_name 
FROM subjects 
WHERE department = ? 
  AND semester = ?
ORDER BY subject_name ASC;
```

### 2. Get All Study Materials (Admin Only)
**Endpoint:** `GET /api/materials/get_all.php`

**Response:**
```json
{
  "success": true,
  "materials": [
    {
      "id": 1,
      "department": "BCA",
      "semester": 5,
      "subject": "Computer Networks",
      "materialType": "notes",
      "title": "Chapter 5 - Network Layer",
      "description": "Detailed notes on network layer protocols",
      "file_name": "network-layer-notes.pdf",
      "file_url": "/uploads/materials/BCA/semester-5/notes/network-layer-notes.pdf",
      "file_size": 2048576,
      "uploaded_by": "Admin User",
      "uploaded_at": "2025-01-20T10:00:00Z"
    }
  ]
}
```

### 3. Get Study Materials by Department (Teacher/Student)
**Endpoint:** `GET /api/materials/get_by_department.php?department={department}`

**Security:**
- Teachers: Can only access materials from their own department
- Students: Can only access materials from their own department and semester
- Backend MUST verify user's department matches requested department

**Response:**
```json
{
  "success": true,
  "materials": [
    {
      "id": 1,
      "semester": 5,
      "subject": "Computer Networks",
      "materialType": "notes",
      "title": "Chapter 5 - Network Layer",
      "file_url": "/uploads/materials/BCA/semester-5/notes/network-layer-notes.pdf",
      "uploaded_at": "2025-01-20T10:00:00Z"
    }
  ]
}
```

### 4. Upload Study Material (Admin Only)
**Endpoint:** `POST /api/materials/upload.php`

**Request (FormData) - For Notes:**
```
department: "BCA"
semester: "5"
subject: "Computer Networks"
materialType: "notes"
unit: "1"
description: "Network layer protocols"
file: [PDF File]
```

**Request (FormData) - For Question Papers:**
```
department: "BCA"
semester: "5"
subject: "Computer Networks"
materialType: "question_papers"
year: "2024"
description: "End semester examination"
file: [PDF File]
```

**Validation:**
- File must be PDF format
- File size limit: 10MB
- Required fields: department, semester, subject, materialType, file
- For notes: `unit` is required (1-8)
- For question papers: `year` is required (2020-2030)
- Only admin users can upload

**File Storage:**
- Path structure: `/uploads/materials/{department}/semester-{semester}/{materialType}/{unit or year}/{filename}`
- Example (Notes): `/uploads/materials/BCA/semester-5/notes/unit-1/network-layer-notes.pdf`
- Example (Q. Papers): `/uploads/materials/BCA/semester-5/question_papers/2024/computer-networks-2024.pdf`

**Response:**
```json
{
  "success": true,
  "material": {
    "id": 1,
    "department": "BCA",
    "semester": 5,
    "subject": "Computer Networks",
    "materialType": "notes",
    "unit": "1",
    "file_url": "/uploads/materials/BCA/semester-5/notes/unit-1/network-layer-notes.pdf"
  }
}
```

### 5. Delete Study Material (Admin Only)
**Endpoint:** `POST /api/materials/delete.php`

**Request:**
```json
{
  "material_id": 1
}
```

**Actions:**
- Delete database record
- Delete physical file from server
- Only admin users can delete

**Response:**
```json
{
  "success": true,
  "message": "Material deleted successfully"
}
```

## Security Requirements

### Admin Upload
- Verify user role is 'admin'
- Validate file type (PDF only)
- Validate file size (max 10MB)
- Sanitize file names to prevent directory traversal
- Store files outside web root or with proper access controls

### Teacher View
- Verify user role is 'staff'
- Filter materials by teacher's department ONLY
- Teachers cannot access materials from other departments
- Read-only access (no upload, edit, or delete)

### Student View
- Verify user role is 'student'
- Filter materials by student's department AND semester
- Students cannot access materials from other departments or semesters
- Read-only access (no upload, edit, or delete)

## File Management

### Upload Process
1. Validate user permissions (admin only)
2. Validate file type and size
3. Generate unique filename to prevent conflicts
4. Create directory structure if not exists
5. Move uploaded file to destination
6. Insert record into database
7. Return file URL

### Delete Process
1. Validate user permissions (admin only)
2. Get file path from database
3. Delete physical file from server
4. Delete database record
5. Return success response

### Download/View Process
1. Validate user permissions
2. Verify user's department matches material's department
3. For students: also verify semester matches
4. Serve file with appropriate headers
5. Log access for audit trail

## Error Handling

### Common Errors
- `401 Unauthorized`: User not logged in
- `403 Forbidden`: User doesn't have permission (wrong role or department)
- `400 Bad Request`: Invalid file type, size, or missing required fields
- `404 Not Found`: Material not found
- `500 Internal Server Error`: File upload/delete failed

## Notes for Frontend Integration

### Admin Upload Page
- Department dropdown: BCA, BBA, B.Com
- Semester dropdown: 1-6
- **Subject dropdown**: Dynamically loaded based on selected department and semester
  - Fetches subjects from subjects database
  - Ensures consistency with student view
  - Prevents typos and maintains data integrity
- Material type: Notes or Question Papers
- **For Notes**: Unit dropdown (1-8)
- **For Question Papers**: Year input (2020-2030)
- Description (optional)
- PDF file upload with preview
- List of uploaded materials with delete option

### Teacher View Page
- Automatically filtered by teacher's department
- Cannot see materials from other departments
- Semester and subject filters
- Download/view PDF button
- No upload or delete options

### Student View Page (Future)
- Automatically filtered by student's department and semester
- Cannot see materials from other departments or semesters
- Subject filter
- Download/view PDF button
- No upload or delete options

## Testing Checklist

- [ ] Admin can upload materials for any department
- [ ] Admin can view all uploaded materials
- [ ] Admin can delete materials
- [ ] Teachers can only view materials from their department
- [ ] Teachers cannot view materials from other departments
- [ ] Teachers cannot upload or delete materials
- [ ] Students can only view materials from their department and semester
- [ ] File upload validates PDF format
- [ ] File upload validates size limit (10MB)
- [ ] Files are stored securely
- [ ] File deletion removes both database record and physical file
- [ ] Proper error messages for all failure cases
