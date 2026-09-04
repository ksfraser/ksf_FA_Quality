# UAT-QC-001: Quality 8D System - User Acceptance Test

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-04

## Status
Approved

## UAT Cases

### UAT-QC-001-01: Create 8D Report

| Field | Value |
|-------|-------|
| Purpose | Verify 8D can be created with basic info |
| Prereqs | Logged in as Quality Manager |
| Actor | Quality Engineer |
| Steps | 1. Navigate to Quality > 8D Reports<br>2. Click "New 8D"<br>3. Enter problem description<br>4. Add team member<br>5. Save |
| Expected | 8D created with unique ID, status Open |
| PASS Criteria | 8D visible in list, all data saved |

### UAT-QC-001-02: Complete D1-D3

| Field | Value |
|-------|-------|
| Purpose | Verify D1-D3 disciplines can be completed |
| Prereqs | 8D in Open status |
| Actor | Quality Engineer |
| Steps | 1. Open existing 8D<br>2. Add team members (D1)<br>3. Enter problem details (D2)<br>4. Add containment actions (D3)<br>5. Save |
| Expected | All D1-D3 data saved |
| PASS Criteria | Each discipline shows content, timestamps recorded |

### UAT-QC-001-03: Close Complete 8D

| Field | Value |
|-------|-------|
| Purpose | Verify complete 8D can be closed |
| Prereqs | 8D with all D1-D8 complete |
| Actor | Quality Manager |
| Steps | 1. Open complete 8D<br>2. Click "Close 8D"<br>3. Confirm closure |
| Expected | Status = Closed, closure date set |
| PASS Criteria | Status locked, cannot be edited |

### UAT-QC-001-04: Prevent Closing Incomplete 8D

| Field | Value |
|-------|-------|
| Purpose | Verify system prevents closing incomplete 8D |
| Prereqs | 8D missing D7 content |
| Actor | Quality Manager |
| Steps | 1. Open incomplete 8D<br>2. Click "Close 8D" |
| Expected | Error message, closure prevented |
| PASS Criteria | Status remains Open, error displayed |