/**
 * Razorpay Payment Integration
 * File: js/payment.js
 */

// Razorpay Configuration
const RAZORPAY_KEY_ID = 'rzp_test_YOUR_KEY_ID_HERE'; // Replace with actual key
const API_URL = '/api';

/**
 * Initiate Payment Process
 */
async function initiatePayment() {
    // Get form data
    const studentName = document.getElementById('student_name').value;
    const studentEmail = document.getElementById('student_email').value;
    const studentPhone = document.getElementById('student_phone').value;
    const className = document.getElementById('class_name').value;
    
    // Validate inputs
    if (!studentName || !studentEmail || !studentPhone || !className) {
        alert('Please fill all required fields');
        return;
    }
    
    // Show loading
    const btn = document.querySelector('[onclick="initiatePayment()"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    try {
        // Step 1: Create order
        const orderResponse = await fetch(API_URL + '/create-order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                student_name: studentName,
                student_email: studentEmail,
                student_phone: studentPhone,
                class_name: className
            })
        });
        
        const orderData = await orderResponse.json();
        
        if (!orderData.success) {
            alert('Error creating order: ' + orderData.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now via Razorpay';
            return;
        }
        
        // Step 2: Open Razorpay Checkout
        openRazorpayCheckout(orderData.data);
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now via Razorpay';
    }
}

/**
 * Open Razorpay Checkout Modal
 */
function openRazorpayCheckout(data) {
    const options = {
        key: data.key_id,
        amount: data.amount * 100, // Convert to paise
        currency: data.currency,
        name: 'Jayant Academy',
        description: 'Admission Fee - Class ' + data.class_name,
        order_id: data.order_id,
        prefill: {
            name: data.student_name,
            email: data.student_email,
            contact: data.student_phone
        },
        theme: {
            color: '#2a5298'
        },
        handler: handlePaymentSuccess,
        modal: {
            ondismiss: function() {
                alert('Payment cancelled');
                const btn = document.querySelector('[onclick="initiatePayment()"]');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now via Razorpay';
            }
        }
    };
    
    const razorpay = new Razorpay(options);
    razorpay.open();
}

/**
 * Handle Payment Success
 */
async function handlePaymentSuccess(response) {
    try {
        // Verify payment on backend
        const verifyResponse = await fetch(API_URL + '/verify-payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature
            })
        });
        
        const verifyData = await verifyResponse.json();
        
        if (verifyData.success) {
            // Payment successful - redirect
            window.location.href = '/payment-success.php';
        } else {
            alert('Payment verification failed: ' + verifyData.message);
            window.location.href = '/payment-failed.php';
        }
        
    } catch (error) {
        console.error('Error verifying payment:', error);
        alert('An error occurred while verifying payment');
        window.location.href = '/payment-failed.php';
    }
}

/**
 * Get Fee Amount
 */
async function getClassFee() {
    const className = document.getElementById('class_name').value;
    const feeDisplay = document.getElementById('fee_amount');
    
    const feeStructure = {
        'Nursery': '₹45,000', 'LKG': '₹45,000', 'UKG': '₹45,000',
        'I': '₹65,000', 'II': '₹65,000', 'III': '₹65,000', 'IV': '₹65,000', 'V': '₹65,000',
        'VI': '₹80,000', 'VII': '₹80,000', 'VIII': '₹80,000',
        'IX': '₹95,000', 'X': '₹95,000'
    };
    
    if (className && feeStructure[className]) {
        feeDisplay.innerHTML = '<strong>Fee: ' + feeStructure[className] + '</strong>';
    }
}

// Load Razorpay script
(function() {
    const script = document.createElement('script');
    script.src = 'https://checkout.razorpay.com/v1/checkout.js';
    script.async = true;
    document.body.appendChild(script);
})();
