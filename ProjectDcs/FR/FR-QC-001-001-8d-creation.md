# FR-QC-001-001: Create 8D Quality Report

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

The system SHALL allow authorized users to create a new 8D quality report with initial problem description and team assignment.

### Preconditions

1. User has Quality Manager or higher permission
2. Part/stock item identified (if applicable)

### Postconditions

1. 8D record created with status 'Open'
2. Team members assigned
3. Problem description captured

### Acceptance Criteria

| ID | Criteria | Test Scenario |
|----|----------|---------------|
| AC-01 | 8D created with unique ID | UT-QC-001-001-001 |
| AC-02 | Status defaults to 'Open' | UT-QC-001-001-002 |
| AC-03 | Team can be assigned | UT-QC-001-001-003 |
| AC-04 | Problem description required | UT-QC-001-001-004 |