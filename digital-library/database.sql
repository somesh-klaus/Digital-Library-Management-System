-- =====================================================
-- Digital Library Management System - Database Schema
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS digital_library;
USE digital_library;

-- =====================================================
-- Users Table
-- Stores both admin and student accounts
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Books Table
-- Stores all book information including PDF file paths
-- =====================================================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(150) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    added_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_subject (subject),
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Default Admin Account
-- Email: admin@library.com
-- Password: Admin@123 (plain text)
-- =====================================================
INSERT INTO users (name, email, password, role) VALUES (
    'Administrator',
    'admin@library.com',
    'Admin@123',
    'admin'
);

-- Note: The admin password is 'Admin@123'
