<?php
/**
 * Staff Dashboard (Limited Access)
 * File: staff/dashboard.php
 */

require_once '../admin/check-auth.php';

// Allow only staff and above
if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('❌ Access Denied!');
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'jayant_academy');

if ($conn->connect_error) {
    die('Database connection failed');
}

// Get statistics
$total_students = $conn->query('SELECT COUNT(*) as count FROM admissions')->fetch_assoc()['count'];
$total_payments = $conn->query('SELECT COUNT(*) as count FROM payments WHERE status = "captured"')->fetch_assoc()['count'];
$pending_payments = $conn->query('SELECT COUNT(*) as count FROM admissions WHERE status = "pending"')->fetch_assoc()['count'];

// Get recent students
$recent_students = $conn->query('SELECT * FROM admissions ORDER BY created_at DESC LIMIT 10');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal - Jayant Academy</title>
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
            background: linear-gradient(135deg, #2a5298, #1e3c72);
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
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #ee5a52;
        }
        
        .container {
            max-width: 1200px;
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
        
        .restriction-banner {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-user-tie"></i> Reception Portal</h1>
        <div class="header-right">
            <div class="user-info">
                <p>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></p>
                <p style="font-size: 0.8em; color: #ccc;">Reception Staff</p>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="container">
        <!-- Restrictions Notice -->
        <div class="restriction-banner">
            <i class="fas fa-info-circle"></i> 
            <strong>Limited Access:</strong> You can view student information and payment details. Faculty data is not accessible.
        </div>
        
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
            
            <div class="stat-card">
                <i class="fas fa-hourglass icon"></i>
                <h3>Pending Admissions</h3>
                <div class="value"><?php echo $pending_payments; ?></div>
            </div>
        </div>
        
        <!-- Navigation Buttons -->
        <div class="nav-buttons">
            <a href="students.php" class="nav-btn">
                <i class="fas fa-list"></i> View Students
            </a>
            <a href="payments.php" class="nav-btn">
                <i class="fas fa-credit-card"></i> View Payments
            </a>
        </div>
        
        <!-- Recent Students Section -->
        <div class="section">
            <h2>👥 Recent Admissions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Parent Name</th>
                        <th>Contact</th>
                        <th>Applied On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $recent_students->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($student['student_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($student['class_applying']); ?></td>
                            <td><?php echo htmlspecialchars($student['parent_name']); ?></td>
                            <td>
                                <a href="tel:<?php echo htmlspecialchars($student['phone']); ?>"><?php echo htmlspecialchars($student['phone']); ?></a>
                            </td>
                            <td><?php echo date('d M Y', strtotime($student['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
