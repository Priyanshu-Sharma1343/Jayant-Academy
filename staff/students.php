<?php
/**
 * Staff - View Students
 * File: staff/students.php
 */

require_once '../admin/check-auth.php';

// Allow only staff access
if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Access Denied');
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'jayant_academy');

if ($conn->connect_error) {
    die('Database connection failed');
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = '';

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $search_query = "WHERE student_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
}

// Get total
$total_count = $conn->query("SELECT COUNT(*) as count FROM admissions $search_query")->fetch_assoc()['count'];
$total_pages = ceil($total_count / $per_page);

// Get students
$query = "SELECT * FROM admissions $search_query ORDER BY created_at DESC LIMIT $offset, $per_page";
$students = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Staff Portal</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-box {
            flex: 1;
            display: flex;
            gap: 10px;
        }
        
        .search-box input, .back-btn {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }
        
        .search-box button, .back-btn {
            background: #667eea;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        
        .back-btn {
            background: #2a5298;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-graduation-cap"></i> All Students</h1>
        <a href="dashboard.php" class="back-btn">← Back</a>
    </div>
    
    <div class="container">
        <div class="controls">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
        
        <div class="section">
            <?php if ($total_count > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Parent Name</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($student['student_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($student['class_applying']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td><?php echo htmlspecialchars($student['parent_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($student['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">No students found</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
