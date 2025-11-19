# Student Materials - Backend Implementation Guide

## Overview
Students can view and download study materials (notes and question papers) for their department and semester. The interface uses a step-by-step selection flow: Material Type → Semester → Subject → Materials List.

## User Flow

### Step 1: Select Material Type
- **Notes**: Study notes and materials
- **Question Papers**: Previous year exam papers

### Step 2: Select Semester
- Choose from Semester 1-6
- Only semesters relevant to the student's department are shown

### Step 3: Select Subject
- Shows subjects for the selected semester
- Subjects are filtered by student's department

### Step 4: Select Unit (for Notes) or Year (for Question Papers)
- **For Notes**: Shows available units (Unit 1, Unit 2, Unit 3, etc.)
- **For Question Papers**: Shows available years (2024, 2023, 2022, etc.)
- Displays count of files available for each unit/year

### Step 5: View & Download Materials
- List of PDF files for the selected unit/year
- Download/view PDF files

## Security Requirements

### Access Control
- **Authentication**: User must be logged in as a student
- **Department Restriction**: Students can ONLY access materials from their own department
- **Semester Restriction**: Students can ONLY access materials from their current semester or below
- **No Upload/Edit/Delete**: Students have read-only access

### Backend Validation
```php
// Verify student's department matches material's department
if ($material->department !== $student->department) {
    return error('Access denied: Department mismatch');
}

// Verify student's semester is >= material's semester
if ($student->semester < $material->semester) {
    return error('Access denied: Material not available for your semester');
}
```

## API Endpoints

### 1. Get Subjects for Semester
**Endpoint:** `GET /api/materials/subjects?department={department}&semester={semester}`

**Security:**
- Verify student's department matches requested department
- Return subjects for the specified semester

**Response:**
```json
{
  "success": true,
  "subjects": [
    "Computer Networks",
    "IT and Environment",
    "Java Programming Using Linux",
    "Open Course",
    "Mini Project"
  ]
}
```

### 2. Get Units or Years for Subject
**Endpoint:** `GET /api/materials/list?department={department}&semester={semester}&subject={subject}&type={type}`

**Parameters:**
- `department`: Student's department (BCA, BBA, B.Com)
- `semester`: Selected semester (1-6)
- `subject`: Selected subject name
- `type`: Material type ('notes' or 'question_papers')

**Security:**
- Verify student's department matches requested department
- Verify student's semester >= requested semester

**Response (For Notes):**
```json
{
  "success": true,
  "items": [
    { "unit": "1", "count": 3 },
    { "unit": "2", "count": 2 },
    { "unit": "3", "count": 4 }
  ]
}
```

**Response (For Question Papers):**
```json
{
  "success": true,
  "items": [
    { "year": "2024", "count": 1 },
    { "year": "2023", "count": 1 },
    { "year": "2022", "count": 1 }
  ]
}
```

### 3. Get Files for Unit or Year
**Endpoint:** `GET /api/materials/files?department={department}&semester={semester}&subject={subject}&type={type}&unit={unit}` (for notes)
**OR:** `GET /api/materials/files?department={department}&semester={semester}&subject={subject}&type={type}&year={year}` (for question papers)

**Parameters:**
- `department`: Student's department
- `semester`: Selected semester
- `subject`: Selected subject
- `type`: Material type
- `unit`: Unit number (for notes only)
- `year`: Year (for question papers only)

**Security:**
- Verify student's department matches requested department
- Verify student's semester >= requested semester

**Response:**
```json
{
  "success": true,
  "files": [
    {
      "id": 1,
      "title": "Unit 1 - Part 1",
      "description": "Introduction to Computer Networks",
      "file_url": "/uploads/materials/BCA/semester-5/notes/unit-1/intro.pdf",
      "uploaded_at": "2025-01-20T10:00:00Z"
    },
    {
      "id": 2,
      "title": "Unit 1 - Part 2",
      "description": "Network Models",
      "file_url": "/uploads/materials/BCA/semester-5/notes/unit-1/models.pdf",
      "uploaded_at": "2025-01-19T14:00:00Z"
    }
  ]
}
```

### 3. Download Material
**Endpoint:** `GET /api/materials/download/:materialId`

**Security:**
- Verify student is logged in
- Verify material's department matches student's department
- Verify material's semester <= student's semester
- Log download for audit trail

