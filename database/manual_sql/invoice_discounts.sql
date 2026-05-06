ALTER TABLE `invoices`
    ADD COLUMN `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `tax_amount`;

CREATE TABLE `invoice_discounts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_id` BIGINT UNSIGNED NOT NULL,
    `invoice_id` BIGINT UNSIGNED NOT NULL,
    `discount_type` ENUM('percentage','amount') NOT NULL,
    `discount_value` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `discount_date` DATE NOT NULL,
    `reason` VARCHAR(255) NOT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `invoice_discounts_invoice_id_index` (`invoice_id`),
    KEY `invoice_discounts_company_id_index` (`company_id`),
    KEY `invoice_discounts_discount_date_index` (`discount_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

