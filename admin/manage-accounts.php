<?php
/**
 * Admin - Manage Accounts & Login Codes
 * File: admin/manage-accounts.php
 */

require_once 'check-auth.php';
requirePermission('manage_accounts');

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'jayant_academy');

if ($conn->connect_error) {
    die('Database connection failed');
}

$message = '';

// Handle code change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'change_code') {
        $user_id = (int)$_POST['user_id'];
        $new_code = trim($_POST['new_code'] ?? '');
        
        if (empty($new_code) || strlen($new_code) < 4) {
            $message = '❌ Code must be at least 4 characters long';
        } else {
            // Check if code already exists
            $check = $conn->query("SELECT id FROM portal_users WHERE login_code = '$new_code' AND id != $user_id");
            if ($check->num_rows > 0) {
                $message = '❌ This code is already in use';
            } else {
                $update = $conn->query("UPDATE portal_users SET login_code = '$new_code' WHERE id = $user_id");
                if ($update) {
                    $message = '✅ Login code updated successfully';
                }
            }
        }
    } elseif ($action === 'toggle_status') {
        $user_id = (int)$_POST['user_id'];
        $new_status = $_POST['status'] === '1' ? 0 : 1;
        
        $update = $conn->query("UPDATE portal_users SET is_active = $new_status WHERE id = $user_id");
        if ($update) {
            $message = '✅ Account status updated';
        }
    }
}

// Get all users except admin
$users = $conn->query('SELECT * FROM portal_users WHERE role IN ("admin", "staff") ORDER BY role, created_at DESC');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts - Admin Portal</title>
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
            background: #f5f7fa;
        }
        
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-btn {
            padding: 10px 20px;
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }
        
        .back-btn:hover {
            background: #1e3c72;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th {
            background: #f0f0f0;
            color: #1e3c72;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        table tr:hover {
            background: #f9f9f9;
        }
        
        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            background: #e8f4f8;
            color: #2a5298;
        }
        
        .role-badge.staff {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .code-input-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .code-input {
            flex: 1;
            min-width: 150px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.9em;
        }
        
        .btn-small {
            padding: 8px 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }
        
        .btn-small:hover {
            background: #764ba2;
        }
        
        .btn-toggle {
            padding: 8px 15px;
            background: #ffc107;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }
        
        .btn-toggle:hover {
            background: #e0a800;
        }
        
        .last-login {
            font-size: 0.85em;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-users-cog"></i> Manage Staff Accounts</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, '✅') === 0) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h2 style="color: #1e3c72; margin-bottom: 20px; font-size: 1.3em; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                <i class="fas fa-key"></i> Account Administration
            </h2>
            
            <?php if ($users->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Login Code</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td>
                                    <span class="role-badge <?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                <td>
                                    <code style="background: #f5f5f5; padding: 5px 8px; border-radius: 4px; font-family: monospace;">
                                        <?php echo htmlspecialchars($user['login_code']); ?>
                                    </code>
                                </td>
                                <td class="last-login">
                                    <?php 
                                    if ($user['last_login']) {
                                        echo date('d M Y H:i', strtotime($user['last_login']));
                                    } else {
                                        echo 'Never';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <form method="POST" style="display: inline; flex: 1;">
                                            <input type="hidden" name="action" value="change_code">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <div class="code-input-group">
                                                <input type="text" name="new_code" class="code-input" placeholder="New code" required>
                                                <button type="submit" class="btn-small" title="Change Login Code">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $user['is_active']; ?>">
                                            <button type="submit" class="btn-toggle" title="Toggle Account Status">
                                                <i class="fas fa-<?php echo $user['is_active'] ? 'lock' : 'unlock'; ?>"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 2em; display: block; margin-bottom: 10px;"></i>
                    No accounts found
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Information Box -->
        <div style="background: #f0f8ff; border-left: 5px solid #667eea; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <h3 style="color: #1e3c72; margin-bottom: 10px;">
                <i class="fas fa-info-circle"></i> How to Change Login Codes
            </h3>
            <ol style="margin-left: 20px; color: #666; line-height: 1.8;">
                <li>Enter a new login code in the input field next to each user</li>
                <li>Code must be at least 4 characters long</li>
                <li>Click the edit button to save the new code</li>
                <li>Staff members must use the new code to login next time</li>
                <li>Use the lock button to activate/deactivate accounts</li>
            </ol>
        </div>
    </div>
</body>
</html>
