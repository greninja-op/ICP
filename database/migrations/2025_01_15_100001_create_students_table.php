<?php
/**
 * Create students table
 * 
 * This migration creates the students table with foreign key to users.
 * Requirements: 20.1, 20.2, 20.3
 */

CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    admission_year INT NOT NULL,
    date_of_birth DATE,
    address TEXT,
    guardian_name VARCHAR(100),
    guardian_phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_department (department),
    INDEX idx_semester (semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
