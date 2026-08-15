<?php
// src/Containers/EstateApi.php
declare(strict_types=1);

namespace Ksfraser\EstatePlanning\Containers;

use Ksfraser\EstatePlanning\Services\EstateTaxCalculatorService;
use Ksfraser\EstatePlanning\ValueObjects\EstateValue;
use Ksfraser\EstatePlanning\ValueObjects\Jurisdiction;

class EstateApi
{
    private EstateTaxCalculatorService $calculator;

    public function __construct()
    {
        $this->calculator = new EstateTaxCalculatorService();
    }

    /**
     * Calculate estate tax with details
     * 
     * @return array{headers: array, status: int, body: array}
     */
    public function calculateTax(): array
    {
        // Placeholder - will be implemented in production
        return [
            'headers' => ['Content-Type: application/json'],
            'status' => 200,
            'body' => [
                'error' => 'API endpoint not implemented in core library'
            ]
        ];
    }
}