<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/Core/Response.php';
require_once __DIR__ . '/../app/Controllers/TenantController.php';

$pdo = \App\Core\Database::getConnection();

// Ensure House 2 PAID invoice has a payment record in existing sqlite db
$h2Count = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE consumer_id = 3")->fetchColumn();
if ($h2Count === 0) {
    $rcpt = 'QRT' . strtoupper(substr(md5('INV-202610-H02'), 0, 7));
    $pdo->exec("INSERT INTO payments (invoice_id, consumer_id, amount, payment_method, mpesa_receipt, phone_number, status) VALUES (4, 3, 2000.00, 'MPESA_STK', '$rcpt', '0722334455', 'COMPLETED')");
}

$tables = ['properties', 'houses', 'consumers', 'meter_readings', 'invoices', 'payments'];
foreach ($tables as $t) {
    $count = $pdo->query("SELECT count(*) FROM {$t}")->fetchColumn();
    echo "{$t}: {$count} rows\n";
}

echo "\n--- Sample Consumers ---\n";
$consumers = $pdo->query("SELECT id, firstname, lastname, phonenumber, role FROM consumers")->fetchAll(PDO::FETCH_ASSOC);
foreach ($consumers as $c) {
    echo "#{$c['id']} [{$c['role']}] {$c['firstname']} {$c['lastname']} - {$c['phonenumber']}\n";
}

echo "\n--- Sample Invoices ---\n";
$invoices = $pdo->query("SELECT invoice_number, house_id, billing_month, units_consumed, total_bill, status FROM invoices")->fetchAll(PDO::FETCH_ASSOC);
foreach ($invoices as $inv) {
    echo "{$inv['invoice_number']} | House {$inv['house_id']} | {$inv['billing_month']} | Units: {$inv['units_consumed']} | KSh {$inv['total_bill']} | {$inv['status']}\n";
}

echo "\n--- Verify Personalized Tenant Payments & Activity Log Page ---\n";
\App\Core\Auth::startSession();
$_SESSION['user_id'] = 2;
$_SESSION['role'] = 'TENANT';
$_SESSION['firstname'] = 'John';
$_SESSION['lastname'] = 'Doe';
$_SESSION['phonenumber'] = '0712345678';
$_SESSION['email'] = 'john.doe@example.com';
$_SESSION['house_id'] = 1;

ob_start();
$ctrl = new \App\Controllers\TenantController();
$ctrl->payments();
$html = ob_get_clean();

echo "Rendered HTML length: " . strlen($html) . " bytes\n";
echo "Contains personalized title: " . (str_contains($html, "John Doe's Payment &amp; Activity Log") ? "PASS" : "FAIL") . "\n";
echo "Contains M-Pesa Ledger: " . (str_contains($html, "M-Pesa Payment Transactions Ledger") ? "PASS" : "FAIL") . "\n";
echo "Contains Activity Log: " . (str_contains($html, "Chronological Account Activity Log") ? "PASS" : "FAIL") . "\n";

