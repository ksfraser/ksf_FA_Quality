# UAT-QC-003-calibration: Calibration System - User Acceptance Test

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-07

## Status
Proposed

## UAT Cases

### UAT-QC-003-01: Register Calibration Device

| Field | Value |
|-------|-------|
| Purpose | Verify calibration device can be registered |
| Prereqs | Logged in as Quality Engineer |
| Actor | Quality Engineer |
| Steps | 1. Navigate to Quality > Calibration<br>2. Click "Register Device"<br>3. Enter device name "Micrometer M-001"<br>4. Select type "Micrometer"<br>5. Enter serial number "MIC-2026-001"<br>6. Enter manufacturer "Mitutoyo"<br>7. Enter location "Quality Lab - Cabinet 3"<br>8. Enter measurement range 0-25mm<br>9. Enter tolerance 0.001mm<br>10. Select interval "Quarterly"<br>11. Save |
| Expected | Device created, next_due_date = today + 3 months |
| PASS Criteria | Device visible in list, all data saved correctly |

### UAT-QC-003-02: Record Calibration Event

| Field | Value |
|-------|-------|
| Purpose | Verify calibration can be recorded and due date updates |
| Prereqs | Device registered |
| Actor | Quality Technician |
| Steps | 1. Open device "Micrometer M-001"<br>2. Click "Record Calibration"<br>3. Enter calibration date today<br>4. Select result "Pass"<br>5. Enter technician name "John Smith"<br>6. Enter calibration standard "ISO 17025"<br>7. Enter as-found reading 25.000mm<br>8. Enter as-left reading 25.000mm<br>9. Verify next due date (auto-calculated to today + 3 months)<br>10. Save |
| Expected | Record created, device last_calibration_date and next_due_date updated |
| PASS Criteria | Record visible in history, device next_due_date = today + 3 months |

### UAT-QC-003-03: Verify Calibration Certificate Upload

| Field | Value |
|-------|-------|
| Purpose | Verify certificate can be attached to calibration record |
| Prereqs | Calibration record exists |
| Actor | Quality Technician |
| Steps | 1. Open calibration record<br>2. Click "Attach Certificate"<br>3. Select PDF file (max 10MB)<br>4. Enter certificate number "CAL-2026-001"<br>5. Save |
| Expected | Certificate path stored, file accessible |
| PASS Criteria | Certificate link visible in record, file downloads correctly |

### UAT-QC-003-04: Handle Out-of-Tolerance Result

| Field | Value |
|-------|-------|
| Purpose | Verify out-of-tolerance result triggers alert and status change |
| Prereqs | Device in active status |
| Actor | Quality Technician |
| Steps | 1. Open device "Scale S-001"<br>2. Click "Record Calibration"<br>3. Enter calibration date today<br>4. Select result "Out of Tolerance"<br>5. Enter as-found reading shows deviation outside tolerance<br>6. Save |
| Expected | Alert created, device status = out_of_service |
| PASS Criteria | Alert visible, device shows out_of_service status, alert type = device_out_of_tolerance |

### UAT-QC-003-05: Acknowledge Calibration Alert

| Field | Value |
|-------|-------|
| Purpose | Verify alert can be acknowledged by Quality Manager |
| Prereqs | Overdue or due-soon alert exists |
| Actor | Quality Manager |
| Steps | 1. Navigate to Quality > Calibration Alerts<br>2. View pending alerts<br>3. Click on alert for "Micrometer M-001"<br>4. Review alert details<br>5. Click "Acknowledge"<br>6. Confirm acknowledgment |
| Expected | Alert marked as acknowledged with timestamp and user |
| PASS Criteria | Alert shows acknowledged status, user name, and timestamp |

### UAT-QC-003-06: Batch Alert Generation

| Field | Value |
|-------|-------|
| Purpose | Verify daily batch correctly generates pending alerts |
| Prereqs | Multiple devices with varying due dates |
| Actor | System (batch job) |
| Steps | 1. Run calibration alert batch job<br>2. Check alerts generated for devices due within 7 days<br>3. Check alerts generated for overdue devices<br>4. Verify no duplicate alerts for same device/type |
| Expected | Alerts created for all devices meeting criteria |
| PASS Criteria | Correct count of due-soon and overdue alerts, no duplicates |

### UAT-QC-003-07: View Calibration History

| Field | Value |
|-------|-------|
| Purpose | Verify calibration history shows all records for device |
| Prereqs | Multiple calibration records exist |
| Actor | Quality Engineer |
| Steps | 1. Open device "Micrometer M-001"<br>2. Click "Calibration History"<br>3. View list of all calibrations |
| Expected | Chronological list showing all calibrations with dates, results, technicians |
| PASS Criteria | All records visible, sorted by date descending, correct data |

### UAT-QC-003-08: Device Status Transition

| Field | Value |
|-------|-------|
| Purpose | Verify device can be returned to service after repair |
| Prereqs | Device in out_of_service status |
| Actor | Quality Manager |
| Steps | 1. Open out_of_service device<br>2. Click "Return to Service"<br>3. Confirm status change<br>4. Verify status = active |
| Expected | Device status changed to active |
| PASS Criteria | Status updated, device appears in active device list |

### UAT-QC-003-09: Prevent Duplicate Serial Numbers

| Field | Value |
|-------|-------|
| Purpose | Verify system rejects duplicate serial numbers |
| Prereqs | Device with serial "MIC-2026-001" exists |
| Actor | Quality Engineer |
| Steps | 1. Navigate to Register Device<br>2. Enter serial number "MIC-2026-001"<br>3. Fill other required fields<br>4. Attempt to save |
| Expected | Error message: "Serial number already exists" |
| PASS Criteria | Save prevented, error displayed, no duplicate created |

### UAT-QC-003-10: Calibration Dashboard

| Field | Value |
|-------|-------|
| Purpose | Verify dashboard shows calibration status summary |
| Prereqs | Multiple devices with varying statuses |
| Actor | Quality Manager |
| Steps | 1. Navigate to Quality > Calibration Dashboard<br>2. View summary widgets |
| Expected | Dashboard shows: total devices, active, overdue, due soon, out of service |
| PASS Criteria | Counts match actual device statuses, widgets display correctly |

---

## Test Environment

| Parameter | Value |
|-----------|-------|
| Browser | Chrome/Firefox latest |
| PHP Version | 7.3+ |
| Database | MySQL/MariaDB |
| FrontAccounting | 2.4.19 |

---

## Traceability

| UAT Case | FR | AC |
|----------|----|----|
| UAT-QC-003-01 | FR-QC-003-001 | AC-01, AC-02, AC-03, AC-07, AC-08 |
| UAT-QC-003-02 | FR-QC-003-003 | AC-01, AC-05 |
| UAT-QC-003-03 | FR-QC-003-003 | AC-06 |
| UAT-QC-003-04 | FR-QC-003-003, FR-QC-003-004 | AC-04, AC-01, AC-03, AC-08 |
| UAT-QC-003-05 | FR-QC-003-004 | AC-05, AC-06 |
| UAT-QC-003-06 | FR-QC-003-004 | AC-01, AC-02, AC-04 |
| UAT-QC-003-07 | FR-QC-003-003 | - |
| UAT-QC-003-08 | FR-QC-003-001 | - |
| UAT-QC-003-09 | FR-QC-003-001 | AC-03 |
| UAT-QC-003-10 | FR-QC-003-001, FR-QC-003-002 | - |
