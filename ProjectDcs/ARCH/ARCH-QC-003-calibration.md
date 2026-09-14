# ARCH-QC-003-calibration - Quality Control Device Calibration Module Architecture

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-07

## Status
Proposed

---

## 1. System Context

### 1.1 Calibration Process Flow

```
┌─────────────────────┐     ┌─────────────────────┐     ┌─────────────────────┐     ┌─────────────────────┐
│   DeviceRegistry    │────▶│ CalibrationScheduler│────▶│  CalibrationRecord  │────▶│    AlertService     │
│                     │     │                      │     │                      │     │                     │
│ - device_id         │     │ - schedule_calculation│   │ - record_id         │     │ - alert_type        │
│ - device_name       │     │ - due_date           │     │ - device_id (FK)    │     │ - device_id (FK)    │
│ - device_type       │     │ - interval_type     │     │ - calibration_date  │     │ - record_id (FK)    │
│ - serial_number     │     │ - next_due          │     │ - result            │     │ - alert_sent        │
│ - location          │     │ - reminder_days     │     │ - technician        │     │ - alert_date        │
└─────────────────────┘     └─────────────────────┘     └─────────────────────┘     └─────────────────────┘
```

### 1.2 Business Process

1. **Register Device** → Add gauge to device registry
2. **Set Calibration Interval** → Define schedule (monthly/quarterly/annual)
3. **Calculate Due Dates** → Scheduler computes next calibration date
4. **Record Calibration** → Technician logs calibration result
5. **Trigger Alerts** → Notifications for upcoming/overdue calibration
6. **Handle Out-of-Tolerance** → Mark device for repair/quarantine

### 1.3 Context Boundaries

| Component | Responsibility | External Dependencies |
|-----------|----------------|----------------------|
| DeviceRegistry | Master device list, metadata | FA stock_items (optional) |
| CalibrationScheduler | Due date calculation, schedule management | None |
| CalibrationRecord | Calibration history, certificates | None |
| AlertService | Notification dispatch | FA hooks, email module |

---

## 2. Component Architecture

### 2.1 Core Classes

| Class | Namespace | Responsibility |
|-------|-----------|----------------|
| `CalibrationDevice` | `ksfraser\FrontAccounting\Quality\Entities` | Device lifecycle |
| `CalibrationRecord` | `ksfraser\FrontAccounting\Quality\Entities` | Single calibration event |
| `CalibrationScheduler` | `ksfraser\FrontAccounting\Quality\Services` | Due date calculation |
| `AlertService` | `ksfraser\FrontAccounting\Quality\Services` | Alert generation |
| `CalibrationDao` | `ksfraser\FrontAccounting\Quality\Dao` | Data access |
| `CalibrationWorkflowHooks` | `ksfraser\FrontAccounting\Quality\Hooks` | Pre/post CRUD hooks |

### 2.2 Class Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                             CalibrationDevice                                        │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ - id: int                                                                             │
│ - device_name: string                                                                 │
│ - device_type: enum(gauge, thermometer, scale, multimeter, micrometer, other)        │
│ - serial_number: string                                                               │
│ - manufacturer: string                                                                │
│ - location: string                                                                    │
│ - measurement_range_min: decimal                                                      │
│ - measurement_range_max: decimal                                                      │
│ - tolerance: decimal                                                                  │
│ - calibration_interval: enum(monthly, quarterly, semiannual, annual)                 │
│ - last_calibration_date: date|null                                                   │
│ - next_due_date: date                                                                │
│ - status: enum(active, out_of_service, retired)                                      │
│ - notes: text                                                                          │
│ - created_at: datetime                                                                │
│ - updated_at: datetime                                                                │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ + calculateNextDueDate(): date                                                         │
│ + isOverdue(): bool                                                                  │
│ + isDueSoon(int $days = 7): bool                                                      │
│ + markOutOfTolerance(): bool                                                          │
└─────────────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ 1..n
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           CalibrationRecord                                          │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ - id: int                                                                             │
│ - device_id: int (FK)                                                                 │
│ - calibration_date: date                                                              │
│ - result: enum(pass, fail, out_of_tolerance)                                          │
│ - technician_name: string                                                             │
│ - technician_id: int                                                                  │
│ - calibration_standard: string                                                        │
│ - temperature: decimal|null                                                           │
│ - humidity: decimal|null                                                              │
│ - as_found_reading: decimal|null                                                      │
│ - as_left_reading: decimal|null                                                       │
│ - correction_factor: decimal|null                                                     │
│ - certificate_number: string|null                                                     │
│ - certificate_path: string|null                                                       │
│ - next_due_date: date                                                                 │
│ - notes: text                                                                          │
│ - created_at: datetime                                                                │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ + isCompliant(): bool                                                                 │
│ + requiresFollowUp(): bool                                                            │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

