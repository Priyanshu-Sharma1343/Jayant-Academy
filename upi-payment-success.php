<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed - Jayant Academy</title>
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
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
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
        
        .success-icon {
            font-size: 4em;
            color: #27ae60;
            margin-bottom: 20px;
            animation: scaleIn 0.6s ease;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        h1 {
            color: #1e3c72;
            margin-bottom: 10px;
            font-size: 2em;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.05em;
        }
        
        .details-box {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
            border-left: 5px solid #667eea;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #1e3c72;
        }
        
        .detail-value {
            color: #666;
        }
        
        .status-banner {
            background: #d4edda;
            border: 2px solid #27ae60;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .next-steps {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: left;
        }
        
        .next-steps h3 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .next-steps ol {
            margin-left: 20px;
            color: #856404;
        }
        
        .next-steps li {
            margin-bottom: 8px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #2a5298;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #1e3c72;
            transform: translateY(-2px);
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 0.95em;
            color: #666;
        }
        
        .contact-item {
            margin-bottom: 10px;
        }
        
        .contact-item i {
            color: #667eea;
            margin-right: 8px;
            width: 20px;
        }
        
        .reference-id {
            background: #f5f5f5;
            padding: 8px 12px;
            border-radius: 5px;
            font-family: monospace;
            color: #1e3c72;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>✅ Payment Confirmed!</h1>
        <p class="subtitle">Thank you for your submission</p>
        
        <div class="status-banner">
            📌 Your application is being processed
        </div>
        
        <div class="details-box">
            <?php
            $name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Student';
            $class = isset($_GET['class']) ? htmlspecialchars($_GET['class']) : 'N/A';
            $amount = isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : '0';
            
            // Generate reference ID
            $reference_id = 'JAY' . date('YmdHis') . rand(1000, 9999);
            ?>
            
            <div class="detail-row">
                <span class="detail-label">Student Name:</span>
                <span class="detail-value"><strong><?php echo $name; ?></strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Class Applied:</span>
                <span class="detail-value"><strong><?php echo $class; ?></strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span class="detail-value"><strong style="color: #27ae60;">₹<?php echo number_format($amount, 0); ?></strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Time:</span>
                <span class="detail-value"><strong><?php echo date('d M Y, h:i A'); ?></strong></span>
            </div>
            
            <div class="reference-id">
                Reference ID: <?php echo $reference_id; ?>
            </div>
        </div>
        
        <div class="next-steps">
            <h3><i class="fas fa-info-circle"></i> Next Steps:</h3>
            <ol>
                <li>You will receive a confirmation email within 5 minutes</li>
                <li>Our team will verify your documents (24-48 hours)</li>
                <li>Admission confirmation will be sent via WhatsApp</li>
                <li>Class assignments will be communicated separately</li>
            </ol>
        </div>
        
        <div class="button-group">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="https://wa.me/917541841303?text=Received%20payment%20confirmation%20for%20admission%20reference%20<?php echo $reference_id; ?>" class="btn btn-secondary" target="_blank">
                <i class="fab fa-whatsapp"></i> Contact Support
            </a>
        </div>
        
        <div class="contact-info">
            <p style="font-weight: 600; margin-bottom: 15px;">For any queries, reach us at:</p>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <strong>06255-220297</strong>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <strong>info@jayantacademy.com</strong>
            </div>
            <div class="contact-item">
                <i class="fab fa-whatsapp"></i>
                <strong>WhatsApp: 7541841303</strong>
            </div>
        </div>
    </div>
</body>
</html>
