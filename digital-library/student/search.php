<?php
/**
 * Search Books - Student
 * Digital Library Management System
 * 
 * Allows students to search books by title, author, or subject
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require student access
requireStudent();

// Get search parameters
$searchQuery = sanitizeInput($_GET['q'] ?? '');
$searchTitle = sanitizeInput($_GET['title'] ?? '');
$searchAuthor = sanitizeInput($_GET['author'] ?? '');
$searchSubject = sanitizeInput($_GET['subject'] ?? '');

// Build query with filters
try {
    $sql = "SELECT * FROM books WHERE 1=1";
    $params = [];
    
    // General search (searches in title, author, and subject)
    if (!empty($searchQuery)) {
        $sql .= " AND (title LIKE ? OR author LIKE ? OR subject LIKE ?)";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
    }
    
    // Specific field searches
    if (!empty($searchTitle)) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$searchTitle%";
    }
    
    if (!empty($searchAuthor)) {
        $sql .= " AND author LIKE ?";
        $params[] = "%$searchAuthor%";
    }
    
    if (!empty($searchSubject)) {
        $sql .= " AND subject LIKE ?";
        $params[] = "%$searchSubject%";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    // Get all unique subjects for filter dropdown
    $stmt = $pdo->query("SELECT DISTINCT subject FROM books ORDER BY subject ASC");
    $subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get all unique authors for filter dropdown
    $stmt = $pdo->query("SELECT DISTINCT author FROM books ORDER BY author ASC");
    $authors = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    error_log("Search Books Error: " . $e->getMessage());
    $books = [];
    $subjects = [];
    $authors = [];
    setError("Failed to search books.");
}

$hasFilters = !empty($searchQuery) || !empty($searchTitle) || !empty($searchAuthor) || !empty($searchSubject);

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>🔍 Search Books</h2>
        <p>Find books by title, author, or subject</p>
    </div>
    
    <!-- Quick Search -->
    <form method="GET" action="search.php" style="margin-bottom: 20px;">
        <div class="search-form">
            <div class="form-group" style="flex: 2;">
                <input type="text" 
                       name="q" 
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       placeholder="Search by title, author, or subject...">
            </div>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
        </div>
    </form>
    
    <!-- Advanced Filters -->
    <details style="margin-bottom: 20px;">
        <summary style="cursor: pointer; color: #2563eb; font-weight: 600;">⚙️ Advanced Filters</summary>
        <form method="GET" action="search.php" class="search-form" style="margin-top: 15px;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" 
                       name="title" 
                       value="<?php echo htmlspecialchars($searchTitle); ?>"
                       placeholder="Search by title...">
            </div>
            
            <div class="form-group">
                <label>Author</label>
                <select name="author">
                    <option value="">All Authors</option>
                    <?php foreach ($authors as $auth): ?>
                        <option value="<?php echo htmlspecialchars($auth); ?>" 
                                <?php echo ($searchAuthor === $auth) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($auth); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Subject</label>
                <select name="subject">
                    <option value="">All Subjects</option>
                    <?php foreach ($subjects as $subj): ?>
                        <option value="<?php echo htmlspecialchars($subj); ?>" 
                                <?php echo ($searchSubject === $subj) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subj); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="search.php" class="btn btn-secondary">Clear All</a>
        </form>
    </details>
    
    <!-- Results Count -->
    <p style="margin-bottom: 15px; color: #6b7280;">
        <?php if ($hasFilters): ?>
            Found <strong><?php echo count($books); ?></strong> book(s) matching your search
        <?php else: ?>
            Showing all <strong><?php echo count($books); ?></strong> book(s)
        <?php endif; ?>
    </p>
</div>

<!-- Search Results -->
<?php if (empty($books)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No Books Found</h3>
            <?php if ($hasFilters): ?>
                <p>No books match your search criteria. Try different keywords or clear filters.</p>
                <a href="search.php" class="btn btn-secondary" style="margin-top: 15px;">Clear Search</a>
            <?php else: ?>
                <p>The library is currently empty. Please check back later.</p>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="books-grid">
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <div class="book-card-header">
                    <div class="book-icon">📖</div>
                </div>
                <div class="book-card-body">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($book['subject']); ?></p>
                    <p style="font-size: 0.8rem; color: #9ca3af;">
                        Added: <?php echo date('M d, Y', strtotime($book['created_at'])); ?>
                    </p>
                </div>
                <div class="book-card-footer">
                    <a href="view_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                    <a href="download.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-success">Download</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
