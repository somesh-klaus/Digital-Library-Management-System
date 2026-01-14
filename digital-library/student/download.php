<?php
/**
 * Download Book - Student
 * Digital Library Management System
 * 
 * Handles secure PDF download for students
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require student access
requireStudent();

// Get book ID from query string
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$bookId) {
    setError("Invalid book ID.");
    header("Location: search.php");
    exit();
}

try {
    // Fetch book details
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setError("Book not found.");
        header("Location: search.php");
        exit();
    }
    
    // Get the file path
    $filePath = '../' . $book['file_path'];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        setError("File not found. Please contact administrator.");
        header("Location: search.php");
        exit();
    }
    
    // Get file info
    $fileSize = filesize($filePath);
    $fileName = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '_', $book['title']) . '.pdf';
    
    // Set headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . $fileSize);
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Clear output buffer
    ob_clean();
    flush();
    
    // Read and output file
    readfile($filePath);
    exit();
    
} catch (PDOException $e) {
    error_log("Download Error: " . $e->getMessage());
    setError("Failed to download book. Please try again.");
    header("Location: search.php");
    exit();
}
?>
