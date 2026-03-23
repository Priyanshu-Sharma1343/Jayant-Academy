/**
 * UPI QR Code Payment Integration
 * File: js/upi-payment.js
 */

// UPI Configuration
const UPI_ID = '7541841303@nyes';
const MERCHANT_NAME = 'Jayant Academy';
const MERCHANT_PHONE = '06255-220297';

// Fee Structure (in Rupees)
const FEE_STRUCTURE = {
    'Nursery': 45000,
    'LKG': 45000,
    'UKG': 45000,
    'I': 65000,
    'II': 65000,
    'III': 65000,
    'IV': 65000,
    'V': 65000,
    'VI': 80000,
    'VII': 80000,
    'VIII': 80000,
    'IX': 95000,
    'X': 95000
};

// Debug logging
console.log('✅ UPI Payment Script Loaded');

/**
 * Initiate UPI Payment
 */
function initiateUPIPayment() {
    console.log('🔵 initiateUPIPayment called');
    
    // Get form data
    const studentName = document.getElementById('student_name');
    const studentEmail = document.getElementById('student_email');
    const studentPhone = document.getElementById('student_phone');
    const className = document.getElementById('class_name');
    
    console.log('studentName element:', studentName);
    console.log('className element:', className);
    
    // Get values
    const name = studentName ? studentName.value : '';
    const email = studentEmail ? studentEmail.value : '';
    const phone = studentPhone ? studentPhone.value : '';
    const cls = className ? className.value : '';
    
    console.log('Form values:', { name, email, phone, cls });
    
    // Validate inputs
    if (!name || !email || !phone || !cls) {
        alert('❌ Please fill all required fields');
        return;
    }
    
    // Get selected payment amount
    const selectedAmount = parseInt(sessionStorage.getItem('selectedPaymentAmount'));
    
    if (!selectedAmount || selectedAmount <= 0) {
        alert('❌ Please select an amount to pay');
        return;
    }
    
    console.log('Selected payment amount:', selectedAmount);
    
    // Store data in session for confirmation page
    sessionStorage.setItem('studentName', name);
    sessionStorage.setItem('studentEmail', email);
    sessionStorage.setItem('studentPhone', phone);
    sessionStorage.setItem('className', cls);
    sessionStorage.setItem('feeAmount', selectedAmount);
    
    // Show UPI Modal with selected amount
    showUPIModal(name, cls, selectedAmount);
}

/**
 * Show UPI Payment Modal
 */
function showUPIModal(studentName, className, feeAmount) {
    console.log('🔵 showUPIModal called', { studentName, className, feeAmount });
    
    const modal = document.getElementById('upiPaymentModal');
    
    if (!modal) {
        console.error('❌ UPI Modal not found');
        alert('Modal not found! Check HTML.');
        return;
    }
    
    console.log('✅ Modal found');
    
    // Update modal content
    const modalStudentName = document.getElementById('modalStudentName');
    const modalClassName = document.getElementById('modalClassName');
    const modalFeeAmount = document.getElementById('modalFeeAmount');
    
    if (modalStudentName) modalStudentName.textContent = studentName;
    if (modalClassName) modalClassName.textContent = className;
    if (modalFeeAmount) modalFeeAmount.textContent = '₹' + feeAmount.toLocaleString();
    
    // Generate QR code
    generateQRCode(feeAmount);
    
    // Generate UPI link
    const upiLink = generateUPILink(studentName, feeAmount);
    const upiLinkElement = document.getElementById('upiLink');
    if (upiLinkElement) {
        upiLinkElement.href = upiLink;
        console.log('✅ UPI Link set');
    }
    
    const upiCopyText = document.getElementById('upiCopyText');
    if (upiCopyText) {
        upiCopyText.value = upiLink;
    }
    
    // Show modal - using display instead of classList
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    console.log('✅ Modal displayed');
}

/**
 * Generate QR Code
 */
