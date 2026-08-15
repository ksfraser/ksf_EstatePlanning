# KSF Estate Planning Modules

## Overview
This repository contains three coordinated modules for estate planning functionality:
- **ksf_EstatePlanning** - Core business logic library (PSR-4 autoloaded)
- **ksf_FA_EstatePlanning** - FrontAccounting module integration
- **ksf_WP_EstatePlanning** - WordPress plugin with REST API and Gutenberg blocks

## Module Structure

### ksf_EstatePlanning (Business Logic)
```
ksf_EstatePlanning/
├── composer.json           # Packagist-ready library config
├── src/
│   ├── Services/           # Tax calculation services
│   ├── ValueObjects/       # Immutable value objects
│   └── Containers/         # API container classes
├── tests/
│   ├── Unit/               # Unit tests (100% coverage target)
│   └── Integration/        # Cross-module integration tests
├── sql/install.sql         # Core database schema
└── ProjectDocs/            # BABOK, RTM, UML documentation
```

### ksf_FA_EstatePlanning (FrontAccounting)
```
ksf_FA_EstatePlanning/
├── composer.json           # Depends on ksf_EstatePlanning
├── hooks.php               # FA module hooks (class-based pattern)
├── _init/config            # Gzip-compressed module config
├── sql/install.sql         # FA tables with 0_ prefix
├── src/                    # FA-specific integration classes
└── tests/                  # FA integration tests
```

### ksf_WP_EstatePlanning (WordPress)
```
ksf_WP_EstatePlanning/
├── ksf-estate-planning.php # Main plugin file
├── composer.json           # Depends on ksf_EstatePlanning
├── src/
│   ├── Plugin.php          # Main plugin class
│   ├── Endpoints/          # REST API controllers
│   └── Models/             # Data models
├── tests/                  # WP integration tests
└── build/                  # Compiled assets (Gutenberg blocks)
```

## Installation

### Composer (Business Logic)
```bash
composer require ksfraser/ksf-estateplanning
```

### FrontAccounting
1. Copy `ksf_FA_EstatePlanning` to `modules/`
2. Activate in FA Admin → Modules → Manage Extensions

### WordPress
1. Copy `ksf_WP_EstatePlanning` to `wp-content/plugins/`
2. Activate in WP Admin → Plugins

## API Endpoints (WordPress)

### Calculate Estate Tax
```http
POST /wp-json/ksf-ep/v1/calculate-tax
Content-Type: application/json

{
  "estate_value": 1500000,
  "province": "ON",
  "tax_year": 2026,
  "exemptions": [50000],
  "credits": [2000]
}
```

Response:
```json
{
  "success": true,
  "data": {
    "gross_estate": 1500000,
    "jurisdiction": "ON",
    "tax_year": 2026,
    "federal_tax": 300000,
    "provincial_tax": 90000,
    "total_tax": 390000,
    "effective_rate": 26.0
  }
}
```

### Shortcodes
- `[ksf_estate_calculator]` - Embed tax calculator
- `[ksf_estate_scenarios]` - List user scenarios

## Business Logic

### Tax Calculation Rules
| Jurisdiction | Federal Exemption | Rate |
|-------------|------------------|------|
| Federal     | $500,000         | 30%  |
| Ontario     | -                | 6%   |
| BC          | -                | 4%   |
| Alberta     | -                | 3%   |
| Quebec      | -                | 8%   |

### Beneficiary Allocation Rules
- Percentages must sum to 100%
- Contingent beneficiaries only receive if primary predeceases
- Validation occurs at scenario save

## Testing
```bash
# Run all tests
composer test

# Run specific module tests
cd ksf_EstatePlanning && ./vendor/bin/phpunit
cd ksf_FA_EstatePlanning && ./vendor/bin/phpunit
cd ksf_WP_EstatePlanning && ./vendor/bin/phpunit
```

## Release Process
1. Update version in all `composer.json` files
2. Tag release: `git tag -a v1.0.0 -m "Release v1.0.0"`
3. Push tags: `git push origin --tags`
4. GitHub Actions will build and publish to Packagist

## Dependencies
- PHP >= 7.3
- ext-json
- ksfraser/ksf-estate (for FA module)
- WordPress >= 5.8 (for WP module)

## Documentation
- `ProjectDocs/BABOK.md` - Business analysis
- `ProjectDocs/RTM.md` - Requirements traceability
- `ProjectDocs/UML.md` - Class/sequence diagrams