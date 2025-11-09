-- Migration script to update departments from old names to new degree programs
-- Run this on your MySQL database

-- Update Students table
-- Map old department names to new degree programs
UPDATE students 
SET department = CASE 
    WHEN department IN ('Computer Science', 'Information Technology') THEN 'BCA'
    WHEN department IN ('Business Administration', 'Management') THEN 'BBA'
    WHEN department IN ('Commerce', 'Accounting', 'Finance') THEN 'B.Com'
    ELSE department
END
WHERE department IN ('Computer Science', 'Information Technology', 'Business Administration', 'Management', 'Commerce', 'Accounting', 'Finance', 'Electronics', 'Mechanical', 'Civil');

-- Update Teachers table
UPDATE teachers 
SET department = CASE 
    WHEN department IN ('Computer Science', 'Information Technology') THEN 'BCA'
    WHEN department IN ('Business Administration', 'Management') THEN 'BBA'
    WHEN department IN ('Commerce', 'Accounting', 'Finance') THEN 'B.Com'
    ELSE department
END
WHERE department IN ('Computer Science', 'Information Technology', 'Business Administration', 'Management', 'Commerce', 'Accounting', 'Finance', 'Electronics', 'Mechanical', 'Civil');

-- Update semester values to be within 1-6 range
-- If any student has semester 7 or 8, cap it to 6
UPDATE students 
SET semester = '6'
WHERE CAST(semester AS UNSIGNED) > 6;

-- Verify the changes
SELECT 'Students by Department' as info;
SELECT department, COUNT(*) as count FROM students GROUP BY department;

SELECT 'Teachers by Department' as info;
SELECT department, COUNT(*) as count FROM teachers GROUP BY department;

SELECT 'Students by Semester' as info;
SELECT semester, COUNT(*) as count FROM students GROUP BY semester ORDER BY CAST(semester AS UNSIGNED);
