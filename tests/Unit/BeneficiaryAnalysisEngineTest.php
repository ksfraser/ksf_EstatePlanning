<?php

declare(strict_types=1);

namespace Tests\Unit;

use ksfraser\EstatePlanning\Services\BeneficiaryAnalysisEngine;
use ksfraser\EstatePlanning\Models\Beneficiary;
use ksfraser\EstatePlanning\Models\EstateDistribution;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for BeneficiaryAnalysisEngine
 *
 * @UML Note: Class diagram in ProjectDocs/UML.md
 * @BABOK Related: UT-EP-002
 */
class BeneficiaryAnalysisEngineTest extends TestCase
{
    private BeneficiaryAnalysisEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new BeneficiaryAnalysisEngine();
    }

    /**
     * @test
     * @BABOK Related: UT-EP-002-001
     */
    public function testValidateBeneficiaryPercentages(): void
    {
        // Arrange
        $beneficiaries = [
            new Beneficiary('Child 1', 50.00),
            new Beneficiary('Child 2', 50.00)
        ];

        // Act & Assert - Should be valid
        $this->assertTrue($this->engine->validateDistribution($beneficiaries));
    }

    /**
     * @test
     * @BABOK Related: UT-EP-002-002
     */
    public function testValidateOverAllocation(): void
    {
        // Arrange
        $beneficiaries = [
            new Beneficiary('Child 1', 60.00),
            new Beneficiary('Child 2', 60.00)
        ];

        // Act & Assert - Should be invalid
        $this->assertFalse($this->engine->validateDistribution($beneficiaries));
    }

    /**
     * @test
     * @BABOK Related: UT-EP-002-003
     */
    public function testCalculateDistribution(): void
    {
        // Arrange
        $beneficiaries = [
            new Beneficiary('Spouse', 40.00),
            new Beneficiary('Child 1', 30.00),
            new Beneficiary('Child 2', 30.00)
        ];
        $estateValue = 1000000.00;

        // Act
        $distribution = $this->engine->calculateDistribution($beneficiaries, $estateValue);

        // Assert
        $this->assertInstanceOf(EstateDistribution::class, $distribution);
        $this->assertCount(3, $distribution->getAllocations());
    }

    /**
     * @test
     * @BABOK Related: UT-EP-002-004
     */
    public function testContingentBeneficiaryAllocation(): void
    {
        // Arrange
        $beneficiaries = [
            new Beneficiary('Primary', 100.00, true),
            new Beneficiary('Contingent', 100.00, false)
        ];
        $estateValue = 500000.00;

        // Act
        $distribution = $this->engine->calculateDistribution($beneficiaries, $estateValue);

        // Assert - Primary gets full amount
        $primaryAllocation = $distribution->getAllocationByBeneficiary('Primary');
        $this->assertEquals(500000.00, $primaryAllocation);
    }

    /**
     * @test
     * @BABOK Related: UT-EP-002-005
     */
    public function testInvalidPercentageThrowsException(): void
    {
        // Arrange
        $beneficiaries = [
            new Beneficiary('Beneficiary 1', 150.00)
        ];
        $estateValue = 100000.00;

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->engine->calculateDistribution($beneficiaries, $estateValue);
    }
}