# Requirements Traceability Matrix (RTM)

## Overview
This matrix traces requirements from BABOK analysis through implementation to test verification.

## Legend
- **BR** - Business Requirement
- **FR** - Functional Requirement  
- **UT** - Unit Test
- **INT** - Integration Test

---

## Traceability Matrix

| Req ID | Description | Module | Component | Test ID | Status |
|--------|-------------|--------|-----------|---------|--------|
| BR-EP-001 | Automated estate tax calculation | ksf_EstatePlanning | EstateTaxCalculatorService | UT-EP-001-001 | ✅ |
| BR-EP-002 | Multi-jurisdiction support | ksf_EstatePlanning | Jurisdiction ValueObject | UT-EP-001-003 | ✅ |
| BR-EP-003 | Exemption/credit handling | ksf_EstatePlanning | calculateWithExemptions() | UT-EP-001-006 | ✅ |
| BR-EP-004 | Beneficiary allocation validation | ksf_EstatePlanning | BeneficiaryAnalysisEngine | UT-EP-002-001 | ✅ |
| BR-EP-005 | FA module integration | ksf_FA_EstatePlanning | hooks.php / install.sql | UT-FA-001 | ✅ |
| BR-EP-006 | WP REST API | ksf_WP_EstatePlanning | EstateTaxController | UT-WP-001 | ✅ |
| BR-EP-007 | WP shortcodes/blocks | ksf_WP_EstatePlanning | Plugin::shortcodeCalculator() | UT-WP-002 | ✅ |

---

## Functional Requirements Detail

### FR-EP-001: Federal Tax Calculation
- **Acceptance**: Returns $0 for estates ≤ $500,000
- **Implementation**: `EstateTaxCalculatorService::calculateFederalTax()`
- **Tests**: UT-EP-001-001, UT-EP-001-002

### FR-EP-002: Provincial Tax Calculation
- **Acceptance**: Correct rate per province (ON: 6%, BC: 4%, AB: 3%, QC: 8%)
- **Implementation**: `EstateTaxCalculatorService::calculateProvincialTax()`
- **Tests**: UT-EP-001-003, UT-EP-001-004

### FR-EP-003: Total Tax Calculation
- **Acceptance**: Federal + Provincial combined
- **Implementation**: `EstateTaxCalculatorService::calculateTotalTax()`
- **Tests**: UT-EP-001-005

### FR-EP-004: Exemption Handling
- **Acceptance**: Reduces taxable estate before rate application
- **Implementation**: `EstateTaxCalculatorService::calculateWithExemptions()`
- **Tests**: UT-EP-001-006

### FR-EP-005: Beneficiary Validation
- **Acceptance**: Rejects allocations ≠ 100%
- **Implementation**: `BeneficiaryAnalysisEngine::validateDistribution()`
- **Tests**: UT-EP-002-001, UT-EP-002-002

### FR-EP-006: Contingent Beneficiaries
- **Acceptance**: Primary receives full, contingent receives on primary death
- **Implementation**: `BeneficiaryAnalysisEngine::calculateDistribution()`
- **Tests**: UT-EP-002-003, UT-EP-002-004

### FR-FA-001: FA Module Registration
- **Acceptance**: Module appears in FA Extensions, security areas registered
- **Implementation**: `hooks_ksf_FA_EstatePlanning::install_access()`
- **Tests**: UT-FA-001-001, UT-FA-001-003

### FR-FA-002: FA Database Schema
- **Acceptance**: Tables created with 0_ prefix, FK to 0_debtors
- **Implementation**: `sql/install.sql`
- **Tests**: INT-FA-001

### FR-WP-001: REST API Calculate Tax
- **Acceptance**: POST /ksf-ep/v1/calculate-tax returns correct JSON
- **Implementation**: `EstateTaxController::calculateTax()`
- **Tests**: UT-WP-001, INT-EP-001

### FR-WP-002: Scenario CRUD
- **Acceptance**: GET/PUT/DELETE /ksf-ep/v1/scenarios works
- **Implementation**: `EstatePlanningScenario` model + controller
- **Tests**: UT-WP-003, INT-WP-001

---

## Test Coverage Summary

| Module | Unit Tests | Integration Tests | Coverage Target |
|--------|------------|-------------------|-----------------|
| ksf_EstatePlanning | 12 | 3 | 100% |
| ksf_FA_EstatePlanning | 5 | 2 | 90% |
| ksf_WP_EstatePlanning | 6 | 2 | 90% |

---

## Verification Checklist

### Pre-Release
- [ ] All unit tests pass
- [ ] All integration tests pass
- [ ] Code coverage ≥ 90%
- [ ] PHPCS passes (PSR-12)
- [ ] PHPStan static analysis passes
- [ ] Security audit (no secrets in code)
- [ ] Dependency audit (composer audit)

### FA Module
- [ ] Gzip _init/config valid
- [ ] hooks.php class extends `hooks`
- [ ] Security sections registered
- [ ] Tables use 0_ prefix
- [ ] Lazy vendor autoload in hooks

### WP Module
- [ ] Plugin header valid
- [ ] REST endpoints registered
- [ ] Tables created via dbDelta
- [ ] Nonce verification on endpoints
- [ ] Capability checks on admin routes