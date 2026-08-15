<?php
// src/Services/EstateTaxCalculatorService.php
declare(strict_types=1);

namespace ksfraser\EstatePlanning\Services;

use ksfraser\EstatePlanning\ValueObjects\Jurisdiction;
use ksfraser\EstatePlanning\ValueObjects\EstateValue;
use InvalidArgumentException;

class EstateTaxCalculatorService
{
    private const FEDERAL_EXEMPTION = 500000.00;
    private const PROVINCIAL_RATES = [
        'ON' => 0.06, // Ontario
        'BC' => 0.04, // British Columbia
        'AB' => 0.03, // Alberta
        'QC' => 0.08, // Quebec
        'DEFAULT' => 0.05,
    ];

    public function calculateFederalTax(EstateValue $value, Jurisdiction $jurisdiction): EstateValue
    {
        $threshold = self::FEDERAL_EXEMPTION;
        if ($value->getAmount() <= $threshold) {
            return new EstateValue(0.00, $value->getCurrency());
        }

        $taxable = $value->getAmount() - $threshold;
        $tax = $taxable * 0.3; // Simplified 30% federal rate
        return new EstateValue(round($tax, 2), $value->getCurrency());
    }

    public function calculateProvincialTax(EstateValue $value, Jurisdiction $jurisdiction): EstateValue
    {
        $rate = self::PROVINCIAL_RATES[$jurisdiction->getCode()] ?? self::PROVINCIAL_RATES['DEFAULT'];
        $tax = $value->getAmount() * $rate;
        return new EstateValue(round($tax, 2), $value->getCurrency());
    }

    public function calculateTotalTax(EstateValue $value, Jurisdiction $jurisdiction): EstateValue
    {
        $federal = $this->calculateFederalTax($value, $jurisdiction);
        $provincial = $this->calculateProvincialTax($value, $jurisdiction);

        return new EstateValue(round($federal->getAmount() + $provincial->getAmount(), 2), $value->getCurrency());
    }

    public function calculateWithExemptions(EstateValue $value, array $exemptions, array $credits, Jurisdiction $jurisdiction): EstateValue
    {
        $adjustedValue = new EstateValue($value->getAmount() - array_sum($exemptions), $value->getCurrency());
        $total = $this->calculateTotalTax($adjustedValue, $jurisdiction);
        $netTax = max(0.0, $total->getAmount() - array_sum($credits));
        return new EstateValue(round($netTax, 2), $value->getCurrency());
    }
}