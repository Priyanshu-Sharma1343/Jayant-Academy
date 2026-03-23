<?php
/**
 * Admin - Payment History
 * File: admin/payments.php
 */

require_once 'check-auth.php';
requirePermission('view_payments');

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
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$filter_query = '';

if (!empty($status_filter) && in_array($status_filter, ['created', 'authorized', 'captured', 'failed', 'refunded'])) {
    $filter_query = "WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}

// Get total count
$total_count = $conn->query("SELECT COUNT(*) as count FROM payments $filter_query")->fetch_assoc()['count'];
$total_pages = ceil($total_count / $per_page);

// Get payments
$query = "SELECT * FROM payments $filter_query ORDER BY created_at DESC LIMIT $offset, $per_page";
$payments = $conn->query($query);

// Get status stats
$status_stats = [];
$stats_result = $conn->query('SELECT status, COUNT(*) as count, SUM(amount) as total FROM payments GROUP BY status');
while ($row = $stats_result->fetch_assoc()) {
    $status_stats[$row['status']] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Admin Portal</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
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
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #1e3c72;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        
        .stat-box h4 {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        
        .stat-box .value {
            font-size: 1.8em;
            color: #1e3c72;
            font-weight: 700;
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
        
        .status-badge.captured {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.created {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge.failed {
            background: #f8d7da;
            color: #721c24;
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
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-credit-card"></i> Payment History</h1>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <?php foreach ($status_stats as $status => $stat): ?>
                <div class="stat-box">
                    <h4><?php echo ucfirst($status); ?></h4>
                    <div class="value"><?php echo $stat['count']; ?></div>
                    <p style="font-size: 0.85em; color: #999; margin-top: 5px;">
                        ₹<?php echo number_format($stat['total'] ? $stat['total'] / 100000 : 0, 1); ?>L
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <a href="payments.php" class="filter-btn <?php if (empty($status_filter)) echo 'active'; ?>">All</a>
            <a href="?status=captured" class="filter-btn <?php if ($status_filter === 'captured') echo 'active'; ?>">✓ Captured</a>
            <a href="?status=created" class="filter-btn <?php if ($status_filter === 'created') echo 'active'; ?>">⏳ Created</a>
            <a href="?status=failed" class="filter-btn <?php if ($status_filter === 'failed') echo 'active'; ?>">✗ Failed</a>
        </div>
        
        <!-- Payments Table -->
        <div class="section">
            <?php if ($total_count > 0): ?>
                <p style="margin-bottom: 15px; color: #666;">Showing <?php echo min(($page - 1) * $per_page + 1, $total_count); ?> to <?php echo min($page * $per_page, $total_count); ?> of <?php echo $total_count; ?> payments</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($payment['student_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($payment['student_email']); ?></td>
                                <td><?php echo htmlspecialchars($payment['class_name']); ?></td>
                                <td><strong>₹<?php echo number_format($payment['amount'], 0); ?></strong></td>
                                <td><?php echo ucfirst($payment['payment_method'] ?? 'UPI'); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($payment['created_at'])); ?></td>
                                <td><span class="status-badge <?php echo $payment['status']; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php if (!empty($status_filter)) echo '&status='.$status_filter; ?>">« First</a>
                            <a href="?page=<?php echo $page - 1; ?><?php if (!empty($status_filter)) echo '&status='.$status_filter; ?>">< Prev</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php if (!empty($status_filter)) echo '&status='.$status_filter; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php if (!empty($status_filter)) echo '&status='.$status_filter; ?>">Next ></a>
                            <a href="?page=<?php echo $total_pages; ?><?php if (!empty($status_filter)) echo '&status='.$status_filter; ?>">Last »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 2em; display: block; margin-bottom: 10px;"></i>
                    No payments found
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
