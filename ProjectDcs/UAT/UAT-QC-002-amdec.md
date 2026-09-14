# UAT-QC-002-amdec: AMDEC/FMEA System - User Acceptance Test

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-07

## Status
Approved

## UAT Cases

### UAT-QC-002-01: Create AMDEC Project

| Field | Value |
|-------|-------|
| Purpose | Verify AMDEC project can be created |
| Prereqs | Logged in as Quality Engineer |
| Actor | Quality Engineer |
| Steps | 1. Navigate to Quality > AMDEC<br>2. Click "New AMDEC Project"<br>3. Enter project name "Motor Assembly AMDEC"<br>4. Select type "Process"<br>5. Enter scope "Analyze motor assembly process failures"<br>6. Save |
| Expected | Project created with unique ID, status = Draft |
| PASS Criteria | Project visible in list, all data saved correctly |

### UAT-QC-002-02: Add Failure Mode

| Field | Value |
|-------|-------|
| Purpose | Verify failure mode can be added to project |
| Prereqs | AMDEC project in Open status |
| Actor | Quality Engineer |
| Steps | 1. Open AMDEC project<br>2. Click "Add Failure Mode"<br>3. Enter item number "MTR-001"<br>4. Enter failure mode "Winding short circuit"<br>5. Enter effect "Motor overheating, potential fire hazard"<br>6. Save |
| Expected | Failure mode added with RPN = 0 (awaiting ratings) |
| PASS Criteria | Failure mode visible in project, RPN shows 0 |

### UAT-QC-002-03: Rate Severity/Occurrence/Detection

| Field | Value |
|-------|-------|
| Purpose | Verify S/O/D ratings can be entered |
| Prereqs | Failure mode exists |
| Actor | Quality Engineer |
| Steps | 1. Open failure mode<br>2. Select Severity = 8 (High impact)<br>3. Select Occurrence = 7 (High frequency)<br>4. Select Detection = 3 (Good controls)<br>5. Save |
| Expected | RPN calculated as 8×7×3 = 168 |
| PASS Criteria | RPN displays 168, risk level shows "Critical" |

### UAT-QC-002-04: Verify RPN Calculation

| Field | Value |
|-------|-------|
| Purpose | Verify RPN auto-calculates correctly |
| Prereqs | S/O/D ratings entered |
| Actor | Quality Engineer |
| Steps | 1. View failure mode list<br>2. Check RPN column |
| Expected | RPN = 168 (8×7×3) |
| PASS Criteria | RPN matches calculation, color coded red |

### UAT-QC-002-05: Create Action for High RPN

| Field | Value |
|-------|-------|
| Purpose | Verify action created for critical RPN |
| Prereqs | Failure mode with RPN ≥ 100 |
| Actor | Quality Engineer |
| Steps | 1. Open failure mode with RPN 168<br>2. Click "Create Action"<br>3. Enter description "Add thermal fuse to winding"<br>4. Select responsible user<br>5. Set due date next week<br>6. Save |
| Expected | Action created and linked to failure mode |
| PASS Criteria | Action visible, status = Pending, due date set |

### UAT-QC-002-06: Complete Action

| Field | Value |
|-------|-------|
| Purpose | Verify action can be marked complete |
| Prereqs | Action in Pending/In Progress status |
| Actor | Responsible User |
| Steps | 1. Open action<br>2. Click "Start Progress"<br>3. Enter result "Thermal fuse installed, tested"<br>4. Click "Complete" |
| Expected | Action status = Completed, result saved |
| PASS Criteria | Status locked, completion timestamp set |

### UAT-QC-002-07: View RPN Summary Report

| Field | Value |
|-------|-------|
| Purpose | Verify RPN summary report accuracy |
| Prereqs | Multiple failure modes with varying RPNs |
| Actor | Quality Manager |
| Steps | 1. Open AMDEC project<br>2. Click "Reports" > "RPN Summary"<br>3. Review summary statistics |
| Expected | Report shows correct counts, averages, highest/lowest RPN |
| PASS Criteria | All statistics match actual failure mode data |

### UAT-QC-002-08: Risk Distribution Chart

| Field | Value |
|-------|-------|
| Purpose | Verify risk distribution chart accuracy |
| Prereqs | Multiple failure modes with different RPNs |
| Actor | Quality Manager |
| Steps | 1. Navigate to Reports > Risk Distribution<br>2. View chart |
| Expected | Distribution shows Low/Medium/High/Critical counts |
| PASS Criteria | Counts and percentages match failure modes |

### UAT-QC-002-09: Generate Printable Worksheet

| Field | Value |
|-------|-------|
| Purpose | Verify AMDEC worksheet contains all data |
| Prereqs | Project with failure modes and actions |
| Actor | Quality Engineer |
| Steps | 1. Click "Reports" > "AMDEC Worksheet"<br>2. Click "Print" or "Export PDF" |
| Expected | Worksheet contains all failure modes, ratings, RPNs, actions |
| PASS Criteria | PDF generated, all data present |

### UAT-QC-002-10: Prevent Closing Project with Open Actions

| Field | Value |
|-------|-------|
| Purpose | Verify project cannot close with overdue actions |
| Prereqs | Project has overdue actions |
| Actor | Quality Manager |
| Steps | 1. Attempt to close project<br>2. Confirm closure |
| Expected | Warning displayed, closure prevented |
| PASS Criteria | Project status remains Open, warning shown |

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
| UAT-QC-002-01 | FR-QC-002-001 | AC-01, AC-02, AC-03, AC-04 |
| UAT-QC-002-02 | FR-QC-002-002 | AC-01, AC-02, AC-03, AC-04 |
| UAT-QC-002-03 | FR-QC-002-003 | AC-01, AC-02, AC-03, AC-05 |
| UAT-QC-002-04 | FR-QC-002-004 | AC-01, AC-02, AC-03, AC-04 |
| UAT-QC-002-05 | FR-QC-002-005 | AC-01, AC-02, AC-03 |
| UAT-QC-002-06 | FR-QC-002-005 | AC-04, AC-05 |
| UAT-QC-002-07 | FR-QC-002-006 | AC-01 |
| UAT-QC-002-08 | FR-QC-002-006 | AC-02 |
| UAT-QC-002-09 | FR-QC-002-006 | AC-03, AC-04 |
| UAT-QC-002-10 | FR-QC-002-001 | - |
