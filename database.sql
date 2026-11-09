-- ====================================================================
-- Water Management and Billing System Database Schema
-- Classic Heights Apartments
-- Compatible with MariaDB 10.4+ / MySQL 5.7+ / MySQL 8.0+ in XAMPP
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `waterbillingsystem` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `waterbillingsystem`;

-- --------------------------------------------------------------------
-- 1. Table structure for table `admins`
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT 'System',
  `lastname` varchar(100) DEFAULT 'Admin',
  `phonenumber` varchar(30) NOT NULL,
  `newpassword` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `phonenumber_unique` (`phonenumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Admin Credentials:
-- Phone: 0712345678
-- Password: admin
INSERT INTO `admins` (`id`, `firstname`, `lastname`, `phonenumber`, `newpassword`) VALUES
(1, 'Property', 'Manager', '0712345678', 'admin'),
(2, 'Administrator', 'Classic', '0700000000', '123456');

-- --------------------------------------------------------------------
-- 2. Table structure for table `consumers` (Tenants)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `consumers`;
CREATE TABLE `consumers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `house_number` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phonenumber` varchar(30) NOT NULL,
  `national_id` varchar(50) NOT NULL,
  `newpassword` varchar(255) NOT NULL,
  `confirmpassword` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `consumer_phone` (`phonenumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Tenants:
-- Tenant 1: Phone: 0798765432, Password: password (House 1)
-- Tenant 2: Phone: 0722000001, Password: password (House 2)
INSERT INTO `consumers` (`id`, `house_number`, `firstname`, `lastname`, `email`, `phonenumber`, `national_id`, `newpassword`, `confirmpassword`) VALUES
(1, '1', 'John', 'Kamau', 'john.kamau@gmail.com', '0798765432', '32145678', 'password', 'password'),
(2, '2', 'Sarah', 'Wanjiku', 'sarah.wanjiku@gmail.com', '0722000001', '34567890', 'password', 'password'),
(3, '3', 'David', 'Otieno', 'david.otieno@gmail.com', '0733000002', '35678901', 'password', 'password');

-- --------------------------------------------------------------------
-- 3. Table structure for table `monthly_bill` (Current Billing Cycles)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `monthly_bill`;
CREATE TABLE `monthly_bill` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `house_number` varchar(50) NOT NULL,
  `billing_month` varchar(50) NOT NULL,
  `date_of_reading` date NOT NULL,
  `current_reading` decimal(10,2) NOT NULL DEFAULT 0.00,
  `previous_reading` decimal(10,2) NOT NULL DEFAULT 100.00,
  `meter_rent` decimal(10,2) NOT NULL DEFAULT 50.00,
  `units_consumed` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_bill` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bbd` decimal(10,2) NOT NULL DEFAULT 1000.00,
  `total_bill` decimal(10,2) NOT NULL DEFAULT 0.00,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Monthly Bills for existing tenants
INSERT INTO `monthly_bill` (`bill_id`, `house_number`, `billing_month`, `date_of_reading`, `current_reading`, `previous_reading`, `meter_rent`, `units_consumed`, `current_bill`, `bbd`, `total_bill`) VALUES
(1, '1', '2026-11', '2026-11-01', 150.00, 100.00, 50.00, 50.00, 7550.00, 1000.00, 8550.00),
(2, '2', '2026-11', '2026-11-01', 135.00, 100.00, 50.00, 35.00, 5300.00, 500.00, 5800.00),
(3, '3', '2026-11', '2026-11-01', 120.00, 100.00, 50.00, 20.00, 3050.00, 0.00, 3050.00);

-- --------------------------------------------------------------------
-- 4. Table structure for table `master_bill` (Historical Archive Records)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `master_bill`;
CREATE TABLE `master_bill` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `house_number` varchar(50) NOT NULL,
  `billing_month` varchar(50) NOT NULL,
  `date_of_reading` date NOT NULL,
  `current_reading` decimal(10,2) NOT NULL DEFAULT 0.00,
  `previous_reading` decimal(10,2) NOT NULL DEFAULT 100.00,
  `meter_rent` decimal(10,2) NOT NULL DEFAULT 50.00,
  `units_consumed` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_bill` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bbd` decimal(10,2) NOT NULL DEFAULT 1000.00,
  `total_bill` decimal(10,2) NOT NULL DEFAULT 0.00,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Master Archive Bills
INSERT INTO `master_bill` (`bill_id`, `house_number`, `billing_month`, `date_of_reading`, `current_reading`, `previous_reading`, `meter_rent`, `units_consumed`, `current_bill`, `bbd`, `total_bill`) VALUES
(1, '1', '2026-10', '2026-10-01', 100.00, 60.00, 50.00, 40.00, 6050.00, 1000.00, 7050.00),
(2, '1', '2026-11', '2026-11-01', 150.00, 100.00, 50.00, 50.00, 7550.00, 1000.00, 8550.00),
(3, '2', '2026-11', '2026-11-01', 135.00, 100.00, 50.00, 35.00, 5300.00, 500.00, 5800.00);

