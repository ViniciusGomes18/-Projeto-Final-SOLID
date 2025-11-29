<?php

namespace App\Application\Controllers;

use App\Application\Services\ParkingService;
use App\Domain\Enums\VehicleType;

class EntryController
{
    public function __construct(private ParkingService $service) {}

    public function handle(): void
    {
        $plate = $_POST['plate'] ?? '';
        $type  = $_POST['type'] ?? '';

        // Normalização para evitar problemas com maiúsculas/minúsculas e acentos.
        $normalizedType = strtolower(
            iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $type)
        );

        $vehicleType = match ($normalizedType) {
            'carro'    => VehicleType::CAR,
            'moto'     => VehicleType::MOTO,
            'caminhao' => VehicleType::TRUCK,
            default    => throw new \Exception("Tipo de veículo inválido."),
        };

        $this->service->enter($plate, $vehicleType);
    }
}
