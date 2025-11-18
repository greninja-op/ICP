# Database Setup Guide

This directory contains the database schema, migrations, and seeding scripts for the University Portal.

## Directory Structure

```
database/
├── migrations/          # Database migration files
│   ├── README.md
│   ├── 2025_01_15_100000_create_users_table.php
│   ├── 2025_01_15_100001_create_students_table.php
│   ├── 2025_01_15_100002_create_teachers_table.php
│   ├── 2025_01_15_100003_create_sessions_table.php
│   ├── 2025_01_15_100004_create_activity_logs_table.php
│   ├── 2025_01_15_110000_create_subjects_table.php
│   ├── 2025_01_15_110001_create_student_subjects_table.php
│   ├── 2025_01_15_110002_create_results_table.php
│   ├── 2025_01_15_110003_create_attendance_table.php
│   ├── 2025_01_15_120000_create_payments_table.php
│   └── 2025_01_15_120001_create_notices_table.php
├── seeds/              # Database seeding scripts
│   ├── README.md
│   └── seed.php
├── migrate.php         # Migration runner script
└── README.md          # This file
```

## Quick Start

### 1. Configure Database Connection

Copy `.env.example` to `.env` and update the database credentials:

```bash
cp .env.example .env
```

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=university_portal
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. Create Database

Create the database in MySQL:

```sql
CREATE DATABASE university_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Run Migrations

Execute all pending migrations:

```bash
php database/migrate.php up
```

This will create all the necessary tables:
- users
- students
- teachers
- sessions
- activity_logs
- subjects
- student_subjects
- results
- attendance
- payments
- notices

### 4. Seed Sample Data

Populate the database with sample data:

```bash
php database/seeds/seed.php
```

This will create:
- 1 admin user
- 10 teachers
- 50 students
- 30 subjects
- Sample results, payments, and notices

## Database Schema

### Core Tables

**users** - All system users (students, teachers, admins)
- Primary authentication table
- Stores username, email, password hash, role
- Indexed on username, email, role

**students** - Student-specific information
- Links to users table via user_id
- Stores student ID, department, semester, admission details
- Indexed on student_id, department, semester

**teachers** - Teacher-specific information
- Links to users table via user_id
- Stores teacher ID, department, qualifications
- Indexed on teacher_id, department

**sessions** - Active user sessions
- Tracks logged-in users
- Stores session data, IP address, user agent
- Indexed on user_id, last_activity

**activity_logs** - Audit trail
- Logs all important user actions
- Stores action type, entity details, timestamp
- Indexed on user_id, action, created_at

### Academic Tables

**subjects** - Course catalog
- Stores subject code, name, department, semester, credits
- Links to teachers table for instructor assignment
- Indexed on subject_code, department+semester

**student_subjects** - Course enrollment
- Many-to-many relationship between students and subjects
- Tracks enrollment by academic year
- Unique constraint on student+subject+year

**results** - Examination results
- Stores marks (internal, theory, lab), grades, grade points
- Calculates SGPA and CGPA
- Unique constraint on student+subject+semester+year
- Indexed on student_id+semester

**attendance** - Attendance records
- Tracks daily attendance (present, absent, leave)
- Links to teacher who marked attendance
- Unique constraint on student+subject+date
- Indexed on student+subject, date

### Administrative Tables

**payments** - Fee management
- Tracks all student payments
- Stores fee type, amount, due date, payment status
- Supports multiple payment methods
- Indexed on student_id+payment_status, due_date

**notices** - Announcements and notifications
- Stores title, content, category, target audience
- Supports expiry dates and priority levels
- Indexed on category, is_active, published_date

## Migration Commands

### Run all pending migrations
```bash
php database/migrate.php up
```

### Rollback last batch of migrations
```bash
php database/migrate.php down
```

### Show migration status
```bash
php database/migrate.php status
```

## Sample Login Credentials

After seeding, you can log in with:

**Admin:**
- Username: `admin`
- Password: `admin123`

**Teacher (any):**
- Username: `rajesh.kumar` (or any other teacher username)
- Password: `teacher123`

**Student (any):**
- Username: Check seeder output for generated usernames
- Password: `student123`

## Database Requirements

- MySQL 8.0+ or MariaDB 10.5+
- PHP 8.1+ with PDO extension
- Character set: utf8mb4
- Collation: utf8mb4_unicode_ci

## Foreign Key Relationships

```
users (1) ──── (1) students
users (1) ──── (1) teachers
users (1) ──── (many) sessions
users (1) ──── (many) activity_logs
users (1) ──── (many) notices [published_by]

students (many) ──── (many) subjects [through student_subjects]
students (1) ──── (many) results
students (1) ──── (many) payments
students (1) ──── (many) attendance

teachers (1) ──── (many) subjects [assigned]
teachers (1) ──── (many) attendance [marked_by]

subjects (1) ──── (many) results
subjects (1) ──── (many) attendance
```

## Backup and Restore

### Backup
```bash
mysqldump -u root -p university_portal > backup_$(date +%Y%m%d).sql
```

### Restore
```bash
mysql -u root -p university_portal < backup_20250115.sql
```

## Troubleshooting

### Connection Failed
- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check if database exists

### Migration Errors
- Check if migrations table exists
- Verify file permissions
- Review error messages for SQL syntax issues

### Seeding Errors
- Ensure migrations have been run first
- Check for duplicate data constraints
- Verify foreign key relationships

## Notes

- All timestamps use server timezone (configure in config/app.php)
- Passwords are hashed using bcrypt with cost factor 12
- All tables use InnoDB engine for transaction support
- Foreign keys use CASCADE delete for data integrity
- Indexes are optimized for common query patterns
