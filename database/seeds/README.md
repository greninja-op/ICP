# Database Seeds

This directory contains database seeding scripts for populating the database with sample data.

## Running the Seeder

To seed the database with sample data:

```bash
php database/seeds/seed.php
```

## What Gets Seeded

The seeder creates the following sample data:

### Users
- **1 Admin User**
  - Username: `admin`
  - Password: `admin123`
  - Email: `admin@university.edu`

- **10 Teachers**
  - Username: `firstname.lastname` (e.g., `rajesh.kumar`)
  - Password: `teacher123`
  - Email: `firstname.lastname@university.edu`
  - Distributed across departments: BCA, BBA, B.Com, BSc Physics

- **50 Students**
  - Username: `firstname.lastname##` (e.g., `aarav.sharma42`)
  - Password: `student123`
  - Email: `firstname.lastname##@student.university.edu`
  - Distributed across departments and semesters

### Academic Data
- **30 Subjects** across 4 departments (BCA, BBA, B.Com, BSc Physics)
- **Student Enrollments** - All students enrolled in subjects for their current semester
- **Results** - Sample results for students in semester 2 or higher
- **Attendance** - (Can be added as needed)

### Administrative Data
- **Payments** - 2-3 payments per student (70% paid, 30% pending)
- **Notices** - 8 sample notices across different categories

## Departments and Subjects

### BCA (Bachelor of Computer Applications)
- Programming in C, Digital Electronics, Mathematics-I, C Programming Lab
- Data Structures, DBMS, Web Technologies, DBMS Lab

### BBA (Bachelor of Business Administration)
- Principles of Management, Business Economics, Financial Accounting
- Marketing Management, HRM, Business Statistics

### B.Com (Bachelor of Commerce)
- Financial Accounting, Business Organization, Business Economics
- Corporate Accounting, Business Law, Cost Accounting

### BSc Physics
- Mechanics, Waves and Optics, Physics Lab-I
- Thermodynamics, Electromagnetism, Physics Lab-II

## Notes

- All passwords are hashed using bcrypt with cost factor 12
- Student IDs follow the format: S{YEAR}{###} (e.g., S2025001)
- Teacher IDs follow the format: T{####} (e.g., T0001)
- Academic year is set to current year
- Results include proper grade calculation (A+, A, B+, B, C, D, F)
- Payments include both paid and pending statuses
- Notices have different priorities and categories

## Resetting Data

To reset and reseed the database:

1. Drop all tables or run migrations down
2. Run migrations up: `php database/migrate.php up`
3. Run seeder: `php database/seeds/seed.php`

## Customization

You can modify the seeder script to:
- Change the number of students, teachers, or subjects
- Add more departments
- Customize payment amounts
- Add more notices
- Adjust grade distributions
