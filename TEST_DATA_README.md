# Comprehensive Test Data Generator

This script generates **100 test users** for the Student Portal:
- **20 Teachers** (5 per department)
- **80 Students** (5 per department per year)

## Quick Start

1. Update database credentials in `generate_comprehensive_test_data.php`:
   ```php
   $host = 'localhost';
   $dbname = 'studentportal';
   $username = 'root';
   $password = '';
   ```

2. Run the script:
   ```bash
   php generate_comprehensive_test_data.php
   ```

## What Gets Generated

### Teachers (20 total)
- **5 teachers per department**
- Departments: BCA, BBA, B.Com, BSc Physics
- Random Indian names
- Username format: `firstname.lastname`
- Email format: `username@university.edu`
- All passwords: `123`

### Students (80 total)
- **5 students per department per year**
- 4 departments × 4 years × 5 students = 80 students
- Complete profile information:
  - Roll numbers (e.g., BCA2024001)
  - Year and semester
  - Section (A, B, or C)
  - Date of birth
  - Blood group
  - Address
  - Guardian information
  - Phone numbers
- All passwords: `123`

## Data Distribution

### By Department:
- **BCA**: 5 teachers, 20 students (5 per year)
- **BBA**: 5 teachers, 20 students (5 per year)
- **B.Com**: 5 teachers, 20 students (5 per year)
- **BSc Physics**: 5 teachers, 20 students (5 per year)

### By Year:
- **1st Year**: 20 students (5 per department)
- **2nd Year**: 20 students (5 per department)
- **3rd Year**: 20 students (5 per department)
- **4th Year**: 20 students (5 per department)

## Testing the Admin Dashboard

After running the script, you can test the drill-down functionality:

1. Login as admin
2. Click "View Students" card
3. Select a year (e.g., "1st Year")
4. Select a department (e.g., "BCA")
5. You should see 5 students from that specific year and department

Similarly for teachers:
1. Click "View Teachers" card
2. Select a department (e.g., "BBA")
3. You should see 5 teachers from that department

## Sample Login Credentials

The script generates random names, but all use the same password format:

**Teacher Login:**
- Username: (generated, e.g., `rajesh.sharma`)
- Password: `123`
- Role: Staff

**Student Login:**
- Username: (generated, e.g., `aarav.patel`)
- Password: `123`
- Role: Student

## Features

- ✅ Handles duplicate usernames automatically
- ✅ Generates realistic Indian names
- ✅ Creates proper roll numbers
- ✅ Assigns random sections
- ✅ Generates guardian information
- ✅ Creates complete address data
- ✅ Passwords are properly hashed using PHP's `password_hash()`

## Database Requirements

Your `users` table should have these columns:
- username, password, full_name, email, role
- department, phone, created_at
- For students: year, semester, section, roll_no, date_of_birth, blood_group, address, guardian_name, guardian_phone

## Troubleshooting

If you get duplicate key errors:
- The script automatically handles duplicates by appending numbers
- If issues persist, clear your users table first:
  ```sql
  DELETE FROM users WHERE role IN ('student', 'staff');
  ```

## Notes

- The script uses random Indian names from a predefined list
- All data is fictional and for testing purposes only
- Phone numbers are randomly generated in Indian format
- Addresses use random Indian cities
- Blood groups are randomly assigned
