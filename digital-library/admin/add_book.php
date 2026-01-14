<?php
/**
 * Add Book Page
 * Digital Library Management System
 * 
 * Allows admin to add new books with PDF upload
 */

require_once '../config/database.php';
require_once '../includes/auth.php';

// Require admin access
requireAdmin();

$errors = [];
$success = false;

// Form field values for repopulating on error
$title = '';
$author = '';
$subject = '';

// Allowed file types for upload
$allowedTypes = ['application/pdf'];
$maxFileSize = 10 * 1024 * 1024; // 10MB max file size

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $title = sanitizeInput($_POST['title'] ?? '');
    $author = sanitizeInput($_POST['author'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    
    // Validate title
    if (empty($title)) {
        $errors[] = "Book title is required.";
    } elseif (strlen($title) > 255) {
        $errors[] = "Book title must not exceed 255 characters.";
    }
    
    // Validate author
    if (empty($author)) {
        $errors[] = "Author name is required.";
    } elseif (strlen($author) > 150) {
        $errors[] = "Author name must not exceed 150 characters.";
    }
    
    // Validate subject
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    } elseif (strlen($subject) > 100) {
        $errors[] = "Subject must not exceed 100 characters.";
    }
    
    // Validate file upload
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Please upload a PDF file.";
    } elseif ($_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error. Please try again.";
    } else {
        $file = $_FILES['pdf_file'];
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = "Only PDF files are allowed.";
        }
        
        // Check file size
        if ($file['size'] > $maxFileSize) {
            $errors[] = "File size must not exceed 10MB.";
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            $errors[] = "Only PDF files are allowed.";
        }
    }
    
    // If no errors, proceed with upload and database insert
    if (empty($errors)) {
        // Create uploads directory if it doesn't exist
        $uploadsDir = '../uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        // Generate unique filename to prevent overwrites
        $uniqueName = uniqid() . '_' . time() . '.pdf';
        $filePath = $uploadsDir . $uniqueName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $filePath)) {
            try {
                // Insert book record into database
                $stmt = $pdo->prepare("INSERT INTO books (title, author, subject, file_path, added_by) 
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $title,
                    $author,
                    $subject,
                    'uploads/' . $uniqueName, // Store relative path
                    $_SESSION['user_id']
                ]);
                
                // Clear form fields and set success
                $title = '';
                $author = '';
                $subject = '';
                setSuccess("Book added successfully!");
                $success = true;
                
            } catch (PDOException $e) {
                error_log("Add Book Error: " . $e->getMessage());
                $errors[] = "Failed to add book. Please try again.";
                // Delete uploaded file if database insert fails
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            $errors[] = "Failed to upload file. Please try again.";
        }
    }
}

// Get list of existing subjects for datalist
try {
    $stmt = $pdo->query("SELECT DISTINCT subject FROM books ORDER BY subject ASC");
    $existingSubjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $existingSubjects = [];
}

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>➕ Add New Book</h2>
        <p>Upload a PDF book to the digital library</p>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="add_book.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Book Title *</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   value="<?php echo htmlspecialchars($title); ?>"
                   placeholder="Enter book title"
                   maxlength="255"
                   required>
        </div>
        
        <div class="form-group">
            <label for="author">Author Name *</label>
            <input type="text" 
                   id="author" 
                   name="author" 
                   value="<?php echo htmlspecialchars($author); ?>"
                   placeholder="Enter author name"
                   maxlength="150"
                   required>
        </div>
        
        <div class="form-group">
            <label for="subject">Subject *</label>
            <input type="text" 
                   id="subject" 
                   name="subject" 
                   value="<?php echo htmlspecialchars($subject); ?>"
                   placeholder="Enter subject (e.g., Computer Science, Mathematics)"
                   list="subjects"
                   maxlength="100"
                   required>
            <datalist id="subjects">
                <?php foreach ($existingSubjects as $subj): ?>
                    <option value="<?php echo htmlspecialchars($subj); ?>">
                <?php endforeach; ?>
            </datalist>
            <small>Select from existing subjects or type a new one</small>
        </div>
        
        <div class="form-group">
            <label for="pdf_file">PDF File *</label>
            <input type="file" 
                   id="pdf_file" 
                   name="pdf_file" 
                   accept=".pdf,application/pdf"
                   required>
            <small>Maximum file size: 10MB. Only PDF files are allowed.</small>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" class="btn btn-success">📤 Upload Book</button>
            <a href="view_books.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Upload Guidelines -->
<div class="card">
    <div class="card-header">
        <h2>📋 Upload Guidelines</h2>
    </div>
    <ul style="margin-left: 20px;">
        <li>Only PDF files are accepted</li>
        <li>Maximum file size is 10MB</li>
        <li>Use clear and descriptive titles</li>
        <li>Enter the complete author name</li>
        <li>Choose an appropriate subject category</li>
        <li>Ensure the PDF is readable and not corrupted</li>
    </ul>
</div>

<?php require_once '../includes/footer.php'; ?>