function generateQRCode(amount) {
    console.log('🔵 generateQRCode called with amount:', amount);
    
    const upiString = generateUPIString(amount);
    console.log('UPI String:', upiString);
    
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(upiString)}`;
    
    const qrImage = document.getElementById('qrCodeImage');
    if (qrImage) {
        qrImage.src = qrApiUrl;
        qrImage.alt = 'UPI QR Code';
        console.log('✅ QR Code image set');
    } else {
        console.error('❌ qrCodeImage element not found');
    }
}

/**
 * Generate UPI String
 */
function generateUPIString(amount) {
    return `upi://pay?pa=${UPI_ID}&pn=${encodeURIComponent(MERCHANT_NAME)}&am=${amount}&tr=ADMISSION_${Date.now()}&tn=Admission%20Fee`;
}

/**
 * Generate UPI Link for Direct Open
 */
function generateUPILink(studentName, amount) {
    const transactionRef = `ADMISSION_${Date.now()}_${studentName.replace(/\s/g, '')}`;
    return `upi://pay?pa=${UPI_ID}&pn=${encodeURIComponent(MERCHANT_NAME)}&am=${amount}&tr=${transactionRef}&tn=Admission%20Fee`;
}

/**
 * Copy UPI Link to Clipboard
 */
function copyUPILink() {
    console.log('🔵 copyUPILink called');
    
    const copyText = document.getElementById('upiCopyText');
    if (!copyText) {
        alert('Copy element not found');
        return;
    }
    
    copyText.select();
    document.execCommand('copy');
    
    // Show feedback
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = '✅ Copied!';
    btn.style.background = '#27ae60';
    
    setTimeout(() => {
        btn.textContent = originalText;
        btn.style.background = '';
    }, 2000);
}

/**
 * Open UPI Payment
 */
function openUPIPayment() {
    console.log('🔵 openUPIPayment called');
    
    const upiLinkElement = document.getElementById('upiLink');
    if (!upiLinkElement) {
        alert('UPI Link element not found');
        return;
    }
    
    const upiLink = upiLinkElement.href;
    console.log('UPI Link:', upiLink);
    
    if (!upiLink) {
        alert('No UPI link generated');
        return;
    }
    
    // For mobile devices
    window.location.href = upiLink;
    
    // Show instruction message
    setTimeout(() => {
        showPaymentInstruction();
    }, 500);
}

/**
 * Show Payment Instruction
 */
function showPaymentInstruction() {
    alert('📱 Your payment app will open shortly.\n\n💡 Steps:\n1. Select your payment method\n2. Enter/Confirm amount\n3. Authorize payment\n4. Return to confirm\n\n⚠️ If app doesn\'t open, click "Copy UPI Link" to copy the link manually.');
}

/**
 * Record Manual Payment (for tracking)
 */
function recordManualPayment() {
    console.log('🔵 recordManualPayment called');
    
    const studentEmail = sessionStorage.getItem('studentEmail');
    const className = sessionStorage.getItem('className');
    const feeAmount = sessionStorage.getItem('feeAmount');
    const studentName = sessionStorage.getItem('studentName');
    const studentPhone = sessionStorage.getItem('studentPhone');
    
    // Simple confirmation message for manual tracking
    const message = `Payment Details:
- Student: ${studentName}
- Class: ${className}
- Amount: ₹${feeAmount}
- Email: ${studentEmail}
- Phone: ${studentPhone}
- Time: ${new Date().toLocaleString()}

📌 Note: Please save this for your records.`;
    
    alert(message);
    
    // Close modal and show confirmation
    closeUPIModal();
    showPaymentConfirmation(studentName, className, feeAmount);
}

/**
 * Close UPI Modal
 */
function closeUPIModal() {
    console.log('🔵 closeUPIModal called');
    
    const modal = document.getElementById('upiPaymentModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        console.log('✅ Modal closed');
    } else {
        console.error('❌ Modal not found');
    }
}

/**
 * Show Payment Confirmation
 */
function showPaymentConfirmation(name, className, amount) {
    // Redirect to confirmation page
    setTimeout(() => {
        window.location.href = `upi-payment-success.php?name=${encodeURIComponent(name)}&class=${encodeURIComponent(className)}&amount=${amount}`;
    }, 500);
}

/**
 * Update Fee Display
 */
