<?php
/**
 * Create payments table
 * 
 * This migration creates the payments table with indexes on student_id, payment_status, and due_date.
 * Requirements: 20.1, 20.2
 */

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    fee_type ENUM('semester', 'exam', 'lab', 'other') NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    fine_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    due_date DATE NOT NULL,
    payment_date TIMESTAMP NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_status ENUM('pending', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'pending',
    receipt_url VARCHAR(255),
    semester INT,
    academic_year VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_student_status (student_id, payment_status),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
