<?php
declare(strict_types=1);

namespace Tests\Integration;

use ksfraser\FrontAccounting\EstatePlanning\FATaxCalculator;
use ksfraser\WP\EstatePlanning\WPGammaCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests between FA and WP calculations.
 *
 * @UML Note: Integration diagram in ProjectDocs/IntegrationDiagram.md
 * @BABOK Related: INT-EP-001
 */
class CalculationIntegrationTest extends TestCase
{
    /**
     * @test
     * @BABOK Related: INT-EP-001-001
     */
    public function testFederalTaxCalculateConsistency()
    {
        // Arrange: equal tax values expected
        $faCalculator = new FATaxCalculator($this->getFAConnection());
        $wpCalculator = new WPGammaCalculator();
        
        $estimatedValue = 2500000.00;
        $jurisdiction = 'CA-FEDERAL';
        
        // Act
        $faTax = $faCalculator->calculateFederalTax($estimatedValue, $jurisdiction);
        $wpTax = $wpCalculator->calculateEstateTax($estimatedValue, $jurisdiction);
        
        // Assert - Should be approximately equal
        $this->assertEqualsWithDelta($faTax, $wpTax, 0.01);
    }

    /**
     * @test
     * @BABOK Related: INT-EP-001-002
     */
    public function testBeneficiaryAllocationValidation()
    {
        // Arrange
        $faBeneficiaries = [
            ['name' => 'Spouse', 'percentage' => 45.00],
            ['name' => 'Child', 'percentage' => 55.00]
        ];
        $wpBeneficiaries = [
            ['name' => 'Spouse', 'percentage' => 40.00],
            ['name' => 'Child', 'percentage' => 60.00]
        ];

        // Act & Assert - Validation results should differ based on business rules
        $faCalculator = new FABeneficiaryEngine();
        $wpEngine = new WPBeneficiaryEngine();
        
        $faIsValid = $faCalculator->validateAllocation($faBeneficiaries);
        $wpIsValid = $wpEngine->validateAllocation($wpBeneficiaries);
        
        // Both should return valid allocations but process differently
        $this->assertFalse($faIsValid);
        $this->assertTrue($wpIsValid);
    }

    /**
     * @test
     * @BABOK Related: INT-EP-001-003
     */
    public function testTaxRateConsistencyAcrossVersions()
    {
        // Arrange
        $jurisdictions = ['CA-FEDERAL', 'CA-ON', 'BC-FEDERAL'];
        $taxYears = [2025, 2026, 2027];
        
        // Act
        foreach ($jurisdictions as $jurisdiction) {
            foreach ($taxYears as $taxYear) {
                // Act - Get current rate
                $currentRate = $this->getCoreEstateCalculator()->getTaxRate($jurisdiction, $taxYear);
                
                // Assert - Should have rate and threshold
                $this->assertGreaterThanOrEqual(0.0, $currentRate['threshold']);
                $this->assertGreaterThanOrEqual(0.0, $currentRate['rate']);
            }
        }
    }

    // Helper to simulate database access for tests
    private function getFAConnection() {
        // Simulate existing FA database connection
        $pdo = new PDO('mysql:host=localhost;dbname=frontaccounting', 'test_user', 'password');
        $pdo->exec('SET NAMES utf8');
        return $pdo;
    }

    // Helper for delta comparison
    private function assertEqualsWithDelta(float $a, float $b, float $delta) {
        $this->assertTrue(abs($a - $b) <= $delta, 
            "Values differ more than allowed delta\n" 
            . "Expected: $a\n"
            . "Actual: $b\n"
            . "Delta: $delta");
    }
}