<?php
/**
 * View Book Details - Student
 * Digital Library Management System
 * 
 * Displays detailed information about a specific book
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

// Fetch book details
try {
    $stmt = $pdo->prepare("SELECT b.*, u.name as added_by_name 
                           FROM books b 
                           JOIN users u ON b.added_by = u.id 
                           WHERE b.id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setError("Book not found.");
        header("Location: search.php");
        exit();
    }
    
    // Get related books (same subject, excluding current book)
    $stmt = $pdo->prepare("SELECT * FROM books 
                           WHERE subject = ? AND id != ? 
                           ORDER BY RAND() 
                           LIMIT 3");
    $stmt->execute([$book['subject'], $bookId]);
    $relatedBooks = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("View Book Error: " . $e->getMessage());
    setError("Failed to load book details.");
    header("Location: search.php");
    exit();
}

require_once '../includes/header.php';
?>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <a href="dashboard.php">Dashboard</a> &gt; 
    <a href="search.php">Search Books</a> &gt; 
    <span style="color: #6b7280;">Book Details</span>
</div>

<div class="card">
    <div class="book-details">
        <!-- Book Cover/Icon -->
        <div class="book-cover">
            <div class="book-icon">📖</div>
            <h3>PDF Document</h3>
        </div>
        
        <!-- Book Information -->
        <div class="book-info">
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            
            <div class="meta-item">
                <span class="meta-label">Author:</span>
                <span class="meta-value"><?php echo htmlspecialchars($book['author']); ?></span>
            </div>
            
            <div class="meta-item">
                <span class="meta-label">Subject:</span>
                <span class="meta-value">
                    <a href="search.php?subject=<?php echo urlencode($book['subject']); ?>">
                        <?php echo htmlspecialchars($book['subject']); ?>
                    </a>
                </span>
            </div>
            
            <div class="meta-item">
                <span class="meta-label">Added On:</span>
                <span class="meta-value"><?php echo date('F d, Y', strtotime($book['created_at'])); ?></span>
            </div>
            
            <div class="meta-item">
                <span class="meta-label">Added By:</span>
                <span class="meta-value"><?php echo htmlspecialchars($book['added_by_name']); ?></span>
            </div>
            
            <!-- Action Buttons -->
            <div class="book-actions">
                <a href="../<?php echo htmlspecialchars($book['file_path']); ?>" 
                   target="_blank" 
                   class="btn btn-primary">
                    📖 View PDF
                </a>
                <a href="download.php?id=<?php echo $book['id']; ?>" 
                   class="btn btn-success">
                    ⬇️ Download PDF
                </a>
                <a href="search.php" class="btn btn-secondary">
                    ← Back to Search
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Related Books -->
<?php if (!empty($relatedBooks)): ?>
<div class="card">
    <div class="card-header">
        <h2>📚 Related Books in <?php echo htmlspecialchars($book['subject']); ?></h2>
    </div>
    <div class="books-grid">
        <?php foreach ($relatedBooks as $related): ?>
            <div class="book-card">
                <div class="book-card-header">
                    <div class="book-icon">📖</div>
                </div>
                <div class="book-card-body">
                    <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($related['author']); ?></p>
                </div>
                <div class="book-card-footer">
                    <a href="view_book.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Search for More -->
<div class="card" style="text-align: center;">
    <h3>Looking for more books?</h3>
    <p style="color: #6b7280; margin-bottom: 15px;">Search our collection by author or explore other subjects</p>
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="search.php?author=<?php echo urlencode($book['author']); ?>" class="btn btn-secondary">
            More by <?php echo htmlspecialchars($book['author']); ?>
        </a>
        <a href="search.php" class="btn btn-primary">Browse All Books</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
