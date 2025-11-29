<?php

namespace App\Domain\Entities;

use App\Domain\Enums\VehicleType;
use DateTime;

class ParkingRecord
{
    public function __construct(
        private ?int $id,
        private string $plate,
        private VehicleType $type,
        private DateTime $entryAt,
        private ?DateTime $exitAt = null,
        private ?float $amount = null
    ) {}

    public function id(): ?int
    {
        return $this->id;
    }

    public function plate(): string
    {
        return $this->plate;
    }

    public function type(): VehicleType
    {
        return $this->type;
    }

    public function entryAt(): DateTime
    {
        return $this->entryAt;
    }

    public function exitAt(): ?DateTime
    {
        return $this->exitAt;
    }

    public function amount(): ?float
    {
        return $this->amount;
    }

    public function close(DateTime $exitAt, float $amount): void
    {
        $this->exitAt = $exitAt;
        $this->amount = $amount;
    }
}
