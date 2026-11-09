-- ===================================================================
-- Classic Heights Apartments - Water Billing & Management System
-- Database Schema (MySQL 8.0+ / MariaDB 10.4+)
-- ===================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS meter_readings;
DROP TABLE IF EXISTS consumers;
DROP TABLE IF EXISTS houses;
DROP TABLE IF EXISTS properties;

CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(150),
    paybill_number VARCHAR(30) DEFAULT '174379',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE houses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT DEFAULT 1,
    house_number VARCHAR(20) NOT NULL UNIQUE,
    meter_number VARCHAR(50) NOT NULL UNIQUE,
    status VARCHAR(20) DEFAULT 'OCCUPIED',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE consumers (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE meter_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    house_id INT NOT NULL,
    billing_month VARCHAR(10) NOT NULL,
    date_of_reading DATE NOT NULL,
    previous_reading DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    current_reading DECIMAL(10,2) NOT NULL,
    units_consumed DECIMAL(10,2) NOT NULL,
    recorded_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
    bbd DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_bill DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'PENDING',
    due_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (consumer_id) REFERENCES consumers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    consumer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) DEFAULT 'MPESA_STK',
    mpesa_receipt VARCHAR(50) NULL,
    phone_number VARCHAR(30) NOT NULL,
    status VARCHAR(20) DEFAULT 'COMPLETED',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (consumer_id) REFERENCES consumers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consumer_id INT NOT NULL,
    phone_number VARCHAR(30) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(30) DEFAULT 'BILL_DISPATCH',
    status VARCHAR(20) DEFAULT 'SENT',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (consumer_id) REFERENCES consumers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
