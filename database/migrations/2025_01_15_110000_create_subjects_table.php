<?php
/**
 * Create subjects table
 * 
 * This migration creates the subjects table with indexes on subject_code, department, and semester.
 * Requirements: 20.1, 20.2
 */

CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    credits INT NOT NULL,
    is_lab BOOLEAN DEFAULT FALSE,
    teacher_id INT,
    description TEXT,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
    INDEX idx_subject_code (subject_code),
    INDEX idx_department_semester (department, semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
