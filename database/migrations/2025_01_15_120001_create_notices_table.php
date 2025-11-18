<?php
/**
 * Create notices table
 * 
 * This migration creates the notices table with indexes on category, is_active, and published_date.
 * Requirements: 20.1, 20.2
 */

CREATE TABLE notices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('academic', 'events', 'urgent', 'general') NOT NULL,
    target_audience ENUM('all', 'students', 'teachers') NOT NULL DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 0,
    published_by INT NOT NULL,
    published_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP NULL,
    FOREIGN KEY (published_by) REFERENCES users(id),
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_published_date (published_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
