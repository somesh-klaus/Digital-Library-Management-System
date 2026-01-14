<?php
/**
 * Login Page
 * Digital Library Management System
 * 
 * Handles user authentication for both admin and students
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}

$errors = [];

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!validateEmail($email)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    // If no validation errors, attempt login
    if (empty($errors)) {
        try {
            // Prepare statement to find user by email
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            // Verify user exists and password is correct
            if ($user && $password === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Set success message and redirect
                setSuccess("Welcome back, " . $user['name'] . "!");
                redirectBasedOnRole();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <div class="card-header">
            <h2>🔐 Login</h2>
            <p>Enter your credentials to access the library</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                       placeholder="Enter your email"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password"
                       required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Register as Student</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
