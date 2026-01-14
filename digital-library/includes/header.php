<?php
/**
 * Common Header File
 * Digital Library Management System
 * 
 * Include this at the top of all pages for consistent navigation
 */

require_once __DIR__ . '/auth.php';

// Determine base URL based on current location
$baseUrl = '';
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/student/') !== false) {
    $baseUrl = '../';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Library Management System</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo $baseUrl; ?>index.php">
                    <h1>📚 Digital Library</h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <!-- Admin Navigation -->
                            <li><a href="<?php echo $baseUrl; ?>admin/dashboard.php">Dashboard</a></li>
                            <li><a href="<?php echo $baseUrl; ?>admin/add_book.php">Add Book</a></li>
                            <li><a href="<?php echo $baseUrl; ?>admin/view_books.php">View Books</a></li>
                        <?php else: ?>
                            <!-- Student Navigation -->
                            <li><a href="<?php echo $baseUrl; ?>student/dashboard.php">Dashboard</a></li>
                            <li><a href="<?php echo $baseUrl; ?>student/search.php">Search Books</a></li>
                        <?php endif; ?>
                        <li class="user-info">
                            <span>Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?></span>
                            <span class="role-badge <?php echo getCurrentUserRole(); ?>"><?php echo ucfirst(getCurrentUserRole()); ?></span>
                        </li>
                        <li><a href="<?php echo $baseUrl; ?>logout.php" class="btn-logout">Logout</a></li>
                    <?php else: ?>
                        <!-- Guest Navigation -->
                        <li><a href="<?php echo $baseUrl; ?>login.php">Login</a></li>
                        <li><a href="<?php echo $baseUrl; ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <div class="container">
            <?php echo displayMessages(); ?>
