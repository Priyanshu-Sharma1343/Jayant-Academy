<?php
/**
 * Jayant Academy - Configuration File
 * Razorpay Payment Gateway Integration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this to your MySQL password
define('DB_NAME', 'jayant_academy');

// Razorpay Configuration - TEST MODE
// Get your keys from: https://dashboard.razorpay.com/#/app/settings/api-keys
define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_KEY_ID_HERE'); // Replace with actual key
define('RAZORPAY_KEY_SECRET', 'YOUR_KEY_SECRET_HERE'); // Replace with actual secret

// Application URLs
define('APP_URL', 'http://localhost');
define('PAYMENT_SUCCESS_URL', APP_URL . '/payment-success.php');
define('PAYMENT_FAILED_URL', APP_URL . '/payment-failed.php');

// Email Configuration
define('ADMIN_EMAIL', 'rxl.jayantacademy@gmail.com');
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com'); // Your Gmail
define('SMTP_PASS', 'your-app-password'); // Gmail App Password

// Fee Structure (in Paise - 1 Rupee = 100 Paise)
$FEE_STRUCTURE = array(
    'Nursery' => 4500000, // ₹45,000
    'LKG' => 4500000,
    'UKG' => 4500000,
    'I' => 6500000, // ₹65,000
    'II' => 6500000,
    'III' => 6500000,
    'IV' => 6500000,
    'V' => 6500000,
    'VI' => 8000000, // ₹80,000
    'VII' => 8000000,
    'VIII' => 8000000,
    'IX' => 9500000, // ₹95,000
    'X' => 9500000
);

// Enable error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// CORS Headers - Allow requests from your domain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Database Connection Function
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Response Helper
function sendResponse($success, $data = [], $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Security: Verify CSRF Token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Generate CSRF Token
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

?>
