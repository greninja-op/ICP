<?php
/**
 * Create results table
 * 
 * This migration creates the results table with composite unique key.
 * Requirements: 20.1, 20.2
 */

CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    internal_marks INT,
    theory_marks INT,
    lab_marks INT,
    total_marks INT NOT NULL,
    grade VARCHAR(2) NOT NULL,
    grade_point INT NOT NULL,
    credit_points INT NOT NULL,
    result_status ENUM('pass', 'fail') NOT NULL,
    published_date TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_result (student_id, subject_id, semester, academic_year),
    INDEX idx_student_semester (student_id, semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
