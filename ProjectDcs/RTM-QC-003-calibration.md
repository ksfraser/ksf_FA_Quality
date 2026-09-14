# RTM-QC-003-calibration - Calibration Module Traceability Matrix

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-07

## Status
Proposed

---

## Traceability Matrix

| BR | FR | UC | UT | UAT | Status |
|----|----|----|----|-----|--------|
| BR-QC-003 | FR-QC-003-001 | UC-QC-003 | UT-QC-003-001-001 | UAT-QC-003-01, UAT-QC-003-09, UAT-QC-003-10 | Proposed |
| BR-QC-003 | FR-QC-003-002 | UC-QC-003 | UT-QC-003-001-001 | UAT-QC-003-02, UAT-QC-003-10 | Proposed |
| BR-QC-003 | FR-QC-003-003 | UC-QC-003 | UT-QC-003-001-001 | UAT-QC-003-02, UAT-QC-003-03, UAT-QC-003-04, UAT-QC-003-07 | Proposed |
| BR-QC-003 | FR-QC-003-004 | UC-QC-003 | UT-QC-003-001-001 | UAT-QC-003-04, UAT-QC-003-05, UAT-QC-003-06 | Proposed |

---

## Requirement Coverage Details

### BR-QC-003: Quality Control Device Calibration

| Scope Item | FR | Coverage |
|------------|----|----------|
| Device registry | FR-QC-003-001 | Full |
| Calibration interval | FR-QC-003-001, FR-QC-003-002 | Full |
| Calibration history | FR-QC-003-003 | Full |
| Measurement range and tolerance | FR-QC-003-001 | Full |
| Out-of-tolerance handling | FR-QC-003-003, FR-QC-003-004 | Full |
| Alert for upcoming/overdue | FR-QC-003-004 | Full |
| Certificate attachment | FR-QC-003-003 | Full |

---

## FR to AC Mapping

### FR-QC-003-001: Device Registry

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Device created with unique ID | UT-QC-003-001-001 | UAT-QC-003-01 |
| AC-02 | Serial number is required | - | UAT-QC-003-01 |
| AC-03 | Serial number uniqueness enforced | - | UAT-QC-003-09 |
| AC-04 | Device type defaults to 'gauge' | UT-QC-003-001-001 | - |
| AC-05 | Calibration interval defaults to 'annual' | UT-QC-003-001-001 | - |
| AC-06 | Next due date calculated correctly | UT-QC-003-001-001 | UAT-QC-003-02 |
| AC-07 | Status defaults to 'active' | - | UAT-QC-003-01 |
| AC-08 | Creator recorded from session | - | UAT-QC-003-01 |

### FR-QC-003-002: Calibration Scheduling

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Monthly interval adds 1 month | UT-QC-003-001-001 | - |
| AC-02 | Quarterly interval adds 3 months | UT-QC-003-001-001 | - |
| AC-03 | Semiannual interval adds 6 months | UT-QC-003-001-001 | - |
| AC-04 | Annual interval adds 12 months | UT-QC-003-001-001 | - |
| AC-05 | End-of-month handling correct | UT-QC-003-001-001 | - |
| AC-06 | Overdue detection works | UT-QC-003-001-001 | - |
| AC-07 | Due soon detection works | UT-QC-003-001-001 | - |
| AC-08 | Next due date updates after record | - | UAT-QC-003-02 |

### FR-QC-003-003: Calibration Recording

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Record created with unique ID | - | UAT-QC-003-02 |
| AC-02 | Calibration date is required | - | UAT-QC-003-03 |
| AC-03 | Result is required | - | UAT-QC-003-03 |
| AC-04 | Out-of-tolerance triggers status change | - | UAT-QC-003-04 |
| AC-05 | Next due date auto-updated | - | UAT-QC-003-02 |
| AC-06 | Certificate uploaded | - | UAT-QC-003-03 |
| AC-07 | Technician ID validated | - | UAT-QC-003-03 |
| AC-08 | Future date rejected | - | UAT-QC-003-03 |

### FR-QC-003-004: Alert System

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Overdue alert created for past-due device | UT-QC-003-001-001 | UAT-QC-003-06 |
| AC-02 | Due soon alert created for device within threshold | UT-QC-003-001-001 | UAT-QC-003-06 |
| AC-03 | Out-of-tolerance alert created immediately | - | UAT-QC-003-04 |
| AC-04 | Only one active overdue alert per device | - | UAT-QC-003-06 |
| AC-05 | Alert can be acknowledged | - | UAT-QC-003-05 |
| AC-06 | Alert shows correct priority | - | UAT-QC-003-05 |
| AC-07 | Batch job generates alerts correctly | - | UAT-QC-003-06 |
| AC-08 | Multiple out-of-tolerance alerts allowed | - | UAT-QC-003-04 |

---

## File Manifest

| File | Type | Description |
|------|------|-------------|
| `ARCH/ARCH-QC-003-calibration.md` | Architecture | System design, schema, components |
| `BR/BR-QC-003-calibration.md` | Business Requirement | High-level requirements |
| `FR/FR-QC-003-001.md` | Functional Requirement | Device registry |
| `FR/FR-QC-003-002.md` | Functional Requirement | Calibration scheduling |
| `FR/FR-QC-003-003.md` | Functional Requirement | Calibration recording |
| `FR/FR-QC-003-004.md` | Functional Requirement | Alert system |
| `UT/UT-QC-003-001-001.md` | Unit Test | Calibration scheduling test |
| `UAT/UAT-QC-003-calibration.md` | UAT Plan | User acceptance test cases |
| `RTM-QC-003-calibration.md` | This file | Traceability matrix |

---

## Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Business Analyst | | | |
| Quality Manager | | | |
| Developer | | | |
| Test Lead | | | |
