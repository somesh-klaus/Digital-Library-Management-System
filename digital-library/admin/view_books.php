<?php
/**
 * View All Books - Admin
 * Digital Library Management System
 * 
 * Displays all books with management options
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require admin access
requireAdmin();

// Get filter parameters
$searchTitle = sanitizeInput($_GET['title'] ?? '');
$searchAuthor = sanitizeInput($_GET['author'] ?? '');
$searchSubject = sanitizeInput($_GET['subject'] ?? '');

// Build query with optional filters
try {
    $sql = "SELECT b.*, u.name as added_by_name 
            FROM books b 
            JOIN users u ON b.added_by = u.id 
            WHERE 1=1";
    $params = [];
    
    if (!empty($searchTitle)) {
        $sql .= " AND b.title LIKE ?";
        $params[] = "%$searchTitle%";
    }
    
    if (!empty($searchAuthor)) {
        $sql .= " AND b.author LIKE ?";
        $params[] = "%$searchAuthor%";
    }
    
    if (!empty($searchSubject)) {
        $sql .= " AND b.subject LIKE ?";
        $params[] = "%$searchSubject%";
    }
    
    $sql .= " ORDER BY b.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    // Get subjects for filter dropdown
    $stmt = $pdo->query("SELECT DISTINCT subject FROM books ORDER BY subject ASC");
    $subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    error_log("View Books Error: " . $e->getMessage());
    $books = [];
    $subjects = [];
    setError("Failed to load books.");
}

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>📚 Manage Books</h2>
        <p>View, filter, and manage all books in the library</p>
    </div>
    
    <!-- Search/Filter Form -->
    <form method="GET" action="view_books.php" class="search-form">
        <div class="form-group">
            <input type="text" 
                   name="title" 
                   value="<?php echo htmlspecialchars($searchTitle); ?>"
                   placeholder="Search by title...">
        </div>
        
        <div class="form-group">
            <input type="text" 
                   name="author" 
                   value="<?php echo htmlspecialchars($searchAuthor); ?>"
                   placeholder="Search by author...">
        </div>
        
        <div class="form-group">
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
        
        <button type="submit" class="btn btn-primary">🔍 Search</button>
        <a href="view_books.php" class="btn btn-secondary">Clear</a>
    </form>
    
    <!-- Results Count -->
    <p style="margin-bottom: 15px; color: #6b7280;">
        Found <strong><?php echo count($books); ?></strong> book(s)
    </p>
    
    <?php if (empty($books)): ?>
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No Books Found</h3>
            <?php if (!empty($searchTitle) || !empty($searchAuthor) || !empty($searchSubject)): ?>
                <p>No books match your search criteria.</p>
                <a href="view_books.php" class="btn btn-secondary" style="margin-top: 15px;">Clear Filters</a>
            <?php else: ?>
                <p>Start by adding your first book to the library.</p>
                <a href="add_book.php" class="btn btn-success" style="margin-top: 15px;">Add First Book</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Subject</th>
                        <th>Added By</th>
                        <th>Added On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['id']; ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['subject']); ?></td>
                            <td><?php echo htmlspecialchars($book['added_by_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                            <td class="actions">
                                <a href="../<?php echo htmlspecialchars($book['file_path']); ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-primary">View PDF</a>
                                <a href="delete_book.php?id=<?php echo $book['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Add Button -->
<div style="text-align: center; margin-top: 20px;">
    <a href="add_book.php" class="btn btn-success">➕ Add New Book</a>
</div>

<?php require_once '../includes/footer.php'; ?>
