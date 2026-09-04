-- 8D Quality Reports table
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_number` VARCHAR(20) NOT NULL UNIQUE,
    `problem_description` TEXT NOT NULL,
    `part_number` VARCHAR(20) NULL,
    `qty_affected` DECIMAL(10,2) NULL,
    `symptom` VARCHAR(255) NULL,
    `status` ENUM('Open', 'In Progress', 'Closed', 'Rejected') NOT NULL DEFAULT 'Open',
    `priority` ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL DEFAULT 'Medium',
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL,
    `closed_at` DATETIME NULL,
    `closed_by` INT NULL,
    `due_date` DATE NULL,
    `customer_id` INT NULL,
    `supplier_id` INT NULL,
    `reference_po` VARCHAR(20) NULL,
    `notes` TEXT NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_part_number` (`part_number`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D1: Team members
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_team` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'Team Member',
    `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_team_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D3: Containment actions
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_containment` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `action_description` TEXT NOT NULL,
    `responsible_user_id` INT NOT NULL,
    `target_date` DATE NOT NULL,
    `completed_date` DATE NULL,
    `effectiveness` VARCHAR(50) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_containment_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D4: Root cause analysis
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_root_cause` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `cause_category` ENUM('Man', 'Machine', 'Method', 'Material', 'Measurement', 'Environment', 'Other') NOT NULL,
    `cause_description` TEXT NOT NULL,
    `evidence` TEXT NULL,
    `confirmed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_rootcause_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D5: Corrective actions
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_corrective_action` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `root_cause_id` INT NULL,
    `action_description` TEXT NOT NULL,
    `responsible_user_id` INT NOT NULL,
    `target_date` DATE NOT NULL,
    `completed_date` DATE NULL,
    `validation_method` VARCHAR(255) NULL,
    `effectiveness_review_date` DATE NULL,
    `status` ENUM('Planned', 'In Progress', 'Completed', 'Verified', 'Cancelled') NOT NULL DEFAULT 'Planned',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_ca_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D6: Implementation tracking
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_implementation` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `corrective_action_id` INT NOT NULL,
    `implementation_notes` TEXT NULL,
    `evidence_of_implementation` TEXT NULL,
    `verified_by` INT NULL,
    `verified_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_impl_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D7: Prevention measures
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_prevention` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `prevention_description` TEXT NOT NULL,
    `system_or_process_changed` VARCHAR(255) NULL,
    `responsible_user_id` INT NOT NULL,
    `target_date` DATE NOT NULL,
    `completed_date` DATE NULL,
    `status` ENUM('Planned', 'Implemented', 'Verified') NOT NULL DEFAULT 'Planned',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_prevention_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- D8: Recognition
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_recognition` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `recognition_type` VARCHAR(100) NULL,
    `recognition_description` TEXT NULL,
    `recognized_by` INT NOT NULL,
    `recognized_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_recognition_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attachments
CREATE TABLE IF NOT EXISTS `0_ksf_quality_8d_attachment` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eightd_id` INT NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(50) NULL,
    `file_size` INT NULL,
    `uploaded_by` INT NOT NULL,
    `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_eightd_id` (`eightd_id`),
    CONSTRAINT `fk_attachment_eightd` FOREIGN KEY (`eightd_id`) REFERENCES `0_ksf_quality_8d` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;