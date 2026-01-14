<?php
/**
 * Logout Handler
 * Digital Library Management System
 * 
 * Destroys user session and redirects to login page
 */

require_once 'includes/auth.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start new session for flash message
session_start();
$_SESSION['success'] = "You have been logged out successfully.";

// Redirect to login page
header("Location: login.php");
exit();
?>
