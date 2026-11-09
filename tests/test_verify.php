<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$pdo = \App\Core\Database::getConnection();

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
