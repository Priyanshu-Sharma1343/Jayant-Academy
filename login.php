<?php
/**
 * Unified Login Portal
 * File: login.php
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: /admin/dashboard.php');
    } else {
        header('Location: /staff/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_code = trim($_POST['login_code'] ?? '');
    
    if (empty($login_code)) {
        $error = '❌ Please enter login code';
    } else {
        // Connect to database
        $conn = new mysqli('localhost', 'root', '', 'jayant_academy');
        
        if ($conn->connect_error) {
            $error = '❌ Database connection failed';
        } else {
            // Check if code exists in portal_users table
            $stmt = $conn->prepare('SELECT id, username, role FROM portal_users WHERE login_code = ? AND is_active = 1');
            $stmt->bind_param('s', $login_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Update last login
                $update_stmt = $conn->prepare('UPDATE portal_users SET last_login = NOW() WHERE id = ?');
                $update_stmt->bind_param('i', $user['id']);
                $update_stmt->execute();
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: /admin/dashboard.php');
                } else {
                    header('Location: /staff/dashboard.php');
                }
                exit;
            } else {
                $error = '❌ Invalid login code!';
            }
            
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login - Jayant Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #1e3c72;
            font-size: 2em;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #666;
            font-size: 0.9em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #1e3c72;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }
        
        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .error-message i {
            font-size: 1.2em;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .info-box {
            background: #f0f8ff;
            border-left: 5px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.85em;
            color: #666;
        }
        
        .info-box p {
            margin-bottom: 8px;
        }
        
        .info-box strong {
            color: #1e3c72;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-lock" style="font-size: 2.5em; color: #667eea; margin-bottom: 10px;"></i>
            <h1>Admin Portal</h1>
            <p>Jayant Academy</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="login_code">
                    <i class="fas fa-key"></i> Login Code
                </label>
                <input type="text" id="login_code" name="login_code" placeholder="Enter your login code" required autofocus>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="info-box">
            <p><strong>ℹ️ Login Information:</strong></p>
            <p>✅ <strong>Admin:</strong> Has access to all student data, payments, and can manage staff accounts</p>
            <p>✅ <strong>Staff:</strong> Can view student information and payment details only</p>
        </div>
        
        <div class="back-link">
            <a href="/">← Back to Website</a>
        </div>
    </div>
</body>
</html>
