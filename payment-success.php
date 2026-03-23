<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Jayant Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 50px; border-radius: 15px; text-align: center; max-width: 600px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .success-icon { color: #4CAF50; font-size: 80px; margin-bottom: 30px; animation: bounce 1s ease-in-out; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        h1 { color: #1e3c72; margin-bottom: 20px; font-size: 2.5em; }
        p { color: #666; margin-bottom: 15px; font-size: 1.1em; }
        .details { background: #f5f7fa; padding: 20px; border-radius: 10px; margin: 30px 0; text-align: left; border-left: 4px solid #4CAF50; }
        .details p { margin: 10px 0; }
        .btn { display: inline-block; padding: 15px 40px; background: #4CAF50; color: white; text-decoration: none; border-radius: 50px; font-weight: 600; margin-top: 30px; transition: all 0.3s ease; }
        .btn:hover { background: #45a049; transform: translateY(-3px); box-shadow: 0 5px 20px rgba(76, 175, 80, 0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Payment Successful! ✓</h1>
        <p>Your admission payment has been completed successfully.</p>
        
        <div class="details">
            <p><strong>📧 Payment Confirmation Sent To:</strong> Your email address</p>
            <p><strong>⏱️ Next Step:</strong> Your admission will be processed within 24 hours</p>
            <p><strong>📱 Contact Us:</strong> 06255-220297 for any queries</p>
            <p style="color: #4CAF50; font-weight: 600; margin-top: 20px;">Thank you for choosing Jayant Academy!</p>
        </div>
        
        <a href="index.html" class="btn"><i class="fas fa-home"></i> Back to Home</a>
    </div>
</body>
</html>
