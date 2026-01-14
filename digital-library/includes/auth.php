<?php
/**
 * Authentication Functions
 * Digital Library Management System
 * 
 * Contains all authentication related functions:
 * - Session management
 * - Role-based access control
 * - Input validation
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if logged in user is admin
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if logged in user is student
 * @return bool True if student, false otherwise
 */
function isStudent() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Require admin access - redirects if not admin
 * Use this at the top of admin-only pages
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = "Access denied. Admin privileges required.";
        header("Location: ../login.php");
        exit();
    }
}

/**
 * Require student access - redirects if not student
 * Use this at the top of student-only pages
 */
function requireStudent() {
    if (!isStudent()) {
        $_SESSION['error'] = "Access denied. Please login as a student.";
        header("Location: ../login.php");
        exit();
    }
}

/**
 * Require any logged in user
 * Redirects to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to continue.";
        header("Location: ../login.php");
        exit();
    }
}

/**
 * Sanitize user input to prevent XSS attacks
 * @param string $data Raw input data
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * Minimum 6 characters
 * @param string $password Password to validate
 * @return bool True if valid, false otherwise
 */
function validatePassword($password) {
    return strlen($password) >= 6;
}

/**
 * Get current user's name
 * @return string User name or 'Guest'
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Get current user's role
 * @return string User role or empty string
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? '';
}

/**
 * Set flash message for success
 * @param string $message Success message
 */
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Set flash message for error
 * @param string $message Error message
 */
function setError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Display and clear flash messages
 * Returns HTML for success/error messages
 */
function displayMessages() {
    $output = '';
    
    if (isset($_SESSION['success'])) {
        $output .= '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    
    if (isset($_SESSION['error'])) {
        $output .= '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    
    return $output;
}

/**
 * Redirect user based on role after login
 */
function redirectBasedOnRole() {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else if (isStudent()) {
        header("Location: student/dashboard.php");
    } else {
        header("Location: login.php");
    }
    exit();
}

/**
 * Generate CSRF token for forms
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