### 2.3 Service Layer

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                      CalibrationScheduler (Service)                                   │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ + INTERVAL_MONTHLY: int = 1                                                           │
│ + INTERVAL_QUARTERLY: int = 3                                                        │
│ + INTERVAL_SEMIANNUAL: int = 6                                                       │
│ + INTERVAL_ANNUAL: int = 12                                                          │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ + calculateNextDueDate(\DateTime $lastCalibration, string $interval): \DateTime       │
│ + getDaysUntilDue(\DateTime $nextDue): int                                           │
│ + isOverdue(\DateTime $nextDue): bool                                                │
│ + isDueSoon(\DateTime $nextDue, int $days = 7): bool                                 │
│ + getOverdueDevices(array $devices): array                                            │
│ + getDevicesDueSoon(array $devices, int $days = 7): array                             │
└─────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────┐
│                          AlertService (Service)                                       │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ + ALERT_DUE_SOON: string = 'calibration_due_soon'                                    │
│ + ALERT_OVERDUE: string = 'calibration_overdue'                                      │
│ + ALERT_OUT_OF_TOLERANCE: string = 'device_out_of_tolerance'                         │
├─────────────────────────────────────────────────────────────────────────────────────┤
│ + createAlert(int $deviceId, string $alertType, int $recordId = null): int            │
│ + getPendingAlerts(): array                                                          │
│ + markAlertSent(int $alertId): bool                                                  │
│ + getAlertsForDevice(int $deviceId): array                                            │
│ + shouldSendReminder(CalibrationDevice $device): bool                                │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Database Schema

### 3.1 Table: 0_calibration_devices

