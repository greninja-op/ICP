# Database Schema - University Student Portal

## Database Design Overview

This document provides the complete database schema for the University Student Portal. The schema is designed for MySQL 8.0+ or PostgreSQL 14+.

## Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   users     │────────>│  students    │────────>│ enrollments │
└─────────────┘         └──────────────┘         └─────────────┘
      │                        │                         │
      │                        │                         │
      v                        v                         v
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   faculty   │         │   payments   │         │  subjects   │
└─────────────┘         └──────────────┘         └─────────────┘
      │                        │                         │
      │                        │                         │
      v                        v                         v
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   courses   │         │   results    │         │ attendance  │
└─────────────┘         └──────────────┘         └─────────────┘
      │                                                  │
      │                                                  │
      v                                                  v
┌─────────────┐                                  ┌─────────────┐
│   notices   │                                  │ departments │
└─────────────┘                                  └─────────────┘
```

## Table Definitions

### 1. users
Core user authentication table for all system users.

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
);
```

**Fields Explanation:**
- `id`: Unique identifier for each user
- `username`: Login username (unique)
- `email`: User email address (unique)
- `password_hash`: Bcrypt/Argon2 hashed password
- `role`: User role (student/faculty/admin)
- `is_active`: Account status (active/inactive)
- `email_verified`: Email verification status
- `last_login`: Last login timestamp
- `created_at`: Account creation timestamp
- `updated_at`: Last update timestamp

---

### 2. students
Extended profile information for students.

```sql
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    profile_photo VARCHAR(255),
    department_id INT,
    current_semester INT DEFAULT 1,
    enrollment_year INT NOT NULL,
    blood_group VARCHAR(5),
    emergency_contact VARCHAR(15),
    parent_name VARCHAR(100),
    parent_phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    INDEX idx_student_id (student_id),
    INDEX idx_department (department_id),
    INDEX idx_semester (current_semester)
);
```

---

### 3. faculty
Faculty member profiles.

```sql
CREATE TABLE faculty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    faculty_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    designation VARCHAR(50),
    department_id INT,
    phone VARCHAR(15),
    office_location VARCHAR(100),
    specialization VARCHAR(100),
    qualification VARCHAR(100),
    experience_years INT,
    profile_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    INDEX idx_faculty_id (faculty_id),
    INDEX idx_department (department_id)
);
```

---

### 4. departments
Academic departments.

```sql
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_code VARCHAR(10) UNIQUE NOT NULL,
    department_name VARCHAR(100) NOT NULL,
    head_of_department INT,
    description TEXT,
    established_year INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (head_of_department) REFERENCES faculty(id),
    INDEX idx_dept_code (department_code)
);
```

---

### 5. subjects
Course/Subject master data.

```sql
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    department_id INT,
    credits INT DEFAULT 3,
    semester INT NOT NULL,
    subject_type ENUM('theory', 'lab', 'practical', 'project') DEFAULT 'theory',
    max_internal_marks INT DEFAULT 20,
    max_theory_marks INT DEFAULT 80,
    max_total_marks INT DEFAULT 100,
    description TEXT,
    syllabus_file VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    INDEX idx_subject_code (subject_code),
    INDEX idx_semester (semester),
    INDEX idx_department (department_id)
);
```

---

### 6. courses
Course offerings (subject + faculty + semester).

```sql
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    faculty_id INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    semester INT NOT NULL,
    section VARCHAR(5),
    max_students INT DEFAULT 60,
    schedule TEXT,
    classroom VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id),
    INDEX idx_academic_year (academic_year),
    INDEX idx_semester (semester),
    INDEX idx_faculty (faculty_id)
);
```

---

### 7. enrollments
Student course enrollments.

```sql
CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('enrolled', 'dropped', 'completed') DEFAULT 'enrolled',
    grade VARCHAR(5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    UNIQUE KEY unique_enrollment (student_id, course_id),
    INDEX idx_student (student_id),
    INDEX idx_course (course_id),
    INDEX idx_status (status)
);
```

---

### 8. results
Examination results and marks.

```sql
CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    internal_marks DECIMAL(5,2) DEFAULT 0,
    theory_marks DECIMAL(5,2) DEFAULT 0,
    practical_marks DECIMAL(5,2) DEFAULT 0,
    total_marks DECIMAL(5,2) DEFAULT 0,
    grade VARCHAR(5),
    grade_points DECIMAL(3,2),
    remarks TEXT,
    result_date DATE,
    is_published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    UNIQUE KEY unique_result (student_id, subject_id, semester, academic_year),
    INDEX idx_student (student_id),
    INDEX idx_semester (semester),
    INDEX idx_academic_year (academic_year),
    INDEX idx_published (is_published)
);
```

---

### 9. semester_summary
Semester-wise GPA and summary.

```sql
CREATE TABLE semester_summary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    total_credits INT DEFAULT 0,
    credits_earned INT DEFAULT 0,
    sgpa DECIMAL(4,2) DEFAULT 0.00,
    cgpa DECIMAL(4,2) DEFAULT 0.00,
    total_marks_obtained DECIMAL(7,2) DEFAULT 0,
    total_marks_maximum DECIMAL(7,2) DEFAULT 0,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    class_rank INT,
    total_students INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_semester (student_id, semester, academic_year),
    INDEX idx_student (student_id),
    INDEX idx_semester (semester)
);
```

---

