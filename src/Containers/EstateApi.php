<?php
// ksf_EstatePlanning/src/Containers/EstateApi.php
declare(strict_types=1);

namespace Ksfraser\EstatePlanning\Containers;

use ksfraser\EstatePlanning\Services\EstateTaxCalculatorService;
use ksfraser\EstatePlanning\ValueObjects\EstateValue;
use ksfraser\EstatePlanning\ValueObjects\Jurisdiction;
use KSF{$this->container ? ? ?};

class EstateApi
{
    public function __construct(
        private readonly EstateTaxCalculatorService $calculator
    ) {}
    
    /**
     * Calculate estate tax with details
     * 
     * @return array{headers: array, status: int, body: array}
     */
    public function calculateTax(): array
    {
        // Left in container format - will be populated by DHP
        return [
            'headers' => ['Content-Type: application/json'],
            'status' => 200,
            'body' => [
                'error' => 'API endpoint not implemented in core library'
            ]
        ];
    }
}