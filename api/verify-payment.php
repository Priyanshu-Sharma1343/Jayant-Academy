<?php
/**
 * Verify & Process Razorpay Payment
 * API Endpoint: /api/verify-payment.php
 */

header('Content-Type: application/json');
require_once '../config.php';
session_start();

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, [], 'Only POST requests allowed');
}

// Get payment data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        sendResponse(false, [], "Missing: $field");
    }
}

$razorpay_order_id = htmlspecialchars($input['razorpay_order_id']);
$razorpay_payment_id = htmlspecialchars($input['razorpay_payment_id']);
$razorpay_signature = htmlspecialchars($input['razorpay_signature']);

// Verify Signature
$hmac = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, RAZORPAY_KEY_SECRET);

if ($hmac !== $razorpay_signature) {
    sendResponse(false, [], 'Payment verification failed - Invalid signature');
}

// Signature valid - Update database
$conn = getDBConnection();

try {
    // Update payment status
    $stmt = $conn->prepare("UPDATE payments SET razorpay_payment_id = ?, razorpay_signature = ?, status = 'captured' WHERE razorpay_order_id = ?");
    $stmt->bind_param("sss", $razorpay_payment_id, $razorpay_signature, $razorpay_order_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Database update failed');
    }
    
    // Get payment details
    $stmt = $conn->prepare("SELECT * FROM payments WHERE razorpay_order_id = ?");
    $stmt->bind_param("s", $razorpay_order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();
    
    $stmt->close();
    
    if (!$payment) {
        throw new Exception('Payment record not found');
    }
    
    // Send confirmation email
    $to = $payment['student_email'];
    $subject = 'Jayant Academy - Payment Successful';
    $message = "
    <h2>Payment Confirmation</h2>
    <p>Dear {$payment['student_name']},</p>
    <p>Your admission payment has been successfully received!</p>
    <table border='1' cellpadding='10'>
        <tr><td>Amount</td><td>₹{$payment['amount']}</td></tr>
        <tr><td>Class</td><td>{$payment['class_name']}</td></tr>
        <tr><td>Payment ID</td><td>{$razorpay_payment_id}</td></tr>
        <tr><td>Date</td><td>" . date('Y-m-d H:i:s') . "</td></tr>
    </table>
    <p>Your admission will be processed within 24 hours. Thank you!</p>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . ADMIN_EMAIL . "\r\n";
    
    mail($to, $subject, $message, $headers);
    
    // Success response
    sendResponse(true, array(
        'payment_id' => $razorpay_payment_id,
        'status' => 'captured',
        'message' => 'Payment successful'
    ), 'Payment processed successfully');
    
} catch (Exception $e) {
    sendResponse(false, [], 'Error: ' . $e->getMessage());
} finally {
    $conn->close();
}

?>