### 10. attendance
Student attendance records.

```sql
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'present',
    remarks TEXT,
    marked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (marked_by) REFERENCES faculty(id),
    UNIQUE KEY unique_attendance (student_id, course_id, attendance_date),
    INDEX idx_student (student_id),
    INDEX idx_course (course_id),
    INDEX idx_date (attendance_date)
);
```

---

### 11. fee_structure
Fee structure by semester and department.

```sql
CREATE TABLE fee_structure (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    tuition_fee DECIMAL(10,2) NOT NULL,
    exam_fee DECIMAL(10,2) DEFAULT 0,
    library_fee DECIMAL(10,2) DEFAULT 0,
    lab_fee DECIMAL(10,2) DEFAULT 0,
    sports_fee DECIMAL(10,2) DEFAULT 0,
    other_fee DECIMAL(10,2) DEFAULT 0,
    total_fee DECIMAL(10,2) NOT NULL,
    due_date DATE,
    late_fee_per_day DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    INDEX idx_department (department_id),
    INDEX idx_semester (semester),
    INDEX idx_academic_year (academic_year)
);
```

---

### 12. payments
Student payment records.

```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id VARCHAR(50) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    fee_type ENUM('semester', 'exam', 'library', 'hostel', 'other') NOT NULL,
    semester INT,
    academic_year VARCHAR(10),
    amount DECIMAL(10,2) NOT NULL,
    late_fee DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('card', 'upi', 'netbanking', 'cash', 'cheque') NOT NULL,
    payment_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    gateway_response TEXT,
    payment_date TIMESTAMP NULL,
    receipt_number VARCHAR(50),
    receipt_url VARCHAR(255),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_payment_id (payment_id),
    INDEX idx_student (student_id),
    INDEX idx_status (payment_status),
    INDEX idx_payment_date (payment_date),
    INDEX idx_semester (semester)
);
```

---

### 13. notices
Announcements and notices.

```sql
CREATE TABLE notices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('urgent', 'academic', 'event', 'general', 'campus_life') DEFAULT 'general',
    priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    target_audience ENUM('all', 'students', 'faculty', 'department') DEFAULT 'all',
    department_id INT NULL,
    attachment_url VARCHAR(255),
    published_by INT NOT NULL,
    published_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (published_by) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    INDEX idx_category (category),
    INDEX idx_priority (priority),
    INDEX idx_published_date (published_date),
    INDEX idx_active (is_active)
);
```

---

### 14. notice_reads
Track which notices have been read by users.

```sql
CREATE TABLE notice_reads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notice_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_read (notice_id, user_id),
    INDEX idx_notice (notice_id),
    INDEX idx_user (user_id)
);
```

---

### 15. password_resets
Password reset tokens.

```sql
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user (user_id)
);
```

---

### 16. sessions
User session management (optional if using JWT).

```sql
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(500) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_token (token(255)),
    INDEX idx_expires (expires_at)
);
```

---

### 17. audit_logs
System audit trail.

```sql
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_table (table_name),
    INDEX idx_created (created_at)
);
```

---

## Indexes Summary

Key indexes for performance optimization:

```sql
-- Users table
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);

-- Students table
CREATE INDEX idx_students_student_id ON students(student_id);
CREATE INDEX idx_students_department ON students(department_id);
CREATE INDEX idx_students_semester ON students(current_semester);

-- Results table
CREATE INDEX idx_results_student ON results(student_id);
CREATE INDEX idx_results_semester ON results(semester);
CREATE INDEX idx_results_published ON results(is_published);

-- Payments table
CREATE INDEX idx_payments_student ON payments(student_id);
CREATE INDEX idx_payments_status ON payments(payment_status);
CREATE INDEX idx_payments_date ON payments(payment_date);

-- Attendance table
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_attendance_date ON attendance(attendance_date);
```

---

## Sample Queries

### Get student dashboard data
```sql
SELECT 
    s.student_id,
    s.first_name,
    s.last_name,
    s.current_semester,
    d.department_name,
    ss.sgpa,
    ss.cgpa,
    (SELECT COUNT(*) FROM enrollments WHERE student_id = s.id AND status = 'enrolled') as enrolled_courses,
    (SELECT SUM(total_amount) FROM payments WHERE student_id = s.id AND payment_status = 'pending') as pending_fees
FROM students s
LEFT JOIN departments d ON s.department_id = d.id
LEFT JOIN semester_summary ss ON s.id = ss.student_id AND ss.semester = s.current_semester
WHERE s.user_id = ?;
```

### Get semester results
```sql
SELECT 
    sub.subject_code,
    sub.subject_name,
    r.internal_marks,
    r.theory_marks,
    r.total_marks,
    r.grade,
    r.grade_points
FROM results r
JOIN subjects sub ON r.subject_id = sub.id
WHERE r.student_id = ? AND r.semester = ? AND r.is_published = TRUE
ORDER BY sub.subject_code;
```

### Get payment history
```sql
SELECT 
    p.payment_id,
    p.fee_type,
    p.semester,
    p.total_amount,
    p.payment_method,
    p.payment_status,
    p.payment_date,
    p.receipt_number
FROM payments p
WHERE p.student_id = ?
ORDER BY p.payment_date DESC;
```

---

## Database Initialization Script

Complete initialization script available in `database_init.sql` file.

---

**Document Version**: 1.0.0  
**Last Updated**: November 19, 2025
