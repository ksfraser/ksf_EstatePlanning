<?php
// src/ValueObjects/EstateValue.php
declare(strict_types=1);

namespace ksfraser\EstatePlanning\ValueObjects;

use InvalidArgumentException;

final class EstateValue
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'CAD')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Estate value cannot be negative');
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}