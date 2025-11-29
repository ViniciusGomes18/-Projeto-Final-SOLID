<?php

require __DIR__ . "/vendor/autoload.php";

use App\Application\Controllers\EntryController;
use App\Application\Controllers\ExitController;
use App\Application\Controllers\ReportController;
use App\Application\Services\ParkingService;
use App\Application\Services\RateCalculator;
use App\Domain\Repositories\SQLiteParkingRepository;

$databaseConnection = require __DIR__ . "/Infra/Database/connection.php";

$parkingRepository = new SQLiteParkingRepository($databaseConnection);
$rateCalculator = new RateCalculator();
$parkingService = new ParkingService($parkingRepository, $rateCalculator);

$action = $_GET['action'] ?? '';

header("Content-Type: text/plain; charset=utf-8");

try {
    if ($action === 'entry') {
        (new EntryController($parkingService))->handle();
        echo "Entrada registrada com sucesso.";
        exit;
    }

    if ($action === 'exit') {
        $result = (new ExitController($parkingService))->handle();

        echo "O veículo do tipo {$result['type']} com a placa {$result['plate']} "
           . "teve a saída registrada. Valor: R$ "
           . number_format($result['amount'], 2, ',', '.');

        exit;
    }

    if ($action === 'report') {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($parkingService->report());
        exit;
    }

    echo "Ação inválida.";
} catch (Exception $exception) {
    http_response_code(400);
    echo $exception->getMessage();
}
