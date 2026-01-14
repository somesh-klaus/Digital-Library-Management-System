<?php
/**
 * Student Dashboard
 * Digital Library Management System
 * 
 * Main student panel showing available books and quick search
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require student access
requireStudent();

// Get statistics
try {
    // Total books count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM books");
    $totalBooks = $stmt->fetch()['count'];
    
    // Get unique subjects
    $stmt = $pdo->query("SELECT COUNT(DISTINCT subject) as count FROM books");
    $totalSubjects = $stmt->fetch()['count'];
    
    // Get unique authors
    $stmt = $pdo->query("SELECT COUNT(DISTINCT author) as count FROM books");
    $totalAuthors = $stmt->fetch()['count'];
    
    // Get recently added books
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 6");
    $recentBooks = $stmt->fetchAll();
    
    // Get list of subjects for quick browse
    $stmt = $pdo->query("SELECT DISTINCT subject, COUNT(*) as count 
                         FROM books 
                         GROUP BY subject 
                         ORDER BY count DESC 
                         LIMIT 5");
    $topSubjects = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Student Dashboard Error: " . $e->getMessage());
    $totalBooks = 0;
    $totalSubjects = 0;
    $totalAuthors = 0;
    $recentBooks = [];
    $topSubjects = [];
}

require_once '../includes/header.php';
?>

<h1 style="margin-bottom: 30px;">🎓 Student Dashboard</h1>

<!-- Welcome Message -->
<div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; margin-bottom: 30px;">
    <h2>Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>!</h2>
    <p>Browse our digital library collection and download books for your studies.</p>
</div>

<!-- Statistics Cards -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-number"><?php echo $totalBooks; ?></div>
        <div class="stat-label">Total Books</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📂</div>
        <div class="stat-number"><?php echo $totalSubjects; ?></div>
        <div class="stat-label">Subjects</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">✍️</div>
        <div class="stat-number"><?php echo $totalAuthors; ?></div>
        <div class="stat-label">Authors</div>
    </div>
</div>

<!-- Quick Search -->
<div class="card">
    <div class="card-header">
        <h2>🔍 Quick Search</h2>
    </div>
    <form method="GET" action="search.php" class="search-form">
        <div class="form-group" style="flex: 2;">
            <input type="text" 
                   name="q" 
                   placeholder="Search by title, author, or subject..."
                   required>
        </div>
        <button type="submit" class="btn btn-primary">Search Books</button>
    </form>
</div>

<!-- Top Subjects -->
<?php if (!empty($topSubjects)): ?>
<div class="card">
    <div class="card-header">
        <h2>📁 Browse by Subject</h2>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php foreach ($topSubjects as $subject): ?>
            <a href="search.php?subject=<?php echo urlencode($subject['subject']); ?>" 
               class="btn btn-secondary">
                <?php echo htmlspecialchars($subject['subject']); ?> 
                <span style="opacity: 0.7;">(<?php echo $subject['count']; ?>)</span>
            </a>
        <?php endforeach; ?>
        <a href="search.php" class="btn btn-primary">View All →</a>
    </div>
</div>
<?php endif; ?>

<!-- Recent Books -->
<div class="card">
    <div class="card-header">
        <h2>📕 Recently Added Books</h2>
    </div>
    
    <?php if (empty($recentBooks)): ?>
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No Books Available</h3>
            <p>The library is currently empty. Please check back later.</p>
        </div>
    <?php else: ?>
        <div class="books-grid">
            <?php foreach ($recentBooks as $book): ?>
                <div class="book-card">
                    <div class="book-card-header">
                        <div class="book-icon">📖</div>
                    </div>
                    <div class="book-card-body">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($book['subject']); ?></p>
                    </div>
                    <div class="book-card-footer">
                        <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="search.php" class="btn btn-primary">View All Books →</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
