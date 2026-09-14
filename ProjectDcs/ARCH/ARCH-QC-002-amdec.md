# ARCH-QC-002-amdec - AMDEC/FMEA Module Architecture

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

### 1.1 AMDEC Project Flow

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  AmdecProject   │────▶│  FailureMode    │────▶│  RpnCalculator  │────▶│     Action      │
│                 │     │                 │     │                 │     │                 │
│ - name          │     │ - failure_mode  │     │ - severity (S)  │     │ - description   │
│ - type          │     │ - effect        │     │ - occurrence(O)  │     │ - responsible   │
│ - scope         │     │ - cause         │     │ - detection (D)  │     │ - due_date      │
│ - product_id    │     │ - S, O, D       │     │ - RPN = S×O×D   │     │ - status        │
└─────────────────┘     └─────────────────┘     └─────────────────┘     └─────────────────┘
```

### 1.2 Business Process

1. **Create AMDEC Project** → Define scope (Design or Process)
2. **Identify Failure Modes** → What can go wrong?
3. **Rate Severity (S)** → Impact of effect (1-10)
4. **Rate Occurrence (O)** → Frequency of cause (1-10)
5. **Rate Detection (D)** → Ability to detect before release (1-10)
6. **Calculate RPN** → S × O × D (range: 1-1000)
7. **Trigger Actions** → High RPN (typically ≥100) requires action
8. **Track Actions** → Monitor implementation
9. **Report** → Risk prioritization matrix

---

## 2. Component Architecture

### 2.1 Core Classes

| Class | Namespace | Responsibility |
|-------|-----------|----------------|
| `AmdecProject` | `ksfraser\FrontAccounting\Quality\Entities` | AMDEC project lifecycle |
| `FailureMode` | `ksfraser\FrontAccounting\Quality\Entities` | Single failure mode with S/O/D |
| `RpnCalculator` | `ksfraser\FrontAccounting\Quality\Services` | RPN calculation and threshold |
| `AmdecAction` | `ksfraser\FrontAccounting\Quality\Entities` | Follow-up action tracking |
| `AmdecDao` | `ksfraser\FrontAccounting\Quality\Dao` | Data access for all AMDEC tables |
| `AmdecWorkflowHooks` | `ksfraser\FrontAccounting\Quality\Hooks` | Pre/post CRUD hooks |

### 2.2 Class Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                        AmdecProject                                 │
├─────────────────────────────────────────────────────────────────────┤
│ - id: int                                                           │
│ - project_name: string                                              │
│ - project_type: enum(design, process)                               │
│ - scope_description: string                                         │
│ - product_id: int|null                                              │
│ - bom_id: int|null                                                  │
│ - status: enum(draft, active, closed)                               │
│ - created_by: int                                                   │
│ - created_at: datetime                                              │
├─────────────────────────────────────────────────────────────────────┤
│ + create(): int                                                     │
│ + update(): bool                                                    │
│ + close(): bool                                                     │
│ + getFailureModes(): FailureMode[]                                  │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              │ 1..n
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        FailureMode                                  │
├─────────────────────────────────────────────────────────────────────┤
│ - id: int                                                           │
│ - project_id: int (FK)                                              │
│ - item_number: string                                               │
│ - potential_failure_mode: string                                    │
│ - potential_effect: string                                         │
│ - severity: int (1-10)                                              │
│ - potential_cause: string                                           │
│ - occurrence: int (1-10)                                            │
│ - current_controls: string                                          │
│ - detection: int (1-10)                                             │
│ - rpn: int (calculated, read-only)                                  │
│ - notes: string                                                     │
├─────────────────────────────────────────────────────────────────────┤
│ + calculateRpn(): int                                               │
│ + requiresAction(): bool                                           │
│ + getActionThreshold(): int                                        │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              │ 1..n
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         AmdecAction                                 │
├─────────────────────────────────────────────────────────────────────┤
│ - id: int                                                           │
│ - failure_mode_id: int (FK)                                         │
│ - action_description: string                                        │
│ - responsible_user_id: int                                          │
│ - due_date: date                                                    │
│ - status: enum(pending, in_progress, completed, cancelled)          │
│ - result: string                                                     │
│ - completed_at: datetime|null                                       │
│ - created_at: datetime                                              │
├─────────────────────────────────────────────────────────────────────┤
│ + markComplete(string $result): bool                                │
│ + isOverdue(): bool                                                │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.3 Service Layer

```
┌─────────────────────────────────────────────────────────────────────┐
│                       RpnCalculator (Service)                        │
├─────────────────────────────────────────────────────────────────────┤
│ + DEFAULT_THRESHOLD: int = 100                                      │
├─────────────────────────────────────────────────────────────────────┤
│ + calculate(int $severity, int $occurrence, int $detection): int    │
│ + requiresAction(int $rpn, int $threshold): bool                    │
│ + getRiskLevel(int $rpn): enum(low, medium, high, critical)        │
│ + getPrioritySortOrder(array $failureModes): array                  │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 3. Database Schema

