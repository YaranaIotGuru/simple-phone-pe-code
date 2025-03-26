<?php
require 'vendor/autoload.php';
use Razorpay\Api\Api;

session_start();

// Replace with your Razorpay Key ID and Secret
$keyId = 'rzp_live_wvRZB4UltMVpRe';
$keySecret = 'ukaojorDv5HAiWCS6nfJpRRf';

$api = new Api($keyId, $keySecret);

// Get payment details from POST
$paymentId = $_POST['razorpay_payment_id'];
$orderId = $_POST['razorpay_order_id'];
$signature = $_POST['razorpay_signature'];

// Verify the signature
$attributes = $orderId . '|' . $paymentId;
$expectedSignature = hash_hmac('sha256', $attributes, $keySecret);

if ($expectedSignature === $signature) {
    // Signature verified, payment successful
    $payment = $api->payment->fetch($paymentId);
    echo "<h2>Payment Successful!</h2>";
    echo "Payment ID: " . $paymentId . "<br>";
    echo "Order ID: " . $orderId . "<br>";
    echo "Amount: ₹" . ($payment->amount / 100) . "<br>";
    // Here you can save the payment details to your database
} else {
    echo "<h2>Payment Verification Failed!</h2>";
}
?>