<?php
require 'vendor/autoload.php'; // Load Composer autoload

use Razorpay\Api\Api;

// Replace with your Razorpay Key ID and Secret
$keyId = 'rzp_live_wvRZB4UltMVpRe';
$keySecret = 'ukaojorDv5HAiWCS6nfJpRRf';

$api = new Api($keyId, $keySecret);

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$amount = $_POST['amount'] * 100; // Convert to paise (Razorpay uses smallest currency unit)

// Create an order
$orderData = [
    'receipt'         => 'rcptid_' . rand(1000, 9999),
    'amount'          => $amount, // Amount in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // Auto-capture payment
];

$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];

// Store order ID in session
session_start();
$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "Course Mantra",
    "description"       => "Just Pay For Course",
    "image"             => "https://beast7.in/logo.png", // Optional
    "prefill"           => [
        "name"          => $name,
        "email"         => $email,
        "contact"       => $mobile,
    ],
    "notes"             => [
        "address"       => "Customer Address",
    ],
    "theme"             => [
        "color"         => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

$json = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment</title>
</head>
<body>
    <button id="rzp-button1" style="display:none;">Pay with Razorpay</button>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <form name="razorpayform" action="success.php" method="POST">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>
    <script>
        var options = <?php echo $json; ?>;
        options.handler = function (response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.razorpayform.submit();
        };
        var rzp = new Razorpay(options);
        window.onload = function() {
            rzp.open();
        };
    </script>
</body>
</html>