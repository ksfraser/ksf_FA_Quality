# RTM-QC-002-amdec - AMDEC/FMEA Traceability Matrix

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-07

## Status
Approved

---

## Traceability Matrix

| BR | FR | UC | UT | UAT | Status |
|----|----|----|----|-----|--------|
| BR-QC-002 | FR-QC-002-001 | UC-QC-002 | UT-QC-002-001-001 | UAT-QC-002-01 | Approved |
| BR-QC-002 | FR-QC-002-002 | UC-QC-002 | UT-QC-002-001-001 | UAT-QC-002-02 | Approved |
| BR-QC-002 | FR-QC-002-003 | UC-QC-002 | UT-QC-002-001-001 | UAT-QC-002-03 | Approved |
| BR-QC-002 | FR-QC-002-004 | UC-QC-002 | UT-QC-002-001-001 | UAT-QC-002-04 | Approved |
| BR-QC-002 | FR-QC-002-005 | UC-QC-002 | - | UAT-QC-002-05, UAT-QC-002-06 | Approved |
| BR-QC-002 | FR-QC-002-006 | UC-QC-002 | - | UAT-QC-002-07, UAT-QC-002-08, UAT-QC-002-09 | Approved |

---

## Requirement Coverage Details

### BR-QC-002: AMDEC/FMEA Analysis System

| Scope Item | FR | Coverage |
|------------|----|----------|
| AMDEC project creation | FR-QC-002-001 | Full |
| Failure mode identification | FR-QC-002-002 | Full |
| Effect analysis | FR-QC-002-002 | Full |
| Severity rating (1-10) | FR-QC-002-003 | Full |
| Occurrence rating (1-10) | FR-QC-002-003 | Full |
| Detection rating (1-10) | FR-QC-002-003 | Full |
| RPN calculation (S×O×D) | FR-QC-002-004 | Full |
| Action priority | FR-QC-002-004, FR-QC-002-005 | Full |
| Recommended actions | FR-QC-002-005 | Full |
| Action follow-up | FR-QC-002-005 | Full |
| Reporting | FR-QC-002-006 | Full |

---

## FR to AC Mapping

### FR-QC-002-001: AMDEC Project Creation

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Project created with unique ID | UT-QC-002-001-001 | UAT-QC-002-01 |
| AC-02 | Project name is required | - | UAT-QC-002-01 |
| AC-03 | Project type defaults to 'process' | - | UAT-QC-002-01 |
| AC-04 | Status defaults to 'draft' | - | UAT-QC-002-01 |
| AC-05 | Creator recorded from session | - | UAT-QC-002-01 |

### FR-QC-002-002: Failure Mode Entry

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Failure mode created with unique ID | - | UAT-QC-002-02 |
| AC-02 | Item number is required | - | UAT-QC-002-02 |
| AC-03 | Potential failure mode is required | - | UAT-QC-002-02 |
| AC-04 | Potential effect is required | - | UAT-QC-002-02 |
| AC-05 | RPN defaults to 0 | - | UAT-QC-002-02 |
| AC-06 | Linked to correct project | - | UAT-QC-002-02 |

### FR-QC-002-003: Severity/Occurrence/Detection Ratings

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | Severity rating accepts 1-10 | - | UAT-QC-002-03 |
| AC-02 | Occurrence rating accepts 1-10 | - | UAT-QC-002-03 |
| AC-03 | Detection rating accepts 1-10 | - | UAT-QC-002-03 |
| AC-04 | Rating outside 1-10 rejected | - | - |
| AC-05 | RPN auto-calculated on rating change | - | UAT-QC-002-03 |

### FR-QC-002-004: RPN Calculation

| AC | Description | UT | UAT |
|----|-------------|-----|-----|
| AC-01 | RPN = S × O × D | UT-QC-002-001-001 | UAT-QC-002-04 |
| AC-02 | RPN minimum is 1 | - | - |
| AC-03 | RPN maximum is 1000 | - | - |
| AC-04 | Risk level correctly classified | UT-QC-002-001-001 | UAT-QC-002-04 |
| AC-05 | Action flag set when RPN ≥ threshold | - | UAT-QC-002-05 |

### FR-QC-002-005: Action Tracking

| AC | Description | UAT |
|----|-------------|-----|
| AC-01 | Action created with unique ID | UAT-QC-002-05 |
| AC-02 | Action linked to correct failure mode | UAT-QC-002-05 |
| AC-03 | Due date required and must be valid | UAT-QC-002-05 |
| AC-04 | Status transitions enforced | UAT-QC-002-06 |
| AC-05 | Completed actions have result | UAT-QC-002-06 |
| AC-06 | Overdue detection works | UAT-QC-002-10 |

### FR-QC-002-006: AMDEC Reporting

| AC | Description | UAT |
|----|-------------|-----|
| AC-01 | RPN summary calculated correctly | UAT-QC-002-07 |
| AC-02 | Risk distribution accurate | UAT-QC-002-08 |
| AC-03 | Action status counts correct | UAT-QC-002-07 |
| AC-04 | Worksheet contains all failure modes | UAT-QC-002-09 |
| AC-05 | PDF export generates | UAT-QC-002-09 |

---

## File Manifest

| File | Type | Description |
|------|------|-------------|
| `ARCH/ARCH-QC-002-amdec.md` | Architecture | System design, schema, components |
| `BR/BR-QC-002-amdec-fmea.md` | Business Requirement | High-level requirements |
| `FR/FR-QC-002-001.md` | Functional Requirement | AMDEC project creation |
| `FR/FR-QC-002-002.md` | Functional Requirement | Failure mode entry |
| `FR/FR-QC-002-003.md` | Functional Requirement | S/O/D ratings |
| `FR/FR-QC-002-004.md` | Functional Requirement | RPN calculation |
| `FR/FR-QC-002-005.md` | Functional Requirement | Action tracking |
| `FR/FR-QC-002-006.md` | Functional Requirement | Reporting |
| `UT/UT-QC-002-001-001.md` | Unit Test | RPN calculation test |
| `UAT/UAT-QC-002-amdec.md` | UAT Plan | User acceptance test cases |
| `RTM-QC-002-amdec.md` | This file | Traceability matrix |

---

## Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Business Analyst | | | |
| Quality Manager | | | |
| Developer | | | |
| Test Lead | | | |
