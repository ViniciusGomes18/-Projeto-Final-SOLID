<?php

namespace App\Application\Services;

use App\Domain\Enums\VehicleType;
use DateTimeInterface;

class RateCalculator implements RateCalculatorInterface
{
    public function calculate(
        VehicleType $type,
        DateTimeInterface $entryAt,
        DateTimeInterface $exitAt,
        ?int $overrideHours = null
    ): float {
        $hours = $overrideHours !== null && $overrideHours >= 1
            ? $overrideHours
            : max(1, (int) ceil(($exitAt->getTimestamp() - $entryAt->getTimestamp()) / 3600));

        return $type->hourlyRate() * $hours;
    }
}
