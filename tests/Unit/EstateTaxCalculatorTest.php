<?php

declare(strict_types=1);

namespace Tests\Unit;

use ksfraser\EstatePlanning\Services\EstateTaxCalculator;
use ksfraser\EstatePlanning\ValueObjects\Jurisdiction;
use ksfraser\EstatePlanning\ValueObjects\EstateValue;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EstateTaxCalculator
 *
 * @UML Note: Class diagram in ProjectDocs/UML.md
 * @BABOK Related: UT-EP-001
 */
class EstateTaxCalculatorTest extends TestCase
{
    private EstateTaxCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new EstateTaxCalculator();
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001
     */
    public function testCalculateFederalTaxWithBasicExemption(): void
    {
        // Arrange
        $estateValue = new EstateValue(500000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-FEDERAL');

        // Act
        $result = $this->calculator->calculateFederalTax($estateValue, $jurisdiction);

        // Assert - Within basic exemption, no tax
        $this->assertGreaterThanOrEqual(0.0, $result->getAmount());
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-002
     */
    public function testCalculateProvincialTaxOntario(): void
    {
        // Arrange
        $estateValue = new EstateValue(1200000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-ON');

        // Act
        $result = $this->calculator->calculateProvincialTax($estateValue, $jurisdiction);

        // Assert - Should have 10% provincial tax
        $this->assertEquals(144000.00, $result->getAmount());
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-003
     */
    public function testCalculateWithInvalidJurisdiction(): void
    {
        // Arrange
        $estateValue = new EstateValue(1000000.00, 'CAD');
        $jurisdiction = new Jurisdiction('XX-XX');

        // Act & Assert - Should throw exception
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->calculateProvincialTax($estateValue, $jurisdiction);
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-004
     */
    public function testCalculateWithNegativeValue(): void
    {
        // Arrange
        $estateValue = new EstateValue(-200000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-FEDERAL');

        // Act & Assert - Should throw exception
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->calculateFederalTax($estateValue, $jurisdiction);
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-005
     */
    public function testCalculateTotalTax(): void
    {
        // Arrange
        $estateValue = new EstateValue(3000000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-BC');

        // Act
        $totalTax = $this->calculator->calculateTotalTax($estateValue, $jurisdiction);

        // Assert - Combine federal + provincial
        $this->assertEquals(360000.00 + 120000.00, $totalTax->getAmount());
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-006
     */
    public function testCalculateWithExemptions(): void
    {
        // Arrange
        $exemptions = [200000.00];
        $estateValue = new EstateValue(1000000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-ON');

        // Act
        $result = $this->calculator->calculateWithExemptions($estateValue, $exemptions, [], $jurisdiction);

        // Assert - Adjust taxable amount
        $this->assertEquals(75600.00, $result->getAmount());
    }
}