function getClassFee() {
    console.log('🔵 getClassFee called');
    
    const classSelect = document.getElementById('class_name');
    const feeDisplay = document.getElementById('fee_amount');
    const amountSelectionBox = document.getElementById('amountSelectionBox');
    
    if (!classSelect || !feeDisplay || !amountSelectionBox) {
        console.error('❌ Missing elements');
        return;
    }
    
    if (classSelect.value && FEE_STRUCTURE[classSelect.value]) {
        const fullAmount = FEE_STRUCTURE[classSelect.value];
        
        // Update full fee display
        const fullFeeAmount = document.getElementById('fullFeeAmount');
        if (fullFeeAmount) {
            fullFeeAmount.textContent = '₹' + fullAmount.toLocaleString();
        }
        
        feeDisplay.style.display = 'block';
        
        // Update payment options
        updatePaymentOptions(fullAmount);
        
        // Show amount selection box
        amountSelectionBox.style.display = 'block';
        
        console.log('✅ Fee display updated:', fullAmount);
    } else {
        feeDisplay.style.display = 'none';
        amountSelectionBox.style.display = 'none';
    }
}

/**
 * Update Payment Options based on Full Fee
 */
function updatePaymentOptions(fullAmount) {
    const registration = 10000;
    const halfAmount = Math.round(fullAmount / 2);
    
    // Update button amounts
    const regBtn = document.getElementById('regAmount');
    const halfBtn = document.getElementById('halfAmount');
    const fullBtn = document.getElementById('fullAmount');
    
    if (regBtn) regBtn.textContent = '₹' + registration.toLocaleString();
    if (halfBtn) halfBtn.textContent = '₹' + halfAmount.toLocaleString();
    if (fullBtn) fullBtn.textContent = '₹' + fullAmount.toLocaleString();
    
    // Store values for later
    document.getElementById('class_name').dataset.fullAmount = fullAmount;
    document.getElementById('class_name').dataset.registration = registration;
    document.getElementById('class_name').dataset.halfAmount = halfAmount;
}

/**
 * Select Payment Amount
 */
function selectPaymentAmount(type) {
    console.log('🔵 selectPaymentAmount called with type:', type);
    
    const classSelect = document.getElementById('class_name');
    if (!classSelect.value) {
        alert('Please select a class first');
        return;
    }
    
    const fullAmount = parseInt(classSelect.dataset.fullAmount);
    const registration = parseInt(classSelect.dataset.registration);
    const halfAmount = parseInt(classSelect.dataset.halfAmount);
    
    let selectedAmount = 0;
    
    if (type === 'registration') {
        selectedAmount = registration;
    } else if (type === 'half') {
        selectedAmount = halfAmount;
    } else if (type === 'full') {
        selectedAmount = fullAmount;
    }
    
    if (selectedAmount > 0) {
        displaySelectedAmount(selectedAmount);
    }
}

/**
 * Show Custom Amount Input
 */
function showCustomAmountInput() {
    console.log('🔵 showCustomAmountInput called');
    
    const customAmountDiv = document.getElementById('customAmountDiv');
    if (customAmountDiv) {
        customAmountDiv.style.display = 'block';
        document.getElementById('customAmount').focus();
    }
}

/**
 * Update Custom Amount
 */
function updateCustomAmount() {
    console.log('🔵 updateCustomAmount called');
    
    const customAmount = document.getElementById('customAmount');
    const amount = parseInt(customAmount.value);
    
    if (amount && amount > 0) {
        displaySelectedAmount(amount);
    } else {
        alert('Please enter a valid amount');
        customAmount.value = '';
    }
}

/**
 * Display Selected Amount
 */
function displaySelectedAmount(amount) {
    console.log('✅ Selected Amount:', amount);
    
    const payAmount = document.getElementById('payAmount');
    const selectedDisplay = document.getElementById('selectedAmountDisplay');
    
    if (payAmount) {
        payAmount.textContent = '₹' + amount.toLocaleString();
    }
    
    if (selectedDisplay) {
        selectedDisplay.style.display = 'block';
    }
    
    // Store selected amount
    sessionStorage.setItem('selectedPaymentAmount', amount);
}

/**
 * Close modal when clicking outside
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Content Loaded');
    
    const modal = document.getElementById('upiPaymentModal');
    
    if (modal) {
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeUPIModal();
            }
        });
    }
});
