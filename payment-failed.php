<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Jayant Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 50px; border-radius: 15px; text-align: center; max-width: 600px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .error-icon { color: #ff6b6b; font-size: 80px; margin-bottom: 30px; animation: shake 0.5s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 75% { transform: translateX(10px); } }
        h1 { color: #1e3c72; margin-bottom: 20px; font-size: 2.5em; }
        p { color: #666; margin-bottom: 15px; font-size: 1.1em; }
        .details { background: #fff5f5; padding: 20px; border-radius: 10px; margin: 30px 0; text-align: left; border-left: 4px solid #ff6b6b; }
        .details p { margin: 10px 0; }
        .btn { display: inline-block; padding: 15px 40px; color: white; text-decoration: none; border-radius: 50px; font-weight: 600; margin: 10px 5px; transition: all 0.3s ease; }
        .btn-retry { background: #ff6b6b; } .btn-retry:hover { background: #ff5252; transform: translateY(-3px); }
        .btn-home { background: #2a5298; } .btn-home:hover { background: #1e3c72; transform: translateY(-3px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1>Payment Failed ✗</h1>
        <p>Unfortunately, your payment could not be processed.</p>
        
        <div class="details">
            <p><strong>🔍 Possible Reasons:</strong></p>
            <ul style="text-align: left; padding-left: 20px;">
                <li>Insufficient balance in account</li>
                <li>Invalid card/UPI details</li>
                <li>Transaction timeout</li>
                <li>Bank declined the transaction</li>
            </ul>
            <p style="color: #ff6b6b; font-weight: 600; margin-top: 20px;">Please try again or contact our support team</p>
            <p><strong>📞 Support:</strong> 06255-220297 | <strong>📧 Email:</strong> rxl.jayantacademy@gmail.com</p>
        </div>
        
        <a href="index.html#admission" class="btn btn-retry"><i class="fas fa-redo"></i> Try Again</a>
        <a href="index.html" class="btn btn-home"><i class="fas fa-home"></i> Home</a>
    </div>
</body>
</html>
