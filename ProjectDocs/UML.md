# UML Documentation

## Class Diagram

```mermaid
classDiagram
    %% Core Business Logic
    class EstateTaxCalculatorService {
        +calculateFederalTax(EstateValue, Jurisdiction): EstateValue
        +calculateProvincialTax(EstateValue, Jurisdiction): EstateValue
        +calculateTotalTax(EstateValue, Jurisdiction): EstateValue
        +calculateWithExemptions(EstateValue, array, array, Jurisdiction): EstateValue
        -FEDERAL_EXEMPTION: float
        -PROVINCIAL_RATES: array
    }

    class BeneficiaryAnalysisEngine {
        +validateDistribution(array): bool
        +calculateDistribution(array, float): EstateDistribution
        -validatePercentages(array): bool
    }

    class EstateDistribution {
        -allocations: array
        +getAllocations(): array
        +getAllocationByBeneficiary(string): float
        +getTotal(): float
    }

    %% Value Objects
    class EstateValue {
        -amount: float
        -currency: string
        +__construct(float, string)
        +getAmount(): float
        +getCurrency(): string
    }

    class Jurisdiction {
        -country: string
        -province: string
        +__construct(string)
        +getCountry(): string
        +getProvince(): string
        +getCode(): string
    }

    %% FA Integration
    class hooks_ksf_FA_EstatePlanning {
        +module_name: string
        +version: string
        +install_tabs(application): void
        +install_access(): array
        +activate_extension(int, bool): bool
    }

    class EstatePlanningApp {
        +__construct()
    }

    %% WP Integration
    class Plugin {
        +activate(): void
        +deactivate(): void
        +init(): void
        +registerEndpoints(): void
        +registerBlocks(): void
        +enqueueAdminScripts(): void
        +shortcodeCalculator(array): string
        +shortcodeScenarios(array): string
        +renderCalculatorBlock(array, string): string
        -createTables(): void
    }

    class EstateTaxController {
        -calculator: EstateTaxCalculatorService
        +calculateTax(WP_REST_Request): WP_REST_Response
        +getScenarios(WP_REST_Request): WP_REST_Response
        +handleScenario(WP_REST_Request): WP_REST_Response
    }

    class EstatePlanningScenario {
        -table: string
        -beneficiariesTable: string
        +getByUser(int): array
        +getById(int): ?array
        +create(array): int
        +update(int, array): ?array
        +delete(int): bool
        -getBeneficiaries(int): array
    }

    %% Relationships
    EstateTaxCalculatorService --> EstateValue
    EstateTaxCalculatorService --> Jurisdiction
    BeneficiaryAnalysisEngine --> EstateDistribution
    BeneficiaryAnalysisEngine --> EstateValue
    
    Plugin --> EstateTaxController
    Plugin --> EstatePlanningScenario
    EstateTaxController --> EstateTaxCalculatorService
    
    hooks_ksf_FA_EstatePlanning --> EstatePlanningApp
```

## Sequence Diagrams

### Tax Calculation Flow (WordPress)

```mermaid
sequenceDiagram
    participant User
    participant WP_Frontend as WP Frontend (JS)
    participant WP_REST as WP REST API
    participant Controller as EstateTaxController
    participant Core as EstateTaxCalculatorService
    participant DB as Database

    User->>WP_Frontend: Enters estate value, province
    WP_Frontend->>WP_REST: POST /ksf-ep/v1/calculate-tax
    WP_REST->>Controller: calculateTax(request)
    Controller->>Core: calculateFederalTax()
    Core-->>Controller: EstateValue
    Controller->>Core: calculateProvincialTax()
    Core-->>Controller: EstateValue
    Controller->>Core: calculateTotalTax()
    Core-->>Controller: EstateValue
    Controller-->>WP_REST: WP_REST_Response
    WP_REST-->>WP_Frontend: JSON Response
    WP_Frontend-->>User: Display results
```

### Scenario Management Flow

```mermaid
sequenceDiagram
    participant Advisor as Advisor (FA/WP)
    participant API as API/Controller
    participant Model as EstatePlanningScenario
    participant DB as Database

    Advisor->>API: POST /scenarios (create)
    API->>Model: create(data)
    Model->>DB: INSERT scenario
    DB-->>Model: insert_id
    Model-->>API: scenario_id
    API-->>Advisor: Created scenario

    Advisor->>API: PUT /scenarios/{id} (update)
    API->>Model: update(id, data)
    Model->>DB: UPDATE scenario
    DB-->>Model: affected_rows
    Model-->>API: updated scenario
    API-->>Advisor: Updated scenario

    Advisor->>API: GET /scenarios/{id} (read)
    API->>Model: getById(id)
    Model->>DB: SELECT scenario + beneficiaries
    DB-->>Model: rows
    Model-->>API: scenario data
    API-->>Advisor: Scenario details

    Advisor->>API: DELETE /scenarios/{id}
    API->>Model: delete(id)
    Model->>DB: DELETE beneficiaries + scenario
    DB-->>Model: affected_rows
    Model-->>API: bool
    API-->>Advisor: Deleted
```

### FA Module Activation Flow

