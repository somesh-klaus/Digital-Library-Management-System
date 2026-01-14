<?php
/**
 * Delete Book Handler
 * Digital Library Management System
 * 
 * Handles book deletion with file removal
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require admin access
requireAdmin();

// Get book ID from query string
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$bookId) {
    setError("Invalid book ID.");
    header("Location: view_books.php");
    exit();
}

try {
    // First, get the book details to find the file path
    $stmt = $pdo->prepare("SELECT id, title, file_path FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setError("Book not found.");
        header("Location: view_books.php");
        exit();
    }
    
    // Delete the physical PDF file
    $filePath = '../' . $book['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Delete the book record from database
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    
    setSuccess("Book '" . $book['title'] . "' has been deleted successfully.");
    
} catch (PDOException $e) {
    error_log("Delete Book Error: " . $e->getMessage());
    setError("Failed to delete book. Please try again.");
}

// Redirect back to view books page
header("Location: view_books.php");
exit();
?>
