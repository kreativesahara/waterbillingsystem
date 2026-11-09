<?php
session_start();
require_once('config.php');

$house_number = $_POST['house_number'] ?? '';
$billing_month = $_POST['billing_month'] ?? '';
$date_of_reading = $_POST['date_of_reading'] ?? '';
$current_reading = isset($_POST['current_reading']) ? floatval($_POST['current_reading']) : 0;

if ($house_number === '' || $billing_month === '' || $date_of_reading === '') {
    echo 'Error: Please fill in all required fields';
    exit;
}

// Fetch previous reading & balance from the most recent prior bill for this house if available
$prevStmt = $db->prepare("SELECT current_reading, total_bill FROM monthly_bill WHERE house_number = ? AND billing_month != ? ORDER BY bill_id DESC LIMIT 1");
$prevStmt->execute([$house_number, $billing_month]);
$prevRow = $prevStmt->fetch(PDO::FETCH_ASSOC);

$previous_reading = $prevRow ? floatval($prevRow['current_reading']) : 100;
$meter_rent = 50;
$units_consumed = $current_reading - $previous_reading;
$cost_per_unit = 150;
$current_bill = ($units_consumed * $cost_per_unit) + $meter_rent;
$bbd = 1000;
$total_bill = $current_bill + $bbd;

// Check if a bill for this house_number and billing_month already exists
$checkStmt = $db->prepare("SELECT bill_id FROM monthly_bill WHERE house_number = ? AND billing_month = ? LIMIT 1");
$checkStmt->execute([$house_number, $billing_month]);
$existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    $updateSql = "UPDATE monthly_bill SET date_of_reading = ?, current_reading = ?, previous_reading = ?, meter_rent = ?, units_consumed = ?, current_bill = ?, bbd = ?, total_bill = ?, timestamp = CURRENT_TIMESTAMP WHERE bill_id = ?";
    $stmtUpdate = $db->prepare($updateSql);
    $result = $stmtUpdate->execute([$date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill, $existing['bill_id']]);

    $updateMaster = "UPDATE master_bill SET date_of_reading = ?, current_reading = ?, previous_reading = ?, meter_rent = ?, units_consumed = ?, current_bill = ?, bbd = ?, total_bill = ? WHERE house_number = ? AND billing_month = ?";
    $stmtUpdate2 = $db->prepare($updateMaster);
    $stmtUpdate2->execute([$date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill, $house_number, $billing_month]);

    $_SESSION['last_populated_house'] = $house_number;
    $_SESSION['last_populated_bill_id'] = $existing['bill_id'];
    echo 'Bill Processed Successfully';
    exit;
}

$sql = "INSERT INTO monthly_bill(house_number,billing_month,date_of_reading,current_reading,previous_reading,meter_rent,units_consumed,current_bill,bbd,total_bill) VALUES(?,?,?,?,?,?,?,?,?,?)";
$sql2 = "INSERT INTO master_bill(house_number,billing_month,date_of_reading,current_reading,previous_reading,meter_rent,units_consumed,current_bill,bbd,total_bill) VALUES(?,?,?,?,?,?,?,?,?,?)";
$stmtinsert = $db->prepare($sql);
$stmtinsert2 = $db->prepare($sql2);
$result = $stmtinsert->execute([$house_number, $billing_month, $date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill]);
$result2 = $stmtinsert2->execute([$house_number, $billing_month, $date_of_reading, $current_reading, $previous_reading, $meter_rent, $units_consumed, $current_bill, $bbd, $total_bill]);

if ($result) {
    $_SESSION['last_populated_house'] = $house_number;
    $_SESSION['last_populated_bill_id'] = $db->lastInsertId();
    echo 'Bill Processed Successfully';
} else {
    echo 'There was errors while connecting to the database ';
}
?>
