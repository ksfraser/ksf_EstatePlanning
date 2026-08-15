<?php
// src/ValueObjects/Jurisdiction.php
declare(strict_types=1);

namespace ksfraser\EstatePlanning\ValueObjects;

use InvalidArgumentException;

final class Jurisdiction
{
    private string $country;
    private string $province;

    public function __construct(string $jurisdiction)
    {
        if (strpos($jurisdiction, '-') === false) {
            throw new InvalidArgumentException('Jurisdiction must be in format COUNTRY-PROVINCE');
        }
        [$country, $province] = explode('-', $jurisdiction, 2);
        
        if (strlen($country) !== 2 || strlen($province) !== 2) {
            throw new InvalidArgumentException('Invalid jurisdiction format');
        }
        
        $this->country = strtoupper($country);
        $this->province = strtoupper($province);
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function getCode(): string
    {
        return $this->country . '-' . $this->province;
    }
}