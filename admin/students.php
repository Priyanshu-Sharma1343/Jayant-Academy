<?php
/**
 * Admin - All Students
 * File: admin/students.php
 */

require_once 'check-auth.php';
requirePermission('view_students');

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'jayant_academy');

if ($conn->connect_error) {
    die('Database connection failed');
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = '';

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $search_query = "WHERE student_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
}

// Get total count
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
    <title>Students - Admin Portal</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            flex-wrap: wrap;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
            display: flex;
            gap: 10px;
        }
        
        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }
        
        .search-box button, .back-btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-box button:hover, .back-btn:hover {
            background: #764ba2;
        }
        
        .back-btn {
            background: #2a5298;
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
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }
        
        .pagination {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
        }
        
        .pagination a:hover {
            background: #667eea;
            color: white;
        }
        
        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .result-info {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-graduation-cap"></i> All Students</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <div class="controls">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
                <?php if (!empty($search)): ?>
                    <a href="students.php" class="back-btn" style="text-decoration: none;"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="section">
            <div class="result-info">
                Showing <strong><?php echo min(($page - 1) * $per_page + 1, $total_count); ?></strong> to 
                <strong><?php echo min($page * $per_page, $total_count); ?></strong> of 
                <strong><?php echo $total_count; ?></strong> students
                <?php if (!empty($search)): ?> (Search: "<?php echo htmlspecialchars($search); ?>"<?php endif; ?>
            </div>
            
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
                            <th>Status</th>
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
                                <td>
                                    <span class="status-badge <?php echo $student['status'] === 'pending' ? 'pending' : 'approved'; ?>">
                                        <?php echo ucfirst($student['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php if (!empty($search)) echo '&search='.$search; ?>">« First</a>
                            <a href="?page=<?php echo $page - 1; ?><?php if (!empty($search)) echo '&search='.$search; ?>">< Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php if (!empty($search)) echo '&search='.$search; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php if (!empty($search)) echo '&search='.$search; ?>">Next ></a>
                            <a href="?page=<?php echo $total_pages; ?><?php if (!empty($search)) echo '&search='.$search; ?>">Last »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                    No students found
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
