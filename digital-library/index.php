<?php
/**
 * Landing Page
 * Digital Library Management System
 * 
 * Main entry point displaying welcome message and features
 */

require_once 'includes/header.php';

// If user is logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    redirectBasedOnRole();
}
?>

<section class="hero">
    <h1>📚 Welcome to Digital Library</h1>
    <p>Your gateway to a vast collection of digital books. Search, read, and download educational resources with ease.</p>
    <div class="hero-buttons">
        <a href="login.php" class="btn btn-primary">Login</a>
        <a href="register.php" class="btn btn-success">Register as Student</a>
    </div>
</section>

<section class="features">
    <div class="feature-card">
        <div class="feature-icon">🔍</div>
        <h3>Search Books</h3>
        <p>Find books easily by title, author, or subject with our powerful search feature.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">📖</div>
        <h3>Read Online</h3>
        <p>Access PDF books directly in your browser without downloading.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">⬇️</div>
        <h3>Download Books</h3>
        <p>Download books to your device for offline reading anytime, anywhere.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <h3>Secure Access</h3>
        <p>Your account is protected with secure authentication and password hashing.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">👨‍💼</div>
        <h3>Admin Management</h3>
        <p>Administrators can easily add, manage, and organize the book collection.</p>
    </div>
    
    <div class="feature-card">
        <div class="feature-icon">🎓</div>
        <h3>Student Friendly</h3>
        <p>Designed specifically for students to access educational materials efficiently.</p>
    </div>
</section>

<div class="card">
    <div class="card-header">
        <h2>About This System</h2>
    </div>
    <p>The Digital Library Management System is a comprehensive platform designed to help students access educational resources digitally. This system allows:</p>
    <ul style="margin: 15px 0 0 20px;">
        <li><strong>Students</strong> - Register, search, view, and download books</li>
        <li><strong>Administrators</strong> - Manage the entire book collection including uploads and deletions</li>
    </ul>
    <p style="margin-top: 15px;"><strong>Default Admin Credentials:</strong></p>
    <ul style="margin: 10px 0 0 20px;">
        <li>Email: admin@library.com</li>
        <li>Password: Admin@123</li>
    </ul>
</div>

<?php require_once 'includes/footer.php'; ?>
