# Study Materials - Unit/Year System Update

## Overview
Updated the study materials system to use **Units** for Notes and **Years** for Question Papers instead of generic titles.

## Changes Made

### Admin Upload Interface

#### Before:
- Title field (free text) for both notes and question papers

#### After:
- **For Notes**: Unit dropdown (Unit 1 - Unit 8)
- **For Question Papers**: Year input (2020-2030)
- Title field removed

### Student View Interface

#### Before:
1. Select Material Type (Notes/Question Papers)
2. Select Semester (1-6)
3. Select Subject
4. View & Download Files

#### After:
1. Select Material Type (Notes/Question Papers)
2. Select Semester (1-6)
3. Select Subject
4. **NEW: Select Unit (for Notes) or Year (for Question Papers)**
5. View & Download Files

## Database Schema Update

### Updated Table: `study_materials`

```sql
CREATE TABLE study_materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    material_type ENUM('notes', 'question_papers') NOT NULL,
    unit VARCHAR(10) NULL,  -- For notes: "1", "2", "3", etc.
    year VARCHAR(4) NULL,   -- For question papers: "2024", "2023", etc.
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

**Key Changes:**
- Removed `title` field
- Added `unit` field (for notes)
- Added `year` field (for question papers)
- Added indexes for unit and year

## File Storage Structure

### Before:
```
/uploads/materials/{department}/semester-{semester}/{type}/{filename}
```

### After:
```
/uploads/materials/{department}/semester-{semester}/{type}/{unit or year}/{filename}
```

**Examples:**
- Notes: `/uploads/materials/BCA/semester-5/notes/unit-1/network-basics.pdf`
- Question Papers: `/uploads/materials/BCA/semester-5/question_papers/2024/computer-networks.pdf`

## API Endpoints Update

### New Endpoint: Get Units or Years
**Endpoint:** `GET /api/materials/list`

**Parameters:**
- `department`: Student's department
- `semester`: Selected semester
- `subject`: Selected subject
- `type`: 'notes' or 'question_papers'

**Response (Notes):**
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

**Response (Question Papers):**
```json
{
  "success": true,
  "items": [
    { "year": "2024", "count": 1 },
    { "year": "2023", "count": 1 }
  ]
}
```

### New Endpoint: Get Files for Unit/Year
**Endpoint:** `GET /api/materials/files`

**Parameters:**
- `department`: Student's department
- `semester`: Selected semester
- `subject`: Selected subject
- `type`: 'notes' or 'question_papers'
- `unit`: Unit number (for notes only)
- `year`: Year (for question papers only)

**Response:**
```json
{
  "success": true,
  "files": [
    {
      "id": 1,
      "title": "Unit 1 - Part 1",
      "description": "Introduction",
      "file_url": "/uploads/materials/BCA/semester-5/notes/unit-1/intro.pdf",
      "uploaded_at": "2025-01-20T10:00:00Z"
    }
  ]
}
```

## UI/UX Changes

### Admin Upload Form

**Material Type Selection:**
- Two large cards: "Notes" and "Question Papers"
- Selection determines which field appears below

**Dynamic Fields:**
- **If Notes selected**: Unit dropdown appears (Unit 1 - Unit 8)
- **If Question Papers selected**: Year input appears (2020-2030)

**Materials Table:**
- Column changed from "Title" to "Unit/Year"
- Shows "Unit 1", "Unit 2" for notes
- Shows "2024", "2023" for question papers

### Student Interface

**Step 4: Unit/Year Selection**
- Grid layout with buttons
- **For Notes**: Shows "Unit 1", "Unit 2", "Unit 3", etc.
- **For Question Papers**: Shows "2024", "2023", "2022", etc.
- Each button shows file count (e.g., "3 files")
- Hover animation on buttons

**Step 5: Files List**
- Shows actual PDF files for selected unit/year
- Download button for each file
- File metadata (description, upload date)

## Benefits

### For Admins:
- ✅ Clearer organization by units/years
- ✅ No need to think of titles
- ✅ Consistent naming convention
- ✅ Easier to manage and track uploads

### For Students:
- ✅ Intuitive navigation (Unit 1, Unit 2, Unit 3...)
- ✅ Easy to find specific units
- ✅ Clear year-wise question papers
- ✅ Better organization of study materials

### For Backend:
- ✅ Structured data (unit/year instead of free text)
- ✅ Easier to query and filter
- ✅ Better indexing performance
- ✅ Consistent file organization

## Migration Notes

If you have existing data with `title` field:

```sql
-- For notes: Extract unit number from title if possible
UPDATE study_materials 
SET unit = REGEXP_REPLACE(title, '[^0-9]', '')
WHERE material_type = 'notes' 
  AND title REGEXP 'Unit [0-9]+';

-- For question papers: Extract year from title if possible
UPDATE study_materials 
SET year = REGEXP_REPLACE(title, '[^0-9]', '')
WHERE material_type = 'question_papers' 
  AND title REGEXP '20[0-9]{2}';

-- Then drop the title column
ALTER TABLE study_materials DROP COLUMN title;
```

## Testing Checklist

### Admin Upload
- [ ] Can select Notes and see Unit dropdown
- [ ] Can select Question Papers and see Year input
- [ ] Unit dropdown shows Unit 1 - Unit 8
- [ ] Year input accepts 2020-2030
- [ ] Upload works with unit for notes
- [ ] Upload works with year for question papers
- [ ] Materials table shows Unit/Year correctly

### Student View
- [ ] Step 4 shows units for notes
- [ ] Step 4 shows years for question papers
- [ ] Unit buttons display correctly (Unit 1, Unit 2, etc.)
- [ ] Year buttons display correctly (2024, 2023, etc.)
- [ ] File count shows on each button
- [ ] Clicking unit/year loads files
- [ ] Files display correctly in step 5
- [ ] Download works

## Files Updated

1. `StudentPortal-React/src/pages/admin/AdminUploadMaterials.jsx`
   - Added unit/year fields
   - Removed title field
   - Updated form data structure
   - Updated materials table

2. `StudentPortal-React/src/pages/StudentMaterials.jsx`
   - Added step 4 for unit/year selection
   - Updated state management
   - Added fetchUnitsOrYears function
   - Added fetchFiles function
   - Updated UI flow

3. `backend-comments/admin-upload-materials.md`
   - Updated database schema
   - Updated API documentation
   - Updated file storage structure

4. `backend-comments/student-materials.md`
   - Updated user flow
   - Added new API endpoints
   - Updated database queries
   - Updated UI documentation

## Summary

The system now uses a more structured approach:
- **Notes**: Organized by Units (1-8)
- **Question Papers**: Organized by Years (2020-2030)

This provides better organization, easier navigation, and clearer structure for both admins and students.
