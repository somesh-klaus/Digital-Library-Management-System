<?php
/**
 * Admin Dashboard
 * Digital Library Management System
 * 
 * Main admin control panel showing statistics and quick actions
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require admin access
requireAdmin();

// Get statistics
try {
    // Total books count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM books");
    $totalBooks = $stmt->fetch()['count'];
    
    // Total students count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
    $totalStudents = $stmt->fetch()['count'];
    
    // Get recently added books
    $stmt = $pdo->query("SELECT b.*, u.name as added_by_name 
                         FROM books b 
                         JOIN users u ON b.added_by = u.id 
                         ORDER BY b.created_at DESC 
                         LIMIT 5");
    $recentBooks = $stmt->fetchAll();
    
    // Get unique subjects count
    $stmt = $pdo->query("SELECT COUNT(DISTINCT subject) as count FROM books");
    $totalSubjects = $stmt->fetch()['count'];
    
} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $totalBooks = 0;
    $totalStudents = 0;
    $totalSubjects = 0;
    $recentBooks = [];
}

require_once '../includes/header.php';
?>

<h1 style="margin-bottom: 30px;">👨‍💼 Admin Dashboard</h1>

<!-- Statistics Cards -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-number"><?php echo $totalBooks; ?></div>
        <div class="stat-label">Total Books</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-number"><?php echo $totalStudents; ?></div>
        <div class="stat-label">Registered Students</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📂</div>
        <div class="stat-number"><?php echo $totalSubjects; ?></div>
        <div class="stat-label">Subjects</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-number">1</div>
        <div class="stat-label">Admin Accounts</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h2>⚡ Quick Actions</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="add_book.php" class="btn btn-success">➕ Add New Book</a>
        <a href="view_books.php" class="btn btn-primary">📖 View All Books</a>
    </div>
</div>

<!-- Recent Books -->
<div class="card">
    <div class="card-header">
        <h2>📕 Recently Added Books</h2>
    </div>
    
    <?php if (empty($recentBooks)): ?>
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No Books Yet</h3>
            <p>Start by adding your first book to the library.</p>
            <a href="add_book.php" class="btn btn-success" style="margin-top: 15px;">Add First Book</a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Subject</th>
                        <th>Added On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBooks as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['subject']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                            <td class="actions">
                                <a href="view_books.php" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 15px;">
            <a href="view_books.php">View all books →</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
