<?php
/**
 * Student Registration Page
 * Digital Library Management System
 * 
 * Allows new students to create an account
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectBasedOnRole();
}

$errors = [];
$name = '';
$email = '';

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Full name is required.";
    } elseif (strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = "Name must be between 2 and 100 characters.";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!validateEmail($email)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        // Check if email already exists
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "This email is already registered. Please login instead.";
            }
        } catch (PDOException $e) {
            error_log("Registration Check Error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again.";
        }
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!validatePassword($password)) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    // Validate password confirmation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // If no errors, create the account
    if (empty($errors)) {
        try {
            // Insert new student
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $stmt->execute([$name, $email, $password]);
            
            // Get the new user's ID
            $user_id = $pdo->lastInsertId();
            
            // Set session variables (auto-login after registration)
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = 'student';
            
            // Set success message and redirect
            setSuccess("Registration successful! Welcome to Digital Library, " . $name . "!");
            header("Location: student/dashboard.php");
            exit();
            
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <div class="card-header">
            <h2>📝 Student Registration</h2>
            <p>Create your account to access the digital library</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?php echo htmlspecialchars($name); ?>"
                       placeholder="Enter your full name"
                       maxlength="100"
                       required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($email); ?>"
                       placeholder="Enter your email"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password"
                       minlength="6"
                       required>
                <small>Password must be at least 6 characters long</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       placeholder="Confirm your password"
                       required>
            </div>
            
            <button type="submit" class="btn btn-success" style="width: 100%;">Create Account</button>
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
