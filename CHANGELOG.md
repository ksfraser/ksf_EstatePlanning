# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-08-15

### Added
- Core estate planning business logic library (ksf_EstatePlanning)
  - EstateTaxCalculatorService with federal/provincial calculations
  - ValueObjects for EstateValue and Jurisdiction
  - BeneficiaryAnalysisEngine for allocation validation
  - Complete PSR-4 autoloading with strict types

- FrontAccounting integration module (ksf_FA_EstatePlanning)
  - Class-based hooks pattern (FA 2.4 compatible)
  - Security sections and access areas
  - Database tables with 0_ prefix convention
  - Gzip-compressed _init/config

- WordPress plugin (ksf_WP_EstatePlanning)
  - REST API endpoints for tax calculation
  - Custom database tables via dbDelta
  - Shortcodes for embedding calculators
  - Gutenberg block registration

- Comprehensive test suite
  - Unit tests with 100% coverage target
  - Integration tests for cross-module consistency
  - BABOK-aligned requirements traceability

- Documentation
  - BABOK business analysis documentation
  - RTM requirements traceability matrix
  - UML class and sequence diagrams
  - README with installation and API docs

### Security
- Strict type declarations on all PHP files
- Input validation on all public methods
- FA security sections properly configured
- WP nonce verification on REST endpoints

### Compliance
- PHP 7.3+ compatible (no 8.x features)
- PSR-12 coding standard
- AGENTS.md development guidelines followed

## [Unreleased]

### Planned
- Multi-jurisdiction tax rate API integration
- PDF report generation for scenarios
- CRM integration hooks
- Advanced trust modeling