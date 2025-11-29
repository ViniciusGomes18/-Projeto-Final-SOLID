<?php

namespace App\Application\Services;

use App\Domain\Enums\VehicleType;
use DateTimeInterface;

interface RateCalculatorInterface
{
    public function calculate(
        VehicleType $type,
        DateTimeInterface $entryAt,
        DateTimeInterface $exitAt,
        ?int $overrideHours = null
    ): float;
}
