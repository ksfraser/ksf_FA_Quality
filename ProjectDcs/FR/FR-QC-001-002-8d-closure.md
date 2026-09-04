# FR-QC-001-002: Close 8D Quality Report

## Version
1.0.0

## Author
KSF Development Team

## Created
2026-09-04

## Status
Approved

## Functional Requirement

### Related BR
BR-QC-001-quality-8d-system

### Description

The system SHALL allow closure of an 8D report only when all disciplines (D1-D8) are complete and validated corrective actions are implemented.

### Preconditions

1. 8D exists with status 'In Progress'
2. All D sections have content
3. Validation evidence attached (if required)

### Postconditions

1. 8D status set to 'Closed'
2. Closure date recorded
3. Team recognition recorded (D8)

### Acceptance Criteria

| ID | Criteria | Test Scenario |
|----|----------|---------------|
| AC-01 | Cannot close incomplete 8D | UT-QC-001-002-001 |
| AC-02 | Closure date recorded | UT-QC-001-002-002 |
| AC-03 | Closed 8D is read-only | UT-QC-001-002-003 |