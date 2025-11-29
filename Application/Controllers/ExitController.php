<?php

namespace App\Application\Controllers;

use App\Application\Services\ParkingService;

class ExitController
{
    public function __construct(private ParkingService $service) {}

    public function handle(): array
    {
        $plate = $_POST['plate'] ?? '';

        $hours = isset($_POST['hours']) && $_POST['hours'] !== ''
            ? max(1, (int) $_POST['hours'])
            : null;

        return $this->service->exit($plate, $hours);
    }
}
