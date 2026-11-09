<?php
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Services\BillingService;
use App\Core\Database;

Database::init(require __DIR__ . '/../app/config.php');

$billing = new BillingService();

echo "=== Test 1: Single Invoice Share Formatting ===\n";
$inv = Database::fetchOne(
    "SELECT i.*, h.house_number, c.firstname, c.lastname, c.phonenumber
     FROM invoices i
     JOIN houses h ON i.house_id = h.id
     JOIN consumers c ON i.consumer_id = c.id
     LIMIT 1"
);
$msg = $billing->formatInvoiceMessage($inv);
echo $msg . "\n\n";

echo "=== Test 2: Building Summary & Broadcast Message ===\n";
$summary = $billing->generateBuildingSummary('2026-10');
echo "Month: {$summary['month']}\n";
echo "Total Invoices: {$summary['total_count']}\n";
echo "Paid Units: {$summary['paid_count']}\n";
echo "Pending Units: {$summary['pending_count']}\n";
echo "Total Billed: KSh {$summary['total_billed']}\n";
echo "Total Unpaid: KSh {$summary['unpaid_balance']}\n\n";

echo "--- Building Broadcast WhatsApp Message ---\n";
echo $summary['building_message'] . "\n\n";

echo "--- Pending Units Reminder Message ---\n";
echo $summary['reminder_message'] . "\n";
