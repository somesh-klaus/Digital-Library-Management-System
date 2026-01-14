<?php
/**
 * Database Configuration File
 * Digital Library Management System
 * 
 * This file handles the MySQL database connection using PDO
 * Ensure XAMPP MySQL service is running before use
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'digital_library');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP MySQL has no password

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Create database connection using PDO
 * Returns PDO object on success
 */
function getDBConnection() {
    try {
        // Create PDO connection with error mode exception
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Log error and display user-friendly message
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database connection failed. Please ensure MySQL is running in XAMPP.");
    }
}

// Create global database connection
$pdo = getDBConnection();
?>
