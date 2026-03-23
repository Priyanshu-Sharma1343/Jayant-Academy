<?php
/**
 * Create Razorpay Order
 * API Endpoint: /api/create-order.php
 */

header('Content-Type: application/json');
require_once 'config.php';
session_start();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, [], 'Only POST requests allowed');
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['student_name', 'student_email', 'student_phone', 'class_name'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        sendResponse(false, [], "Missing field: $field");
    }
}

$student_name = htmlspecialchars($input['student_name']);
$student_email = filter_var($input['student_email'], FILTER_SANITIZE_EMAIL);
$student_phone = htmlspecialchars($input['student_phone']);
$class_name = htmlspecialchars($input['class_name']);

// Validate email
if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, [], 'Invalid email address');
}

// Get fee amount
global $FEE_STRUCTURE;
if (!isset($FEE_STRUCTURE[$class_name])) {
    sendResponse(false, [], 'Invalid class selected');
}

$amountInPaise = $FEE_STRUCTURE[$class_name];
$amountInRupees = $amountInPaise / 100;

// Include Razorpay API Library
require_once 'vendor/autoload.php'; // Assumes Razorpay SDK installed via Composer

// Razorpay API Setup
$api = new \Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

try {
    // Create Razorpay Order
    $orderData = array(
        'receipt' => 'admission_' . time(),
        'amount' => $amountInPaise, // in paise
        'currency' => 'INR',
        'customer_notify' => 1,
        'notes' => array(
            'student_name' => $student_name,
            'student_email' => $student_email,
            'student_phone' => $student_phone,
            'class_name' => $class_name
        )
    );
    
    $order = $api->order->create($orderData);
    
    // Save order to database
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO payments (razorpay_order_id, student_name, student_email, student_phone, class_name, amount, currency, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'created')");
    $stmt->bind_param("sssssss", $order['id'], $student_name, $student_email, $student_phone, $class_name, $amountInRupees, 'INR');
    
    if (!$stmt->execute()) {
        throw new Exception('Database error: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    // Return order details
    sendResponse(true, array(
        'order_id' => $order['id'],
        'amount' => $amountInRupees,
        'currency' => 'INR',
        'student_name' => $student_name,
        'student_email' => $student_email,
        'key_id' => RAZORPAY_KEY_ID
    ), 'Order created successfully');
    
} catch (Exception $e) {
    sendResponse(false, [], 'Error: ' . $e->getMessage());
}

?>
