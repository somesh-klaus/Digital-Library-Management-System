# Digital Library Management System

A complete, fully functional Digital Library Management System built with PHP, MySQL, HTML, and CSS. This system is designed for college/university environments where students can browse, search, and download educational materials, while administrators can manage the book collection.

## 📋 Features

### Authentication System
- ✅ Student registration with email validation
- ✅ Secure login for both Admin and Students
- ✅ Simple password verification
- ✅ Session-based authentication
- ✅ Role-based access control

### Admin Module
- ✅ Admin Dashboard with statistics
- ✅ Add new books with PDF upload
- ✅ View all books with search/filter
- ✅ Delete books (removes file and database record)
- ✅ Protected admin routes

### Student Module
- ✅ Student Dashboard with quick search
- ✅ Browse books by subject
- ✅ Search books by title, author, or subject
- ✅ View book details
- ✅ Download PDF files
- ✅ View PDF in browser
- ✅ Related books suggestions

## 🛠️ Tech Stack

- **Backend:** PHP 8+ (Procedural)
- **Database:** MySQL
- **Frontend:** HTML5 + CSS3
- **Authentication:** PHP Sessions
- **No JavaScript dependencies**
- **No CSS frameworks**

## 📁 Project Structure

```
digital-library/
├── config/
│   └── database.php          # Database connection (PDO)
├── includes/
│   ├── header.php            # Common header with navigation
│   ├── footer.php            # Common footer
│   └── auth.php              # Authentication functions
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── add_book.php          # Add new book form
│   ├── view_books.php        # View/manage all books
│   └── delete_book.php       # Delete book handler
├── student/
│   ├── dashboard.php         # Student dashboard
│   ├── search.php            # Search books
│   ├── view_book.php         # View book details
│   └── download.php          # Download PDF handler
├── uploads/                  # PDF storage folder
├── css/
│   └── style.css             # Main stylesheet
├── index.php                 # Landing page
├── login.php                 # Login page
├── register.php              # Student registration
├── logout.php                # Logout handler
├── database.sql              # Database schema
└── README.md                 # This file
```

## ⚙️ Installation Instructions

### Prerequisites
- XAMPP (or similar) with Apache and MySQL
- PHP 8.0 or higher
- Web browser

### Step 1: Copy Project Files
1. Copy the entire `digital-library` folder to your XAMPP's `htdocs` directory:
   ```
   C:\xampp\htdocs\digital-library\
   ```

### Step 2: Create Database
1. Start XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Open phpMyAdmin: http://localhost/phpmyadmin
4. Click on **"Import"** tab
5. Choose the `database.sql` file from the project folder
6. Click **"Go"** to execute

**OR** manually:
1. Create a new database named `digital_library`
2. Select the database
3. Go to SQL tab
4. Copy and paste contents of `database.sql`
5. Click "Go" to execute

### Step 3: Configure Database (if needed)
If your MySQL has a password, edit `config/database.php`:
```php
define('DB_PASS', 'your_password_here');
```

### Step 4: Access the System
Open your browser and navigate to:
```
http://localhost/digital-library/
```

## 🔐 Default Login Credentials

### Admin Account
- **Email:** admin@library.com
- **Password:** Admin@123

### Student Account
- Register a new account through the registration page

## 📊 Database Schema

### Users Table
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary Key, Auto Increment |
| name | VARCHAR(100) | User's full name |
| email | VARCHAR(100) | Unique email address |
| password | VARCHAR(255) | Hashed password |
| role | ENUM | 'admin' or 'student' |
| created_at | TIMESTAMP | Registration date |

### Books Table
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary Key, Auto Increment |
| title | VARCHAR(255) | Book title |
| author | VARCHAR(150) | Author name |
| subject | VARCHAR(100) | Subject category |
| file_path | VARCHAR(255) | Path to PDF file |
| added_by | INT | Foreign Key to users |
| created_at | TIMESTAMP | Upload date |

## 🔒 Security Features

1. **Password Storage:** Passwords are stored and compared directly
2. **Prepared Statements:** All database queries use PDO prepared statements to prevent SQL injection
3. **Input Sanitization:** All user inputs are sanitized using `htmlspecialchars()` and custom functions
4. **Session Security:** Proper session management with secure logout
5. **Role-Based Access:** Admin and student pages are protected with role checks
6. **File Upload Validation:** Only PDF files are allowed, with MIME type and extension verification
7. **File Size Limit:** Maximum upload size is 10MB

## 📝 Usage Guide

### For Students
1. Register a new account from the homepage
2. Login with your credentials
3. Browse or search for books
4. View book details
5. Download or view PDFs

### For Administrators
1. Login with admin credentials
2. View dashboard statistics
3. Add new books with PDF upload
4. Manage existing books
5. Delete books when needed

## 🎓 Project Information

This project demonstrates:

- Procedural PHP programming
- MySQL database design
- Authentication systems
- Role-based access control
- File upload handling
- Clean UI/UX design

## 📞 Support

For any issues or questions, please ensure:
1. XAMPP Apache and MySQL are running
2. Database is properly imported
3. File permissions are correct for uploads folder

---

© 2024 Digital Library Management System
