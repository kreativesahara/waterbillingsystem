<?php
require_once('config.php');
session_start();

$rawPhone = isset($_POST['phonenumber']) ? trim($_POST['phonenumber']) : '';
$newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';

// Support matching phone number in local (07...), stripped (7...), or international (2547...) formats
$digits = preg_replace('/\D+/', '', $rawPhone);
$localPhone = $rawPhone;
$strippedPhone = $digits;
if (strpos($digits, '254') === 0 && strlen($digits) >= 12) {
    $strippedPhone = substr($digits, 3);
    $localPhone = '0' . $strippedPhone;
} elseif (strpos($digits, '0') === 0 && strlen($digits) >= 10) {
    $strippedPhone = substr($digits, 1);
    $localPhone = $digits;
}

$sql = "SELECT C.*, M.bill_id, M.billing_month, M.date_of_reading, M.current_reading, M.previous_reading,
               M.units_consumed, M.cost_per_unit, M.current_bill, M.meter_rent, M.bbd, M.total_bill
        FROM consumers C
        LEFT JOIN monthly_bill M ON C.house_number = M.house_number
        WHERE (C.phonenumber = ? OR C.phonenumber = ? OR C.phonenumber = ?)
          AND C.newpassword = ?
        ORDER BY M.bill_id DESC
        LIMIT 1";

$stmtselect = $db->prepare($sql);
$result = $stmtselect->execute([$rawPhone, $localPhone, $strippedPhone, $newpassword]);

if ($result) {
    $login_user = $stmtselect->fetch(PDO::FETCH_ASSOC);
    if ($login_user) {
        $_SESSION['waterbillingsystem'] = $login_user;
        $_SESSION['phonenumber'] = $login_user['phonenumber'];
        $_SESSION['house_number'] = $login_user['house_number'];
        $_SESSION['prompt_payment_on_login'] = true;
        echo 'Log in Successfully';
    } else {
        echo 'There no user for that combination';
    }
} else {
    echo 'There was errors while connecting to the database ';
}
?>