```mermaid
sequenceDiagram
    participant Admin as FA Admin
    participant FA_Core as FA Core
    participant Hooks as hooks_ksf_FA_EstatePlanning
    participant DB as Database

    Admin->>FA_Core: Activate ksf_FA_EstatePlanning
    FA_Core->>Hooks: activate_extension(company)
    Hooks->>DB: db_import('install.sql')
    DB-->>Hooks: Tables created
    Hooks-->>FA_Core: true
    FA_Core->>Hooks: install_tabs(app)
    Hooks->>FA_Core: app->add_application(EstatePlanningApp)
    FA_Core->>Hooks: install_access()
    Hooks-->>FA_Core: security_areas, security_sections
    FA_Core-->>Admin: Module activated, menu added
```

## Component Diagram

```mermaid
graph TB
    subgraph "Core Library (ksf_EstatePlanning)"
        direction TB
        Core[EstateTaxCalculatorService]
        Beneficiary[BeneficiaryAnalysisEngine]
        VOs[ValueObjects: EstateValue, Jurisdiction]
    end

    subgraph "FrontAccounting Module (ksf_FA_EstatePlanning)"
        direction TB
        FA_Hooks[hooks_ksf_FA_EstatePlanning]
        FA_App[EstatePlanningApp]
        FA_Tables[0_ksf_estateplanning_* tables]
    end

    subgraph "WordPress Plugin (ksf_WP_EstatePlanning)"
        direction TB
        WP_Plugin[Plugin]
        WP_Controller[EstateTaxController]
        WP_Model[EstatePlanningScenario]
        WP_Tables[wp_ksf_estate_* tables]
        WP_REST[REST API Endpoints]
        WP_Blocks[Gutenberg Blocks]
        WP_Shortcodes[Shortcodes]
    end

    Core -.->|PSR-4 autoload| FA_Hooks
    Core -.->|PSR-4 autoload| WP_Controller
    Beneficiary -.->|PSR-4 autoload| FA_Hooks
    Beneficiary -.->|PSR-4 autoload| WP_Controller
    VOs -.->|PSR-4 autoload| FA_Hooks
    VOs -.->|PSR-4 autoload| WP_Controller

    WP_Plugin --> WP_Controller
    WP_Plugin --> WP_Model
    WP_Plugin --> WP_REST
    WP_Plugin --> WP_Blocks
    WP_Plugin --> WP_Shortcodes
    WP_Controller --> Core
    WP_Model --> WP_Tables
    FA_Hooks --> FA_App
    FA_Hooks --> FA_Tables
```

## Deployment Diagram

```mermaid
graph TB
    subgraph "Development"
        Git[GitHub Repos]
        CI[GitHub Actions CI]
    end

    subgraph "Packaging"
        Packagist[Packagist.org]
    end

    subgraph "FrontAccounting Server"
        FA[FA Installation]
        FA_Modules[modules/ksf_FA_EstatePlanning]
        FA_DB[(FA Database)]
    end

    subgraph "WordPress Server"
        WP[WP Installation]
        WP_Plugins[plugins/ksf_WP_EstatePlanning]
        WP_DB[(WP Database)]
    end

    subgraph "Client Applications"
        External[External PHP Apps]
    end

    Git --> CI
    CI -->|Publish| Packagist
    Packagist -->|composer require| FA_Modules
    Packagist -->|composer require| WP_Plugins
    Packagist -->|composer require| External
    FA_Modules --> FA_DB
    WP_Plugins --> WP_DB
```

## Data Flow Diagram (Level 0)

```mermaid
graph LR
    User[User/Advisor] -->|Input| System[Estate Planning System]
    System -->|Tax Calculation| Core[Core Engine]
    Core -->|Results| System
    System -->|Store| FA_DB[(FA Database)]
    System -->|Store| WP_DB[(WP Database)]
    System -->|Output| User
    
    External[External Systems] -->|API| Core
    Core -->|JSON| External
```

## State Diagram - Estate Planning Scenario

```mermaid
stateDiagram-v2
    [*] --> Draft: Create scenario
    Draft --> Active: Advisor activates
    Active --> Completed: Client signs off
    Active --> Archived: Advisor archives
    Completed --> Archived: Time passes
    Archived --> [*]: Delete scenario
    
    Draft: Can edit all fields
    Active: Read-only for client, editable by advisor
    Completed: Read-only, generates reports
    Archived: Hidden from lists, recoverable
```

## Package Diagram

```mermaid
packageDiagram
    package "ksfraser\\EstatePlanning" {
        package Services {
            class EstateTaxCalculatorService
            class BeneficiaryAnalysisEngine
        }
        package ValueObjects {
            class EstateValue
            class Jurisdiction
        }
        package Containers {
            class EstateApi
        }
        package Models {
            class EstateDistribution
        }
    }

    package "ksfraser\\FrontAccounting\\EstatePlanning" {
        class hooks_ksf_FA_EstatePlanning
        class EstatePlanningApp
    }

    package "ksfraser\\WP\\EstatePlanning" {
        class Plugin
        class EstateTaxController
        class EstatePlanningScenario
    }

    "ksfraser\\FrontAccounting\\EstatePlanning" --> "ksfraser\\EstatePlanning"
    "ksfraser\\WP\\EstatePlanning" --> "ksfraser\\EstatePlanning"
```

---

*Generated for Estate Planning Modules v1.0.0*