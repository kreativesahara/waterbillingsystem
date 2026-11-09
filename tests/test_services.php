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
use App\Services\MPesaService;
use App\Services\SmsService;

$billing = new BillingService();
$calc = $billing->calculate(1, 160.0);
echo "=== Test Billing Calculation for House 1 with Dial 160.0 ===\n";
echo "Previous Reading: {$calc['previous_reading']}\n";
echo "Current Reading: {$calc['current_reading']}\n";
echo "Units Consumed: {$calc['units_consumed']}\n";
echo "Water Cost: KSh {$calc['water_cost']}\n";
echo "Meter Rent: KSh {$calc['meter_rent']}\n";
echo "Arrears BBD: KSh {$calc['arrears']}\n";
echo "Total Bill: KSh {$calc['total_bill']}\n";

$mpesa = new MPesaService();
echo "\n=== Test M-Pesa STK Push Settlement ===\n";
$stkRes = $mpesa->initiateStkPush(3, '0712345678', 3000.00);
echo "Result: " . json_encode($stkRes, JSON_PRETTY_PRINT) . "\n";

$sms = new SmsService();
echo "\n=== Test SMS Dispatch ===\n";
$smsRes = $sms->sendBillNotification(3);
echo "Result: " . json_encode($smsRes, JSON_PRETTY_PRINT) . "\n";
