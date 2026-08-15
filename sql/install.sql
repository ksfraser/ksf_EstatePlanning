-- ksf_EstatePlanning/sql/install.sql
-- This is the core database schema for the business logic module
CREATE TABLE IF NOT EXISTS `ksf_estate_planning_tax_rates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `jurisdiction` VARCHAR(50) NOT NULL,
    `tax_year` INT NOT NULL,
    `rate` DECIMAL(8,4) DEFAULT 0.0000,
    `threshold` DECIMAL(15,2) DEFAULT 0.00,
    `effective_date` DATE DEFAULT NULL,
    `expiry_date` DATE DEFAULT NULL,
    UNIQUE KEY `jurisdiction_tax_year` (`jurisdiction`, `tax_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ksf_estate_planning_scenarios` (
    `scenario_id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT DEFAULT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `status` ENUM('active', 'inactive', 'archived') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ksf_estate_planning_beneficiaries` (
    `beneficiary_id` INT AUTO_INCREMENT PRIMARY KEY,
    `scenario_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `allocation_percentage` DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    `relationship_type` VARCHAR(50) DEFAULT NULL,
    `is_primary` BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (`scenario_id`) REFERENCES `ksf_estate_planning_scenarios`(`scenario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ksf_estate_planning_trusts` (
    `trust_id` INT AUTO_INCREMENT PRIMARY KEY,
    `scenario_id` INT NOT NULL,
    `trust_name` VARCHAR(255) DEFAULT NULL,
    `creation_date` DATE DEFAULT NULL,
    `beneficiary_type` VARCHAR(50) DEFAULT NULL,
    `is_revocable` BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (`scenario_id`) REFERENCES `ksf_estate_planning_scenarios`(`scenario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ksf_estate_planning_events` (
    `event_id` INT AUTO_INCREMENT PRIMARY KEY,
    `scenario_id` INT NOT NULL,
    `event_type` ENUM('death', 'incapacity', 'birth', 'divorce', 'marriage') DEFAULT 'death',
    `event_date` DATE DEFAULT NULL,
    `associated_party_id` INT DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    FOREIGN KEY (`scenario_id`) REFERENCES `ksf_estate_planning_scenarios`(`scenario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;