**Response:**
- Serve PDF file with appropriate headers
- Content-Type: application/pdf
- Content-Disposition: inline or attachment

## Database Queries

### Get Subjects for Department and Semester
```sql
SELECT DISTINCT subject 
FROM study_materials 
WHERE department = ? 
  AND semester = ?
ORDER BY subject ASC;
```

### Get Units or Years for Subject
```sql
-- For Notes: Get distinct units
SELECT DISTINCT unit, COUNT(*) as count
FROM study_materials
WHERE department = ?
  AND semester = ?
  AND subject = ?
  AND material_type = 'notes'
GROUP BY unit
ORDER BY CAST(unit AS UNSIGNED) ASC;

-- For Question Papers: Get distinct years
SELECT DISTINCT year, COUNT(*) as count
FROM study_materials
WHERE department = ?
  AND semester = ?
  AND subject = ?
  AND material_type = 'question_papers'
GROUP BY year
ORDER BY year DESC;
```

### Get Files for Unit or Year
```sql
-- For Notes
SELECT id, description as title, description, file_url, uploaded_at
FROM study_materials
WHERE department = ?
  AND semester = ?
  AND subject = ?
  AND material_type = 'notes'
  AND unit = ?
ORDER BY uploaded_at DESC;

-- For Question Papers
SELECT id, CONCAT(subject, ' - ', year) as title, description, file_url, uploaded_at
FROM study_materials
WHERE department = ?
  AND semester = ?
  AND subject = ?
  AND material_type = 'question_papers'
  AND year = ?
ORDER BY uploaded_at DESC;
```

### Verify Student Access
```sql
SELECT m.*, s.department as student_dept, s.semester as student_sem
FROM study_materials m
CROSS JOIN students s
WHERE m.id = ?
  AND s.student_id = ?
  AND m.department = s.department
  AND m.semester <= s.semester;
```

## Frontend Features

### Navigation Integration
- New "Materials" button added to student navigation bar
- Uses same animation system as other nav buttons (motion/react)
- Active state with blue background highlight
- Book icon for materials

### Step-by-Step Flow
1. **Material Type Selection**: Large cards for Notes and Question Papers
2. **Semester Selection**: Grid of 6 semester buttons
3. **Subject Selection**: List of subjects for selected semester
4. **Unit/Year Selection**: 
   - For Notes: Grid of unit buttons (Unit 1, Unit 2, etc.)
   - For Question Papers: Grid of year buttons (2024, 2023, etc.)
5. **Files List**: Downloadable PDF files with metadata

### UI Components
- Back button navigation through steps
- Loading states with spinner
- Empty states with helpful messages
- Responsive grid layouts
- Hover animations on interactive elements
- Dark mode support

## Error Handling

### Common Errors
- `401 Unauthorized`: Student not logged in
- `403 Forbidden`: Access denied (wrong department or semester)
- `404 Not Found`: Material not found
- `500 Internal Server Error`: Server error

### Error Messages
```json
{
  "success": false,
  "error": "Access denied: You can only view materials from your department"
}
```

## Testing Checklist

- [ ] Students can only view materials from their own department
- [ ] Students cannot view materials from higher semesters
- [ ] Material type selection works (Notes vs Question Papers)
- [ ] Semester selection shows correct semesters (1-6)
- [ ] Subject list shows correct subjects for department and semester
- [ ] Materials list shows correct files for selected criteria
- [ ] Download/view PDF works correctly
- [ ] Back button navigation works through all steps
- [ ] Loading states display correctly
- [ ] Empty states display when no materials available
- [ ] Navigation bar "Materials" button works
- [ ] Active state highlights correctly
- [ ] Dark mode works properly
- [ ] Responsive design works on mobile

## Notes for Backend Developer

### Subject List
The subject list should come from the `study_materials` table (distinct subjects) or from a separate `subjects` table if you have one. Make sure to filter by department and semester.

### File Security
- Store files outside web root or use access control
- Verify permissions before serving files
- Log all downloads for audit trail
- Consider rate limiting to prevent abuse

### Performance
- Cache subject lists (they don't change often)
- Index database columns: department, semester, subject, material_type
- Consider pagination for large material lists

### Future Enhancements
- Search functionality across materials
- Bookmarking favorite materials
- Recently viewed materials
- Material ratings/feedback
- Download statistics