```sql
CREATE TABLE IF NOT EXISTS `0_calibration_devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_name` VARCHAR(255) NOT NULL,
  `device_type` ENUM('gauge', 'thermometer', 'scale', 'multimeter', 'micrometer', 'caliper', 'other') NOT NULL DEFAULT 'gauge',
  `serial_number` VARCHAR(100) NOT NULL,
  `manufacturer` VARCHAR(255) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `measurement_range_min` DECIMAL(10,4) DEFAULT NULL,
  `measurement_range_max` DECIMAL(10,4) DEFAULT NULL,
  `tolerance` DECIMAL(10,4) DEFAULT NULL,
  `calibration_interval` ENUM('monthly', 'quarterly', 'semiannual', 'annual') NOT NULL DEFAULT 'annual',
  `last_calibration_date` DATE DEFAULT NULL,
  `next_due_date` DATE NOT NULL,
  `status` ENUM('active', 'out_of_service', 'retired') NOT NULL DEFAULT 'active',
  `notes` TEXT,
  `created_by` INT(11) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_serial_number` (`serial_number`),
  KEY `idx_device_type` (`device_type`),
  KEY `idx_status` (`status`),
  KEY `idx_next_due_date` (`next_due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.2 Table: 0_calibration_records

```sql
CREATE TABLE IF NOT EXISTS `0_calibration_records` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `calibration_date` DATE NOT NULL,
  `result` ENUM('pass', 'fail', 'out_of_tolerance') NOT NULL,
  `technician_name` VARCHAR(255) NOT NULL,
  `technician_id` INT(11) NOT NULL,
  `calibration_standard` VARCHAR(255) DEFAULT NULL,
  `temperature` DECIMAL(5,2) DEFAULT NULL,
  `humidity` DECIMAL(5,2) DEFAULT NULL,
  `as_found_reading` DECIMAL(10,4) DEFAULT NULL,
  `as_left_reading` DECIMAL(10,4) DEFAULT NULL,
  `correction_factor` DECIMAL(10,4) DEFAULT NULL,
  `certificate_number` VARCHAR(100) DEFAULT NULL,
  `certificate_path` VARCHAR(500) DEFAULT NULL,
  `next_due_date` DATE NOT NULL,
  `notes` TEXT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_device_id` (`device_id`),
  KEY `idx_calibration_date` (`calibration_date`),
  KEY `idx_result` (`result`),
  KEY `idx_next_due_date` (`next_due_date`),
  CONSTRAINT `fk_calibration_device` FOREIGN KEY (`device_id`) REFERENCES `0_calibration_devices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.3 Table: 0_calibration_alerts

```sql
CREATE TABLE IF NOT EXISTS `0_calibration_alerts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `device_id` INT(11) NOT NULL,
  `record_id` INT(11) DEFAULT NULL,
  `alert_type` ENUM('calibration_due_soon', 'calibration_overdue', 'device_out_of_tolerance') NOT NULL,
  `alert_date` DATE NOT NULL,
  `alert_sent` DATETIME DEFAULT NULL,
  `alert_destination` VARCHAR(255) DEFAULT NULL,
  `message` TEXT,
  `acknowledged` TINYINT(1) NOT NULL DEFAULT 0,
  `acknowledged_by` INT(11) DEFAULT NULL,
  `acknowledged_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_device_id` (`device_id`),
  KEY `idx_alert_type` (`alert_type`),
  KEY `idx_alert_sent` (`alert_sent`),
  KEY `idx_acknowledged` (`acknowledged`),
  CONSTRAINT `fk_alert_device` FOREIGN KEY (`device_id`) REFERENCES `0_calibration_devices`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alert_record` FOREIGN KEY (`record_id`) REFERENCES `0_calibration_records`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 4. Calibration Intervals

| Interval | Months | Typical Use Case |
|----------|--------|------------------|
| Monthly | 1 | High-precision gauges, frequent use |
| Quarterly | 3 | Regular use gauges |
| Semiannual | 6 | Standard equipment |
| Annual | 12 | Low-use, stable instruments |

---

## 5. Alert Thresholds

| Alert Type | Trigger | Default Lead Time |
|------------|---------|-------------------|
| Due Soon | next_due_date within N days | 7 days |
| Overdue | next_due_date < today | N/A |
| Out of Tolerance | record result = 'out_of_tolerance' | Immediate |

---

## 6. File Structure

```
ksf_FA_Quality/
├── ProjectDcs/
│   ├── ARCH/
│   │   └── ARCH-QC-003-calibration.md
│   ├── BR/
│   │   └── BR-QC-003-calibration.md
│   ├── FR/
│   │   ├── FR-QC-003-001.md
│   │   ├── FR-QC-003-002.md
│   │   ├── FR-QC-003-003.md
│   │   └── FR-QC-003-004.md
│   ├── UT/
│   │   └── UT-QC-003-001-001.md
│   ├── UAT/
│   │   └── UAT-QC-003-calibration.md
│   └── RTM-QC-003-calibration.md
├── src/
│   ├── Entities/
│   │   ├── CalibrationDevice.php
│   │   └── CalibrationRecord.php
│   ├── Services/
│   │   ├── CalibrationScheduler.php
│   │   └── AlertService.php
│   └── Dao/
│       └── CalibrationDao.php
├── sql/
│   └── install.sql
└── hooks.php
```

---

## 7. Dependencies

| Dependency | Type | Purpose |
|------------|------|---------|
| ksf_FA_Common | Trait | WorkflowHooksTrait, CrudOperationsTrait |
| ksf_common_db | Package | DbConnectionInterface for DAO |
| FA Session | Core | User authentication and authorization |
| ksf_FA_Documents | Module | Certificate file storage |

---

## 8. Security

| Security Section | Constant | Access Level |
|------------------|----------|--------------|
| View Calibration | `SS_ksf_FA_QUALITY_CALIBRATION_VIEW` | Quality Viewer+ |
| Create Device | `SS_ksf_FA_QUALITY_CALIBRATION_DEVICE_CREATE` | Quality Engineer+ |
| Record Calibration | `SS_ksf_FA_QUALITY_CALIBRATION_RECORD` | Quality Technician+ |
| Manage Alerts | `SS_ksf_FA_QUALITY_CALIBRATION_ALERTS` | Quality Manager+ |
| Admin | `SS_ksf_FA_QUALITY_CALIBRATION_ADMIN` | System Admin |
