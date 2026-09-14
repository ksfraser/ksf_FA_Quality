# BR-QC-002 - AMDEC/FMEA Analysis System

## Business Requirement

**Source**: WebErpMesv2 `quality_amdecs` table
**Module**: ksf_FA_Quality
**Status**: Proposed

### Problem Statement

Proactive quality planning requires Failure Mode, Effects and Criticality Analysis (AMDEC/FMEA)
to identify potential failure modes before they occur. The 8D system handles reactive problem
solving, but AMDEC enables preventive quality planning.

### Business Value

- **Proactive**: Identify failures before they happen
- **Risk Prioritization**: Focus resources on highest-risk issues
- **Design Improvement**: Improve product/process design
- **Cost Reduction**: Prevent costly field failures

### Scope

#### In Scope
1. AMDEC project creation (process or design scope)
2. Failure mode identification (what can go wrong)
3. Effect analysis (impact of each failure mode)
4. Severity rating (1-10 scale)
5. Occurrence rating (1-10 scale)
6. Detection rating (1-10 scale)
7. RPN calculation (S × O × D)
8. Action priority (high RPN → action required)
9. Recommended actions per failure mode
10.跟踪 (follow-up on actions taken)

#### Out of Scope
1. Automatic severity/occurrence lookup from historical data
2. Integration with CAD/process design tools
3. Statistical capability analysis (Cp/Cpk)

### Constraints

- PHP 7.3+ compatibility
- Must link to existing BOM/stock for process AMDEC

### Dependencies

- BR-QC-001 (8D system)
- ksf_FA_ProductLookup (for part identification)

### Related Requirements

- FR-QC-002-001: AMDEC project creation
- FR-QC-002-002: Failure mode entry
- FR-QC-002-003: Severity/Occurrence/Detection ratings
- FR-QC-002-004: RPN calculation
- FR-QC-002-005: Action tracking
- FR-QC-002-006: Reporting

### WebErpMesv2 Reference

See `database/migrations/2024_07_17_211835_create_quality_amdecs_table.php`
