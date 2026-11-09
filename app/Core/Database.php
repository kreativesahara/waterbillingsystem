<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    public static function init(array $config): void
    {
        self::$config = $config;
    }

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../config.php';
        }

        $dbConfig = self::$config['database'];
        $driver = $dbConfig['driver'] ?? 'sqlite';

        try {
            if ($driver === 'sqlite') {
                $dbPath = $dbConfig['sqlite']['database'];
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec("PRAGMA foreign_keys = ON;");

                if ($isNew) {
                    self::runMigrationsAndSeed(self::$instance, 'sqlite');
                }
            } else {
                $my = $dbConfig['mysql'];
                $dsn = "mysql:host={$my['host']};port={$my['port']};dbname={$my['database']};charset={$my['charset']}";
                self::$instance = new PDO($dsn, $my['username'], $my['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }

            return self::$instance;
        } catch (PDOException $e) {
            // If MySQL fails, gracefully fallback to SQLite if in development
            if ($driver === 'mysql') {
                error_log("MySQL connection failed: " . $e->getMessage() . ". Falling back to SQLite.");
                $dbPath = $dbConfig['sqlite']['database'];
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec("PRAGMA foreign_keys = ON;");
                if ($isNew) {
                    self::runMigrationsAndSeed(self::$instance, 'sqlite');
                }
                return self::$instance;
            }

            throw new \RuntimeException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Automatically sets up tables and realistic seed data
     */
    public static function runMigrationsAndSeed(PDO $pdo, string $driver = 'sqlite'): void
    {
        $autoInc = $driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';

        $schema = "
        CREATE TABLE IF NOT EXISTS properties (
            id $autoInc,
            name VARCHAR(100) NOT NULL,
            location VARCHAR(150),
            paybill_number VARCHAR(30) DEFAULT '174379',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS houses (
            id $autoInc,
            property_id INT DEFAULT 1,
            house_number VARCHAR(20) NOT NULL UNIQUE,
            meter_number VARCHAR(50) NOT NULL UNIQUE,
            status VARCHAR(20) DEFAULT 'OCCUPIED',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS consumers (
            id $autoInc,
            house_id INT NULL,
            firstname VARCHAR(50) NOT NULL,
            lastname VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phonenumber VARCHAR(30) NOT NULL UNIQUE,
            national_id VARCHAR(30),
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(20) DEFAULT 'TENANT',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS meter_readings (
            id $autoInc,
            house_id INT NOT NULL,
            billing_month VARCHAR(10) NOT NULL, -- e.g. 2026-10
            date_of_reading DATE NOT NULL,
            previous_reading DECIMAL(10,2) NOT NULL DEFAULT 0,
            current_reading DECIMAL(10,2) NOT NULL,
            units_consumed DECIMAL(10,2) NOT NULL,
            recorded_by INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (house_id) REFERENCES houses(id)
        );

        CREATE TABLE IF NOT EXISTS invoices (
            id $autoInc,
            invoice_number VARCHAR(30) NOT NULL UNIQUE,
            house_id INT NOT NULL,
            consumer_id INT NOT NULL,
            billing_month VARCHAR(10) NOT NULL,
            date_of_reading DATE NOT NULL,
            previous_reading DECIMAL(10,2) NOT NULL,
            current_reading DECIMAL(10,2) NOT NULL,
            units_consumed DECIMAL(10,2) NOT NULL,
            cost_per_unit DECIMAL(10,2) NOT NULL DEFAULT 150.00,
            water_cost DECIMAL(10,2) NOT NULL,
            meter_rent DECIMAL(10,2) NOT NULL DEFAULT 50.00,
            bbd DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- Balance brought down / arrears
            total_bill DECIMAL(10,2) NOT NULL,
            amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            status VARCHAR(20) DEFAULT 'PENDING', -- PENDING, PAID, PARTIAL, OVERDUE
            due_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (house_id) REFERENCES houses(id),
            FOREIGN KEY (consumer_id) REFERENCES consumers(id)
        );

        CREATE TABLE IF NOT EXISTS payments (
            id $autoInc,
            invoice_id INT NOT NULL,
            consumer_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(30) DEFAULT 'MPESA_STK',
            mpesa_receipt VARCHAR(50) NULL,
            phone_number VARCHAR(30) NOT NULL,
            status VARCHAR(20) DEFAULT 'COMPLETED',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (invoice_id) REFERENCES invoices(id),
            FOREIGN KEY (consumer_id) REFERENCES consumers(id)
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id $autoInc,
            consumer_id INT NOT NULL,
            phone_number VARCHAR(30) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(30) DEFAULT 'BILL_DISPATCH',
            status VARCHAR(20) DEFAULT 'SENT',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (consumer_id) REFERENCES consumers(id)
        );
        ";

        $pdo->exec($schema);

        // Seed default Property
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM properties");
        if ((int)$stmt->fetchColumn() === 0) {
            $pdo->exec("INSERT INTO properties (name, location, paybill_number) 
                VALUES ('Classic Heights Apartments', 'Kilimani, Nairobi', '174379')");
        }

        // Seed 18 Houses
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM houses");
        if ((int)$stmt->fetchColumn() === 0) {
            for ($i = 1; $i <= 18; $i++) {
                $meter = sprintf("MTR-CH-%04d", 1000 + $i);
                $status = ($i === 17) ? 'VACANT' : 'OCCUPIED';
                $pdo->exec("INSERT INTO houses (property_id, house_number, meter_number, status) 
                    VALUES (1, '$i', '$meter', '$status')");
            }
        }

        // Seed Default Admin and Tenants with modern secure password hashing (password = 'admin123' / 'tenant123')
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM consumers");
        if ((int)$stmt->fetchColumn() === 0) {
            $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
            $tenantPass = password_hash('tenant123', PASSWORD_BCRYPT);

            // Admin account
            $pdo->exec("INSERT INTO consumers (house_id, firstname, lastname, email, phonenumber, national_id, password_hash, role) 
                VALUES (NULL, 'System', 'Admin', 'admin@classicheights.co.ke', '0700111222', '12345678', '$adminPass', 'ADMIN')");

            // Sample Tenants
            $sampleTenants = [
                ['house' => 1, 'first' => 'John', 'last' => 'Doe', 'phone' => '0712345678', 'email' => 'john.doe@example.com', 'id' => '28934102'],
                ['house' => 2, 'first' => 'Sarah', 'last' => 'Wanjiku', 'phone' => '0722334455', 'email' => 'sarah.w@example.com', 'id' => '31456789'],
                ['house' => 3, 'first' => 'David', 'last' => 'Otieno', 'phone' => '0733445566', 'email' => 'david.o@example.com', 'id' => '27891234'],
                ['house' => 4, 'first' => 'Grace', 'last' => 'Muthoni', 'phone' => '0744556677', 'email' => 'grace.m@example.com', 'id' => '32567812'],
                ['house' => 5, 'first' => 'Kevin', 'last' => 'Kipchoge', 'phone' => '0755667788', 'email' => 'kevin.k@example.com', 'id' => '29871245'],
                ['house' => 6, 'first' => 'Mary', 'last' => 'Achieng', 'phone' => '0766778899', 'email' => 'mary.a@example.com', 'id' => '33451290'],
            ];

            foreach ($sampleTenants as $t) {
                $pdo->exec("INSERT INTO consumers (house_id, firstname, lastname, email, phonenumber, national_id, password_hash, role) 
                    VALUES ({$t['house']}, '{$t['first']}', '{$t['last']}', '{$t['email']}', '{$t['phone']}', '{$t['id']}', '$tenantPass', 'TENANT')");
            }
        }

        // Seed Sample Historical Readings & Invoices for House 1 so the tenant and admin dashboards have rich data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices");
        if ((int)$stmt->fetchColumn() === 0) {
            $history = [
                ['month' => '2026-08', 'date' => '2026-08-28', 'prev' => 100, 'curr' => 114, 'status' => 'PAID', 'bbd' => 0, 'paid' => 2150],
                ['month' => '2026-09', 'date' => '2026-09-28', 'prev' => 114, 'curr' => 129, 'status' => 'PAID', 'bbd' => 0, 'paid' => 2300],
                ['month' => '2026-10', 'date' => '2026-10-28', 'prev' => 129, 'curr' => 147, 'status' => 'PENDING', 'bbd' => 250, 'paid' => 0],
            ];

            $rate = 150.00;
            $rent = 50.00;

            foreach ($history as $idx => $h) {
                $units = $h['curr'] - $h['prev'];
                $waterCost = $units * $rate;
                $total = $waterCost + $rent + $h['bbd'];
                $invNum = sprintf("INV-%s-H01-%02d", str_replace('-', '', $h['month']), $idx + 1);
                $dueDate = date('Y-m-d', strtotime($h['date'] . ' + 10 days'));

                $pdo->exec("INSERT INTO meter_readings (house_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, recorded_by)
                    VALUES (1, '{$h['month']}', '{$h['date']}', {$h['prev']}, {$h['curr']}, $units, 1)");

                $pdo->exec("INSERT INTO invoices (invoice_number, house_id, consumer_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, cost_per_unit, water_cost, meter_rent, bbd, total_bill, amount_paid, status, due_date)
                    VALUES ('$invNum', 1, 2, '{$h['month']}', '{$h['date']}', {$h['prev']}, {$h['curr']}, $units, $rate, $waterCost, $rent, {$h['bbd']}, $total, {$h['paid']}, '{$h['status']}', '$dueDate')");

                if ($h['status'] === 'PAID') {
                    $receipt = 'QRT' . strtoupper(substr(md5($invNum), 0, 7));
                    $pdo->exec("INSERT INTO payments (invoice_id, consumer_id, amount, payment_method, mpesa_receipt, phone_number, status)
                        VALUES (" . ($idx + 1) . ", 2, {$h['paid']}, 'MPESA_STK', '$receipt', '0712345678', 'COMPLETED')");
                }
            }

            // Seed recent readings for House 2 and 3 as well
            $pdo->exec("INSERT INTO meter_readings (house_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, recorded_by)
                VALUES (2, '2026-10', '2026-10-28', 85, 98, 13, 1)");
            $pdo->exec("INSERT INTO invoices (invoice_number, house_id, consumer_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, cost_per_unit, water_cost, meter_rent, bbd, total_bill, amount_paid, status, due_date)
                VALUES ('INV-202610-H02', 2, 3, '2026-10', '2026-10-28', 85, 98, 13, 150.00, 1950.00, 50.00, 0.00, 2000.00, 2000.00, 'PAID', '2026-11-07')");
            $h2Receipt = 'QRT' . strtoupper(substr(md5('INV-202610-H02'), 0, 7));
            $pdo->exec("INSERT INTO payments (invoice_id, consumer_id, amount, payment_method, mpesa_receipt, phone_number, status)
                VALUES (4, 3, 2000.00, 'MPESA_STK', '$h2Receipt', '0722334455', 'COMPLETED')");

            $pdo->exec("INSERT INTO meter_readings (house_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, recorded_by)
                VALUES (3, '2026-10', '2026-10-28', 140, 158, 18, 1)");
            $pdo->exec("INSERT INTO invoices (invoice_number, house_id, consumer_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, cost_per_unit, water_cost, meter_rent, bbd, total_bill, amount_paid, status, due_date)
                VALUES ('INV-202610-H03', 3, 4, '2026-10', '2026-10-28', 140, 158, 18, 150.00, 2700.00, 50.00, 500.00, 3250.00, 0.00, 'PENDING', '2026-11-07')");
        }
    }
}
