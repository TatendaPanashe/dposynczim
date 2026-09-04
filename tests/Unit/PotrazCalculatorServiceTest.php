<?php

namespace Tests\Unit;

use App\Services\PotrazCalculatorService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PotrazCalculatorServiceTest extends TestCase
{
    #[DataProvider('tierBoundaries')]
    public function test_it_calculates_the_statutory_tier_and_fees(
        int $count,
        string $tier,
        float $registrationFee,
        float $applicationFee,
        float $total,
    ): void {
        $result = (new PotrazCalculatorService)->calculateTierAndFee($count);

        $this->assertSame($tier, $result['tier']);
        $this->assertSame($registrationFee, $result['registration_fee']);
        $this->assertSame($applicationFee, $result['application_fee']);
        $this->assertSame($total, $result['total_cost']);
    }

    public function test_it_rejects_counts_below_the_first_statutory_tier(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PotrazCalculatorService)->calculateTierAndFee(49);
    }

    /** @return array<string, array{int, string, float, float, float}> */
    public static function tierBoundaries(): array
    {
        return [
            'tier one minimum' => [50, 'Tier 1', 50.0, 0.0, 50.0],
            'tier one maximum' => [1_000, 'Tier 1', 50.0, 0.0, 50.0],
            'tier two minimum' => [1_001, 'Tier 2', 300.0, 30.0, 330.0],
            'tier two maximum' => [100_000, 'Tier 2', 300.0, 30.0, 330.0],
            'tier three minimum' => [100_001, 'Tier 3', 500.0, 30.0, 530.0],
            'tier three maximum' => [500_000, 'Tier 3', 500.0, 30.0, 530.0],
            'tier four minimum' => [500_001, 'Tier 4', 2_500.0, 30.0, 2_530.0],
        ];
    }
}
