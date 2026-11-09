<?php
/**
 * Application Configuration
 * Classic Heights Apartments - Water Billing & Management System
 */

return [
    'app' => [
        'name' => 'Classic Heights Water System',
        'short_name' => 'ClassicWater',
        'env' => 'development',
        'debug' => true,
        'base_url' => '', // auto-detected if empty
        'currency' => 'KSh',
        'timezone' => 'Africa/Nairobi',
    ],

    'billing' => [
        'default_rate_per_unit' => 150.00, // KSh per unit (m3)
        'default_meter_rent' => 50.00,     // Monthly fixed meter rent
        'grace_period_days' => 10,         // Days before bill is marked overdue
        'property_name' => 'Classic Heights Apartments',
        'total_units' => 18,
    ],

    'database' => [
        // 'sqlite' allows self-contained zero-setup operation; change to 'mysql' when running on XAMPP/MariaDB
        'driver' => 'sqlite',
        
        'sqlite' => [
            'database' => __DIR__ . '/../database/waterbillingsystem.sqlite',
        ],

        'mysql' => [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'waterbillingsystem',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
    ],

    'mpesa' => [
        'enabled' => true,
        'shortcode' => '174379',
        'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
        'env' => 'sandbox', // sandbox | live
    ],

    'sms' => [
        'enabled' => true,
        'provider' => 'africastalking', // mock or africastalking
        'sender_id' => 'CLASSICHTS',
    ]
];
