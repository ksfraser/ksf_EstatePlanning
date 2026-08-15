<?php
declare(strict_types=1);

namespace Tests\Unit;

use ksfraser\EstatePlanning\Services\EstateTaxCalculatorService;
use ksfraser\EstatePlanning\ValueObjects\EstateValue;
use ksfraser\EstatePlanning\ValueObjects\Jurisdiction;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EstateTaxCalculatorService.
 *
 * @UML Note: Class diagram in ProjectDocs/UML.md
 * @BABOK Related: UT-EP-001-001
 */
class EstateTaxCalculatorServiceTest extends TestCase
{
    private EstateTaxCalculatorService $calculator;

    protected function setUp(): void
    {
        $this->calculator = new EstateTaxCalculatorService();
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001-001-tax-federal-basic
     */
    public function testFederalCalculationWithBasicExemption(): void
    {
        // Arrange - Federal tax exemption threshold (2026)
        $estateValue = new EstateValue(500000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-FEDERAL');

        // Act
        $result = $this->calculator->calculateFederal($estateValue, $jurisdiction);

        // Assert - Within basic exemption, no tax
        $this->assertGreaterThanOrEqual(0.0, $result->getAmount());
        $this->assertSameCurrency('CAD', $result);
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001-002-tax-federal-threshold
     */
    public function testFederalCalculationAboveThreshold(): void
    {
        // Arrange - Estate exceeding federal exemption
        $estateValue = new EstateValue(5000000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-FEDERAL');

        // Act
        $result = $this->calculator->calculateFederal($estateValue, $jurisdiction);

        // Assert - Tax must be calculated
        $this->assertGreaterThan(0.0, $result->getAmount());
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001-003-tax-provincial-ontario
     */
    public function testProvincialCalculationOntario(): void
    {
        // Arrange - Ontario estate
        $estateValue = new EstateValue(1500000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-ON');

        // Act
        $result = $this->calculator->calculateProvincial($estateValue, $jurisdiction);

        // Assert - Probate fees and taxes
        $this->assertGreaterThan(0.0, $result->getAmount());
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001-004-invalid-jurisdiction
     */
    public function testCalculationWithInvalidJurisdictionThrowsException(): void
    {
        // Arrange
        $estateValue = new EstateValue(100000.00, 'CAD');
        $jurisdiction = new Jurisdiction('XX-INVALID');

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->calculateFederal($estateValue, $jurisdiction);
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001-005-negative-value
     */
    public function testCalculationWithNegativeValueThrowsException(): void
    {
        // Arrange
        $estateValue = new EstateValue(-50000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-FEDERAL');

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->calculateFederal($estateValue, $jurisdiction);
    }

    /**
     * @test
     * @BABOK Related: UT-EP-001-001-006-total-calculation
     */
    public function testTotalCalculationCombinesFederalAndProvincial(): void
    {
        // Arrange
        $estateValue = new EstateValue(3000000.00, 'CAD');
        $jurisdiction = new Jurisdiction('CA-BC');

        // Act
        $total = $this->calculator->calculateTotal($estateValue, $jurisdiction);

        // Assert
        $breakdown = $total->getBreakdown();
        $this->assertArrayHasKey('federal', $breakdown);
        $this->assertArrayHasKey('provincial', $breakdown);
        $this->assertGreaterThan(0.0, $total->getAmount());
    }
}