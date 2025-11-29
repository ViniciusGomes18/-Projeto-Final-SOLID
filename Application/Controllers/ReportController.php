<?php

namespace App\Application\Controllers;

use App\Application\Services\ParkingService;

class ReportController
{
    public function __construct(private ParkingService $service) {}

    public function handle(): array
    {
        return $this->service->report();
    }
}