### 3.1 Table: 0_amdec_projects

```sql
CREATE TABLE IF NOT EXISTS `0_amdec_projects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `project_name` VARCHAR(255) NOT NULL,
  `project_type` ENUM('design', 'process') NOT NULL DEFAULT 'process',
  `scope_description` TEXT,
  `product_id` INT(11) DEFAULT NULL,
  `bom_id` INT(11) DEFAULT NULL,
  `status` ENUM('draft', 'active', 'closed') NOT NULL DEFAULT 'draft',
  `created_by` INT(11) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.2 Table: 0_amdec_failure_modes

```sql
CREATE TABLE IF NOT EXISTS `0_amdec_failure_modes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) NOT NULL,
  `item_number` VARCHAR(100) NOT NULL,
  `potential_failure_mode` TEXT NOT NULL,
  `potential_effect` TEXT NOT NULL,
  `severity` TINYINT(3) NOT NULL CHECK (severity BETWEEN 1 AND 10),
  `potential_cause` TEXT,
  `occurrence` TINYINT(3) NOT NULL CHECK (occurrence BETWEEN 1 AND 10),
  `current_controls` TEXT,
  `detection` TINYINT(3) NOT NULL CHECK (detection BETWEEN 1 AND 10),
  `rpn` INT(11) NOT NULL DEFAULT 0,
  `notes` TEXT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_rpn` (`rpn`),
  CONSTRAINT `fk_amdec_project` FOREIGN KEY (`project_id`) REFERENCES `0_amdec_projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.3 Table: 0_amdec_actions

```sql
CREATE TABLE IF NOT EXISTS `0_amdec_actions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `failure_mode_id` INT(11) NOT NULL,
  `action_description` TEXT NOT NULL,
  `responsible_user_id` INT(11) NOT NULL,
  `due_date` DATE NOT NULL,
  `status` ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `result` TEXT,
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_failure_mode_id` (`failure_mode_id`),
  KEY `idx_responsible` (`responsible_user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_due_date` (`due_date`),
  CONSTRAINT `fk_amdec_failure_mode` FOREIGN KEY (`failure_mode_id`) REFERENCES `0_amdec_failure_modes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 4. RPN Risk Matrix

| RPN Range | Risk Level | Color | Action Required |
|-----------|------------|-------|-----------------|
| 1-20 | Low | Green | No immediate action |
| 21-50 | Medium | Yellow | Monitor |
| 51-100 | High | Orange | Action recommended |
| 101-1000 | Critical | Red | Action required |

---

## 5. File Structure

```
ksf_FA_Quality/
├── ProjectDcs/
│   ├── ARCH/
│   │   └── ARCH-QC-002-amdec.md
│   ├── BR/
│   │   └── BR-QC-002-amdec-fmea.md
│   ├── FR/
│   │   ├── FR-QC-002-001.md
│   │   ├── FR-QC-002-002.md
│   │   ├── FR-QC-002-003.md
│   │   ├── FR-QC-002-004.md
│   │   ├── FR-QC-002-005.md
│   │   └── FR-QC-002-006.md
│   ├── UT/
│   │   └── UT-QC-002-001-001.md
│   └── UAT/
│       └── UAT-QC-002-amdec.md
├── src/
│   ├── Entities/
│   │   ├── AmdecProject.php
│   │   ├── FailureMode.php
│   │   └── AmdecAction.php
│   ├── Services/
│   │   └── RpnCalculator.php
│   └── Dao/
│       └── AmdecDao.php
├── sql/
│   └── install.sql
└── hooks.php
```

---

## 6. Dependencies

| Dependency | Type | Purpose |
|------------|------|---------|
| ksf_FA_Common | Trait | WorkflowHooksTrait, CrudOperationsTrait |
| ksf_common_db | Package | DbConnectionInterface for DAO |
| FA Session | Core | User authentication and authorization |
| ksf_FA_ProductLookup | Module | Product/stock item reference |

---

## 7. Security

| Security Section | Constant | Access Level |
|------------------|----------|--------------|
| View AMDEC | `SS_ksf_FA_QUALITY_AMDDEC_VIEW` | Quality Viewer+ |
| Create AMDEC | `SS_ksf_FA_QUALITY_AMDDEC_CREATE` | Quality Engineer+ |
| Edit AMDEC | `SS_ksf_FA_QUALITY_AMDDEC_EDIT` | Quality Engineer+ |
| Close AMDEC | `SS_ksf_FA_QUALITY_AMDDEC_CLOSE` | Quality Manager+ |
| Admin | `SS_ksf_FA_QUALITY_AMDDEC_ADMIN` | System Admin |
