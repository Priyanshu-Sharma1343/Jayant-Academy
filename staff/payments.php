<?php
/**
 * Staff - View Payments
 * File: staff/payments.php
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
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Filter
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$where_clause = '';

if ($status !== 'all') {
    $status = $conn->real_escape_string($status);
    $where_clause = "WHERE status = '$status'";
}

// Get total
$total = $conn->query("SELECT COUNT(*) as count FROM payments $where_clause")->fetch_assoc()['count'];
$total_pages = ceil($total / $per_page);

// Get payments
$query = "SELECT * FROM payments $where_clause ORDER BY created_at DESC LIMIT $offset, $per_page";
$payments = $conn->query($query);

// Get status distribution
$status_query = "SELECT status, COUNT(*) as count, SUM(amount) as total FROM payments GROUP BY status";
$status_stats = $conn->query($status_query);
$stats = [];
while ($row = $status_stats->fetch_assoc()) {
    $stats[$row['status']] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Staff Portal</title>
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
        
        .back-btn {
            background: #667eea;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
        }
        
        .stat-box.captured {
            border-left-color: #4caf50;
        }
        
        .stat-box.created {
            border-left-color: #ff9800;
        }
        
        .stat-box.failed {
            border-left-color: #f44336;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1e3c72;
        }
        
        .stat-total {
            font-size: 14px;
            color: #667eea;
            margin-top: 5px;
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
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
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-captured {
            background: #c8e6c9;
            color: #2e7d32;
        }
        
        .status-created {
            background: #ffe0b2;
            color: #e65100;
        }
        
        .status-failed {
            background: #ffcdd2;
            color: #c62828;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            text-decoration: none;
            color: #1e3c72;
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
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-credit-card"></i> All Payments</h1>
        <a href="dashboard.php" class="back-btn">← Back</a>
    </div>
    
    <div class="container">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <?php
            $status_labels = [
                'captured' => '✓ Captured',
                'created' => '⏳ Created',
                'failed' => '✗ Failed'
            ];
            
            foreach ($status_labels as $key => $label):
                $count = isset($stats[$key]) ? $stats[$key]['count'] : 0;
                $total = isset($stats[$key]) ? $stats[$key]['total'] : 0;
            ?>
                <div class="stat-box <?php echo $key; ?>">
                    <div class="stat-label"><?php echo $label; ?></div>
                    <div class="stat-value"><?php echo $count; ?></div>
                    <div class="stat-total">₹<?php echo number_format($total, 2); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Filter Buttons -->
        <div class="section">
            <div class="filters">
                <a href="?status=all" class="filter-btn <?php echo $status === 'all' ? 'active' : ''; ?>">All</a>
                <a href="?status=captured" class="filter-btn <?php echo $status === 'captured' ? 'active' : ''; ?>">✓ Captured</a>
                <a href="?status=created" class="filter-btn <?php echo $status === 'created' ? 'active' : ''; ?>">⏳ Created</a>
                <a href="?status=failed" class="filter-btn <?php echo $status === 'failed' ? 'active' : ''; ?>">✗ Failed</a>
            </div>
            
            <!-- Payments Table -->
            <?php if ($total > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date/Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($payment['student_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($payment['email']); ?></td>
                                <td><?php echo htmlspecialchars($payment['class']); ?></td>
                                <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($payment['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $payment['status']; ?>">
                                        <?php echo ucfirst($payment['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($page > 1) {
                            echo '<a href="?page=1&status=' . urlencode($status) . '">First</a>';
                            echo '<a href="?page=' . ($page - 1) . '&status=' . urlencode($status) . '">←</a>';
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            if ($i == $page) {
                                echo '<span class="active">' . $i . '</span>';
                            } else {
                                echo '<a href="?page=' . $i . '&status=' . urlencode($status) . '">' . $i . '</a>';
                            }
                        }
                        
                        if ($page < $total_pages) {
                            echo '<a href="?page=' . ($page + 1) . '&status=' . urlencode($status) . '">→</a>';
                            echo '<a href="?page=' . $total_pages . '&status=' . urlencode($status) . '">Last</a>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <p style="text-align: center; margin-top: 15px; color: #999; font-size: 14px;">
                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total); ?> of <?php echo $total; ?> payments
                </p>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">No payments found</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
