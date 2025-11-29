<?php

namespace App\Application\Services;

use App\Domain\Entities\ParkingRecord;
use App\Domain\Repositories\ParkingRepositoryInterface;
use App\Domain\Enums\VehicleType;

class ParkingService
{
    public function __construct(
        private ParkingRepositoryInterface $repo,
        private RateCalculatorInterface $rates
    ){}

    public function enter(string $plate, VehicleType $type): void
    {
        $record = new ParkingRecord(
            id: null,
            plate: $plate,
            type: $type,
            entryAt: new \DateTime()
        );

        $this->repo->add($record);
    }

    public function exit(string $plate, ?int $hours = null): array
    {
        $record = $this->repo->findActiveByPlate($plate);

        if (!$record) {
            throw new \Exception("Veículo não está no estacionamento");
        }

        $exit = new \DateTime();

        $amount = $this->rates->calculate(
            $record->type(),
            $record->entryAt(),
            $exit,
            $hours
        );

        $record->close($exit, $amount);
        $this->repo->update($record);

        return [
            'plate'  => $record->plate(),
            'type'   => $record->type()->value,
            'amount' => $amount,
        ];
    }

    public function report(): array
    {
        return $this->repo->getReport();
    }
}
