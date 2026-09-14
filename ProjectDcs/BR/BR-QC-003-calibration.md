# BR-QC-003 - Quality Control Device Calibration

## Business Requirement

**Source**: WebErpMesv2 `quality_control_devices` + calibration fields
**Module**: ksf_FA_Quality
**Status**: Proposed

### Problem Statement

Manufacturing quality depends on calibrated measurement equipment. Uncalibrated gauges
produce false pass/fail decisions. FA has no way to:
- Track measurement devices
- Manage calibration schedules
- Record calibration results
- Trigger alerts for overdue calibration

### Business Value

- **Quality Assurance**: Only calibrated tools used for inspections
- **Compliance**: ISO 9001, IATF 16949 require calibration records
- **Cost Control**: Catch out-of-spec tools before they cause quality issues
- **Audit Ready**: Complete calibration history

### Scope

#### In Scope
1. Device registry (gauge name, type, serial number, location)
2. Calibration interval (monthly, quarterly, annual)
3. Calibration history (date, result, next due, technician)
4. Measurement range and tolerance
5. Out-of-tolerance handling
6. Alert for upcoming/overdue calibration
7. Certificate attachment

#### Out of Scope
1. Automatic gauge data capture
2. SPC integration
3. Gauge R&R studies

### Dependencies

- ksf_FA_Quality (8D/AMDEC)
- ksf_FA_Documents (for certificate storage)

### Related Requirements

- FR-QC-003-001: Device registry
- FR-QC-003-002: Calibration scheduling
- FR-QC-003-003: Calibration recording
- FR-QC-003-004: Alert system
