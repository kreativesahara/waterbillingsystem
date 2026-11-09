<?php
session_start();
require_once('config.php');

header('Content-Type: application/json; charset=utf-8');

$house_number = isset($_REQUEST['house_number']) ? trim($_REQUEST['house_number']) : '';
$bill_id = isset($_REQUEST['bill_id']) ? intval($_REQUEST['bill_id']) : 0;
$override_phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';

// If the admin filled out the billing form and clicked "Send Bill" directly, save/update the bill first
$billing_month = isset($_POST['billing_month']) ? trim($_POST['billing_month']) : '';
$date_of_reading = isset($_POST['date_of_reading']) ? trim($_POST['date_of_reading']) : '';
$current_reading = isset($_POST['current_reading']) && $_POST['current_reading'] !== '' ? floatval($_POST['current_reading']) : null;

if ($house_number !== '' && $billing_month !== '' && $date_of_reading !== '' && $current_reading !== null) {
    $prevStmt = $db->prepare("SELECT current_reading FROM monthly_bill WHERE house_number = ? AND billing_month != ? ORDER BY bill_id DESC LIMIT 1");
    $prevStmt->execute([$house_number, $billing_month]);
    $prevRow = $prevStmt->fetch(PDO::FETCH_ASSOC);

    $previous_reading = $prevRow ? floatval($prevRow['current_reading']) : 100;
    $meter_rent = 50;
    $units_consumed = $current_reading - $previous_reading;
    $cost_per_unit = 150;
    $current_bill = ($units_consumed * $cost_per_unit) + $meter_rent;
    $bbd = 1000;
    $total_bill = $current_bill + $bbd;

    $checkStmt = $db->prepare("SELECT bill_id FROM monthly_bill WHERE house_number = ? AND billing_month = ? LIMIT 1");
    $checkStmt->execute([$house_number, $billing_month]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $bill_id = intval($existing['bill_id']);
        $stmtUp = $db->prepare("UPDATE monthly_bill SET date_of_reading = ?, current_reading = ?, previous_reading = ?, meter_rent = ?, units_consumed = ?, current_bill = ?, bbd = ?, total_bill = ?, timestamp = CURRENT_TIMESTAMP WHERE bill_id = ?");
        $stmtUp->execute([$date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill, $bill_id]);
    } else {
        $stmtIns = $db->prepare("INSERT INTO monthly_bill(house_number,billing_month,date_of_reading,current_reading,previous_reading,meter_rent,units_consumed,current_bill,bbd,total_bill) VALUES(?,?,?,?,?,?,?,?,?,?)");
        $stmtIns->execute([$house_number, $billing_month, $date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill]);
        $bill_id = intval($db->lastInsertId());

        $stmtIns2 = $db->prepare("INSERT INTO master_bill(house_number,billing_month,date_of_reading,current_reading,previous_reading,meter_rent,units_consumed,current_bill,bbd,total_bill) VALUES(?,?,?,?,?,?,?,?,?,?)");
        $stmtIns2->execute([$house_number, $billing_month, $date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill]);
    }
}

// Fetch the target bill
if ($bill_id > 0) {
    $billStmt = $db->prepare("SELECT * FROM monthly_bill WHERE bill_id = ? LIMIT 1");
    $billStmt->execute([$bill_id]);
    $bill = $billStmt->fetch(PDO::FETCH_ASSOC);
} elseif ($house_number !== '') {
    $billStmt = $db->prepare("SELECT * FROM monthly_bill WHERE house_number = ? ORDER BY timestamp DESC, bill_id DESC LIMIT 1");
    $billStmt->execute([$house_number]);
    $bill = $billStmt->fetch(PDO::FETCH_ASSOC);
} else {
    $billStmt = $db->query("SELECT * FROM monthly_bill ORDER BY timestamp DESC, bill_id DESC LIMIT 1");
    $bill = $billStmt->fetch(PDO::FETCH_ASSOC);
}

if (!$bill) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No water bill found for the selected house. Please populate the bill first.'
    ]);
    exit;
}

$house_number = $bill['house_number'];

// Fetch tenant associated with this house_number
$tenantStmt = $db->prepare("SELECT * FROM consumers WHERE house_number = ? ORDER BY id DESC LIMIT 1");
$tenantStmt->execute([$house_number]);
$tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);

$tenantName = $tenant ? trim($tenant['firstname'] . ' ' . $tenant['lastname']) : ('House ' . $house_number . ' Tenant');
$rawPhone = $override_phone !== '' ? $override_phone : ($tenant['phonenumber'] ?? '');

/**
 * Format phone number to WhatsApp international standard (e.g. 2547XXXXXXXX)
 */
function formatWhatsAppPhone($phone) {
    $digits = preg_replace('/\D+/', '', $phone);
    if ($digits === '') {
        return '';
    }
    if (strpos($digits, '0') === 0 && strlen($digits) === 10) {
        return '254' . substr($digits, 1);
    }
    if (strlen($digits) === 9 && (strpos($digits, '7') === 0 || strpos($digits, '1') === 0)) {
        return '254' . $digits;
    }
    return $digits;
}

$whatsappPhone = formatWhatsAppPhone($rawPhone);

// Build redirect URL to the Tenant Portal Login with payment prompt parameters
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$redirectLink = $scheme . '://' . $host . '/waterbillingsystem/user/login.php?action=pay&house=' . urlencode($house_number) . '&bill_id=' . urlencode($bill['bill_id']) . ($rawPhone !== '' ? '&phone=' . urlencode($rawPhone) : '');

// Construct WhatsApp message
$totalFormatted = number_format((float)$bill['total_bill'], 2);
$currentBillFormatted = number_format((float)$bill['current_bill'], 2);
$bbdFormatted = number_format((float)$bill['bbd'], 2);
$meterRentFormatted = number_format((float)$bill['meter_rent'], 2);

$message = "*CLASSIC HEIGHTS APARTMENTS - WATER BILL*\n"
    . "Hello *{$tenantName}* (House No. {$house_number}),\n\n"
    . "Your water bill statement for *{$bill['billing_month']}* is ready:\n"
    . "- Reading Date: {$bill['date_of_reading']}\n"
    . "- Previous Reading: {$bill['previous_reading']}\n"
    . "- Current Reading: {$bill['current_reading']}\n"
    . "- Units Consumed: {$bill['units_consumed']} units\n"
    . "- Current Charge: KSh {$currentBillFormatted}\n"
    . "- Meter Rent: KSh {$meterRentFormatted}\n"
    . "- Balance B/D: KSh {$bbdFormatted}\n"
    . "*TOTAL AMOUNT DUE: KSh {$totalFormatted}*\n\n"
    . "Click the link below to log into your Tenant Portal and prompt M-Pesa payment:\n"
    . $redirectLink . "\n\n"
    . "Thank you,\nClassic Heights Management";

$whatsappUrl = $whatsappPhone !== ''
    ? 'https://wa.me/' . $whatsappPhone . '?text=' . rawurlencode($message)
    : 'https://wa.me/?text=' . rawurlencode($message);

echo json_encode([
    'status' => 'success',
    'has_tenant' => (bool)$tenant,
    'tenant_name' => $tenantName,
    'tenant_phone' => $rawPhone,
    'whatsapp_phone' => $whatsappPhone,
    'house_number' => $house_number,
    'bill_id' => $bill['bill_id'],
    'billing_month' => $bill['billing_month'],
    'total_bill' => $totalFormatted,
    'redirect_link' => $redirectLink,
    'message' => $message,
    'whatsapp_url' => $whatsappUrl
]);

