<?php
/**
 * Local Configuration Template (EXAMPLE)
 * 
 * Instructions:
 * 1. Copy this file to "config.local.php" (which is ignored by git).
 * 2. Set your actual production or custom database & API credentials below.
 * 3. Never commit "config.local.php" to any public or version-controlled repository.
 */

// Custom Database Credentials Override
$db_host = 'localhost';
$db_port = 3306;
$db_user = 'root';
$db_pass = '';              // Enter your MySQL password here
$db_name = 'waterbillingsystem';

// Optional: M-Pesa Daraja API Credentials (if integrated)
define('MPESA_ENV', 'sandbox'); // 'sandbox' or 'live'
define('MPESA_CONSUMER_KEY', 'your_consumer_key_here');
define('MPESA_CONSUMER_SECRET', 'your_consumer_secret_here');
define('MPESA_SHORTCODE', '174379');
define('MPESA_PASSKEY', 'your_passkey_here');

// Optional: SMS Gateway Credentials (e.g. Africa's Talking)
define('SMS_USERNAME', 'sandbox');
define('SMS_API_KEY', 'your_sms_api_key_here');
