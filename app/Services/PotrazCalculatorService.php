<?php

namespace App\Services;

use InvalidArgumentException;

final class PotrazCalculatorService
{
    /**
     * Calculate the DP1 licence tier under the S.I. 155 of 2024 fee schedule.
     *
     * Tier 1 starts at 50 data subjects. Tiers 2-4 include the additional
     * $30 application fee specified for those tiers.
     *
     * @return array{tier: string, registration_fee: float, application_fee: float, total_cost: float}
     */
    public function calculateTierAndFee(int $dataSubjectCount): array
    {
        if ($dataSubjectCount < 50) {
            throw new InvalidArgumentException('At least 50 data subjects are required for a DP1 licence tier.');
        }

        return match (true) {
            $dataSubjectCount <= 1_000 => $this->fees('Tier 1', 50.0, 0.0),
            $dataSubjectCount <= 100_000 => $this->fees('Tier 2', 300.0, 30.0),
            $dataSubjectCount <= 500_000 => $this->fees('Tier 3', 500.0, 30.0),
            default => $this->fees('Tier 4', 2_500.0, 30.0),
        };
    }

    /** @return array{tier: string, registration_fee: float, application_fee: float, total_cost: float} */
    private function fees(string $tier, float $registrationFee, float $applicationFee): array
    {
        return [
            'tier' => $tier,
            'registration_fee' => $registrationFee,
            'application_fee' => $applicationFee,
            'total_cost' => $registrationFee + $applicationFee,
        ];
    }
}
