ALTER TABLE `invoices`
    ADD COLUMN `credit_applied_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `paid_amount`;

CREATE TABLE `company_credit_ledger` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_id` BIGINT UNSIGNED NOT NULL,
    `invoice_id` BIGINT UNSIGNED NULL,
    `entry_type` ENUM('credit','debit') NOT NULL,
    `entry_date` DATE NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `description` VARCHAR(255) NULL,
    `source_type` VARCHAR(100) NULL,
    `source_id` BIGINT UNSIGNED NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `company_credit_ledger_company_id_index` (`company_id`),
    KEY `company_credit_ledger_invoice_id_index` (`invoice_id`),
    KEY `company_credit_ledger_source_index` (`source_type`, `source_id`),
    KEY `company_credit_ledger_entry_date_index` (`entry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoice_credit_applications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` BIGINT UNSIGNED NOT NULL,
    `company_id` BIGINT UNSIGNED NOT NULL,
    `application_date` DATE NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `reason` VARCHAR(255) NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `invoice_credit_applications_invoice_id_index` (`invoice_id`),
    KEY `invoice_credit_applications_company_id_index` (`company_id`),
    KEY `invoice_credit_applications_application_date_index` (`application_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

