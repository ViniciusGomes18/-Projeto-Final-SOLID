<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\ParkingRecord;

interface ParkingRepositoryInterface
{
    public function add(ParkingRecord $record): void;

    public function findActiveByPlate(string $plate): ?ParkingRecord;

    public function update(ParkingRecord $record): void;

    public function getReport(): array;
}
