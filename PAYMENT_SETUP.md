# Jayant Academy - Payment Gateway Setup Guide

## 🚀 Quick Start Setup

### Prerequisites
- PHP 7.4+
- MySQL/MariaDB
- Razorpay Account (Free)
- Composer (Optional, for SDK)

---

## 📋 Step 1: Database Setup

### Create MySQL Database
```bash
# Method 1: Using phpMyAdmin
- Open phpMyAdmin: http://localhost/phpmyadmin
- Create new database: "jayant_academy"
- Import database.sql file to create tables

# Method 2: Using Command Line
mysql -u root -p < database.sql
```

---

## 🔑 Step 2: Razorpay Configuration

### Get Your Razorpay Keys
1. Visit: https://dashboard.razorpay.com/
2. Sign up for free account
3. Go to Settings → API Keys
4. Copy: **Key ID** and **Key Secret** (Test Mode)

### Update config.php
```php
// In: config.php (Lines 9-10)

define('RAZORPAY_KEY_ID', 'rzp_test_XXXXX'); // Paste your Key ID
define('RAZORPAY_KEY_SECRET', 'XXXXXXX');    // Paste your Key Secret
```

---

## 💻 Step 3: Install Razorpay PHP SDK

### Using Composer
```bash
cd d:\priyanshu\Jayant Academy\jayant-school
composer require razorpay/razorpay
```

### Manual Installation (No Composer)
1. Download Razorpay SDK: https://github.com/razorpay/razorpay-php
2. Extract to: `vendor/razorpay/razorpay/`

---

## 🔧 Step 4: Update HTML Integration

### In index.html - Admission Form Section

Replace the current admission form with:

```html
<!-- Add this inside admission form after class field -->
<div id="fee_amount" style="background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 15px 0; color: #1e3c72; font-weight: 600;">
    Select a class to see fee
</div>

<!-- Replace submit button with -->
<button type="button" onclick="initiatePayment()" style="background: linear-gradient(135deg, #25d366, #20ba58); border: none; color: white; padding: 15px 40px; border-radius: 8px; font-size: 1em; font-weight: 600; cursor: pointer; margin-top: 20px;">
    <i class="fab fa-cc-razorpay"></i> Pay Now via Razorpay
</button>
```

### Add JavaScript Include
Add this before closing `</body>` tag in index.html:

```html
<!-- Payment Gateway -->
<script src="js/payment.js"></script>
```

### Update Form ID Attributes
Make sure admission form has these IDs:
```html
<input type="text" id="student_name" name="student" ... required>
<input type="text" id="parent_name" name="parent" ... required>
<input type="email" id="student_email" name="email" ... required>
<input type="tel" id="student_phone" name="phone" ... required>
<select id="class_name" name="class" onchange="getClassFee()" required>
    <option value="">Select Class</option>
    <option value="Nursery">Nursery (₹45,000)</option>
    <option value="LKG">LKG (₹45,000)</option>
    <option value="UKG">UKG (₹45,000)</option>
    <option value="I">Class I (₹65,000)</option>
    <!-- ... other classes ... -->
</select>
```

---

## 📧 Step 5: Email Configuration

### Update config.php for Email Notifications
```php
define('SMTP_USER', 'your-gmail@gmail.com');
define('SMTP_PASS', 'your-app-password'); // Use Gmail App Password

// Generate Gmail App Password:
// 1. Go to myaccount.google.com/apppasswords
// 2. Create app-specific password
// 3. Use that password above
```

---

## 📁 File Structure
```
jayant-school/
├── index.html                    (Main website)
├── config.php                    (Configuration)
├── database.sql                  (Database schema)
├── payment-success.php           (Success page)
├── payment-failed.php            (Failure page)
├── js/
│   └── payment.js               (Payment handler)
├── api/
│   ├── create-order.php         (Create Razorpay order)
│   └── verify-payment.php       (Verify payment)
└── vendor/
    └── razorpay/razorpay/       (Razorpay SDK)
```

---

## 🧪 Testing

### Test Cards (Razorpay Sandbox)
```
Success:  4111 1111 1111 1111
Failure:  4222 2222 2222 2222
CVV: Any 3 digits
Expiry: Any future date
```

### Test Payment Flow
1. Fill admission form
2. Select class
3. Click "Pay Now via Razorpay"
4. Use test card above
5. Complete payment

---

## 📊 Database Tables

### payments table
- Stores all payment transactions
- Tracks Razorpay order & payment IDs
- Records payment status

### admissions table
- Stores student admission data
- Links to payments

### enquiries table
- Stores enquiry form submissions

### portal_users table
- Admin/Staff/Student/Parent login credentials

---

## 🔒 Security Notes

**Never share:**
- Razorpay Key Secret
- Database passwords
- Admin credentials

**Always use:**
- HTTPS in production
- Strong database passwords
- Input validation (already included)
- CSRF tokens (already included)

---

## 🛠️ Troubleshooting

### Database Connection Error
```
Error: Database connection failed
→ Check DB credentials in config.php
→ Ensure MySQL is running
```

### Razorpay SDK Not Found
```
Error: Class 'Razorpay\Api\Api' not found
→ Install SDK: composer require razorpay/razorpay
→ Or download manually
```

### Payment Not Capturing
```
→ Check Razorpay logs
→ Verify API keys
→ Ensure database is writable
```

### Email Not Sending
```
→ Check SMTP credentials
→ Verify Gmail App Password
→ Enable "Less secure apps" if using old Gmail
```

---

## 📞 Support

**Jayant Academy Contact:**
- Phone: 06255-220297
- Email: rxl.jayantacademy@gmail.com
- WhatsApp: https://wa.me/917541841303

**Razorpay Support:**
- Website: https://razorpay.com/support
- Docs: https://razorpay.com/docs

---

## ✅ Checklist

- [ ] Create MySQL database
- [ ] Import database.sql
- [ ] Get Razorpay API keys
- [ ] Update config.php with keys
- [ ] Install Razorpay SDK
- [ ] Update index.html with payment button
- [ ] Add payment.js to HTML
- [ ] Configure email settings
- [ ] Test with test cards
- [ ] Go live (switch to Live Keys)

---

**Last Updated:** March 2026
**Version:** 1.0
