<?php
/**
 * Admin Dashboard
 * File: admin/dashboard.php
 */

require_once 'check-auth.php';
requirePermission('view_all');

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'jayant_academy');

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get statistics
$total_students = $conn->query('SELECT COUNT(*) as count FROM admissions')->fetch_assoc()['count'];
$total_payments = $conn->query('SELECT COUNT(*) as count FROM payments WHERE status = "captured"')->fetch_assoc()['count'];
$total_revenue = $conn->query('SELECT SUM(amount) as total FROM payments WHERE status = "captured"')->fetch_assoc()['total'] ?? 0;
$pending_admissions = $conn->query('SELECT COUNT(*) as count FROM admissions WHERE status = "pending"')->fetch_assoc()['count'];

// Get recent payments
$recent_payments = $conn->query('SELECT * FROM payments WHERE status = "captured" ORDER BY created_at DESC LIMIT 5');

// Get students by class
$class_stats = $conn->query('SELECT class_applying as class, COUNT(*) as count FROM admissions GROUP BY class_applying ORDER BY count DESC');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jayant Academy</title>
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
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 1.8em;
        }
        
        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info p {
            font-size: 0.9em;
        }
        
        .logout-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #ee5a52;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .stat-card .value {
            font-size: 2.5em;
            font-weight: 700;
            color: #1e3c72;
        }
        
        .stat-card.pending {
            border-left-color: #ffc107;
        }
        
        .stat-card.revenue {
            border-left-color: #27ae60;
        }
        
        .stat-card .icon {
            float: right;
            font-size: 2.5em;
            color: #f0f0f0;
        }
        
        .nav-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .nav-btn {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .nav-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .section h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.3em;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            background: #f0f0f0;
            color: #1e3c72;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e0e0e0;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        table tr:hover {
            background: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .badge.success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge.pending {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>
        </div>
        <div class="header-right">
            <div class="user-info">
                <p>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></p>
                <p style="font-size: 0.8em; color: #ccc;"><?php echo ucfirst($role); ?> User</p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users icon"></i>
                <h3>Total Students</h3>
                <div class="value"><?php echo $total_students; ?></div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-check-circle icon"></i>
                <h3>Confirmed Payments</h3>
                <div class="value"><?php echo $total_payments; ?></div>
            </div>
            
            <div class="stat-card revenue">
                <i class="fas fa-rupee-sign icon"></i>
                <h3>Total Revenue</h3>
                <div class="value">₹<?php echo number_format($total_revenue / 100000, 2); ?>L</div>
            </div>
            
            <div class="stat-card pending">
                <i class="fas fa-hourglass icon"></i>
                <h3>Pending Admissions</h3>
                <div class="value"><?php echo $pending_admissions; ?></div>
            </div>
        </div>
        
        <!-- Navigation Buttons -->
        <div class="nav-buttons">
            <a href="students.php" class="nav-btn">
                <i class="fas fa-list"></i> View All Students
            </a>
            <a href="payments.php" class="nav-btn">
                <i class="fas fa-credit-card"></i> Payment History
            </a>
            <a href="manage-accounts.php" class="nav-btn">
                <i class="fas fa-users-cog"></i> Manage Accounts
            </a>
            <a href="reports.php" class="nav-btn">
                <i class="fas fa-file-pdf"></i> Generate Reports
            </a>
        </div>
        
        <!-- Recent Payments Section -->
        <div class="section">
            <h2>📊 Recent Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($payment = $recent_payments->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($payment['student_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($payment['student_email']); ?></td>
                            <td><strong>₹<?php echo number_format($payment['amount'], 0); ?></strong></td>
                            <td><?php echo htmlspecialchars($payment['class_name']); ?></td>
                            <td><?php echo date('d M Y', strtotime($payment['created_at'])); ?></td>
                            <td><span class="badge success">✓ <?php echo ucfirst($payment['status']); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Students by Class -->
        <div class="section">
            <h2>📚 Admissions by Class</h2>
            <table>
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Number of Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($row = $class_stats->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['class']); ?></strong></td>
                            <td>
                                <strong><?php echo $row['count']; ?></strong> 
                                <div style="width: <?php echo min($row['count'] * 10, 200); ?>px; height: 8px; background: #667eea; border-radius: 4px; margin-top: 5px;"></div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
