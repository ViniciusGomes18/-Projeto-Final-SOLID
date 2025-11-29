<?php

namespace App\Domain\Repositories;

use PDO;
use DateTime;
use App\Domain\Entities\ParkingRecord;
use App\Domain\Enums\VehicleType;

class SQLiteParkingRepository implements ParkingRepositoryInterface
{
    public function __construct(private PDO $databaseConnection) {}

    public function add(ParkingRecord $parkingRecord): void
    {
        $insertStatement = $this->databaseConnection->prepare(
            'INSERT INTO parking (plate, type, entry_at) VALUES (?, ?, ?)'
        );

        $insertStatement->execute([
            $parkingRecord->plate(),
            $parkingRecord->type()->value,
            $parkingRecord->entryAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function findActiveByPlate(string $plate): ?ParkingRecord
    {
        $selectStatement = $this->databaseConnection->prepare(
            'SELECT * FROM parking WHERE plate = ? AND exit_at IS NULL'
        );

        $selectStatement->execute([$plate]);

        $recordData = $selectStatement->fetch(PDO::FETCH_ASSOC);

        if (!$recordData) {
            return null;
        }

        return $this->mapRowToParkingRecord($recordData);
    }

    public function update(ParkingRecord $parkingRecord): void
    {
        $updateStatement = $this->databaseConnection->prepare(
            'UPDATE parking SET exit_at = ?, amount = ? WHERE id = ?'
        );

        $updateStatement->execute([
            $parkingRecord->exitAt()->format('Y-m-d H:i:s'),
            $parkingRecord->amount(),
            $parkingRecord->id(),
        ]);
    }

    public function getReport(): array
    {
        $reportStatement = $this->databaseConnection->query(
            'SELECT type, COUNT(*) AS total, SUM(amount) AS revenue
             FROM parking
             WHERE amount IS NOT NULL
             GROUP BY type'
        );

        return $reportStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function mapRowToParkingRecord(array $recordRow): ParkingRecord
    {
        return new ParkingRecord(
            id: $recordRow['id'],
            plate: $recordRow['plate'],
            type: VehicleType::from($recordRow['type']),
            entryAt: new DateTime($recordRow['entry_at']),
            exitAt: null,
            amount: null
        );
    }
}
