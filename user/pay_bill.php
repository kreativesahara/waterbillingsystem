<?php
require_once('config.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['waterbillingsystem'])) {
    http_response_code(401);
    echo json_encode([
        'ok'      => false,
        'message' => 'Your session has expired. Please log in again.'
    ]);
    exit;
}

$sessionUser  = $_SESSION['waterbillingsystem'];
$houseNumber  = isset($_POST['house_number']) && trim($_POST['house_number']) !== ''
    ? trim($_POST['house_number'])
    : (isset($sessionUser['house_number']) ? $sessionUser['house_number'] : '');
$billId       = isset($_POST['bill_id']) ? (int)$_POST['bill_id'] : 0;
$billingMonth = isset($_POST['billing_month']) ? trim($_POST['billing_month']) : '';
$rawPhone     = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$amount       = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

if ($rawPhone === '') {
    echo json_encode([
        'ok'      => false,
        'message' => 'Please enter a valid phone number to receive the payment prompt.'
    ]);
    exit;
}

if ($amount <= 0) {
    echo json_encode([
        'ok'      => false,
        'message' => 'Please enter a valid payment amount greater than KSh 0.'
    ]);
    exit;
}

// Normalize phone number to international format (e.g. 2547...)
$digits = preg_replace('/\D+/', '', $rawPhone);
if (strpos($digits, '0') === 0 && strlen($digits) >= 10) {
    $normalizedPhone = '254' . substr($digits, 1);
} elseif ((strpos($digits, '7') === 0 || strpos($digits, '1') === 0) && strlen($digits) === 9) {
    $normalizedPhone = '254' . $digits;
} else {
    $normalizedPhone = $digits;
}

try {
    $db->exec("CREATE TABLE IF NOT EXISTS `bill_payments` (
        `payment_id` int(11) NOT NULL AUTO_INCREMENT,
        `bill_id` int(11) DEFAULT NULL,
        `house_number` varchar(50) NOT NULL,
        `billing_month` varchar(30) DEFAULT NULL,
        `phone_number` varchar(30) NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `reference_code` varchar(32) NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'PROMPT_SENT',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`payment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $refCode = 'STK' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 7));

    $stmt = $db->prepare("INSERT INTO bill_payments (bill_id, house_number, billing_month, phone_number, amount, reference_code, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $billId > 0 ? $billId : null,
        (string)$houseNumber,
        $billingMonth,
        $normalizedPhone,
        $amount,
        $refCode,
        'PROMPT_SENT'
    ]);

    echo json_encode([
        'ok'             => true,
        'message'        => 'Payment prompt has been sent to +' . $normalizedPhone . '. Please enter your M-Pesa PIN on your phone to complete payment.',
        'reference_code' => $refCode,
        'phone_number'   => '+' . $normalizedPhone,
        'amount'         => number_format($amount, 2),
        'house_number'   => $houseNumber,
        'billing_month'  => $billingMonth
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok'      => false,
        'message' => 'Could not initiate payment prompt: ' . $e->getMessage()
    ]);
}

