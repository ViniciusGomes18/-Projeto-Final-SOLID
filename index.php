<?php

require __DIR__ . '/vendor/autoload.php';

use App\Application\Controllers\EntryController;
use App\Application\Controllers\ExitController;
use App\Application\Controllers\ReportController;
use App\Application\Services\ParkingService;
use App\Application\Services\RateCalculator;
use App\Domain\Repositories\SQLiteParkingRepository;

// Bootstrap simples do back-end
$databaseConnection = require __DIR__ . '/Infra/Database/connection.php';

$parkingRepository = new SQLiteParkingRepository($databaseConnection);
$rateCalculator    = new RateCalculator();
$parkingService    = new ParkingService($parkingRepository, $rateCalculator);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Estacionamento Inteligente</title>
</head>
<body>
<h1>Controle de Estacionamento</h1>

<h2>Entrada</h2>
<form method="POST" action="?action=entry">
    Placa:
    <input name="plate" required>
    Tipo:
    <select name="type">
        <option value="carro">Carro</option>
        <option value="moto">Moto</option>
        <option value="caminhao">Caminhão</option>
    </select>
    <button type="submit">Registrar Entrada</button>
</form>

<h2>Saída</h2>
<form method="POST" action="?action=exit">
    Placa:
    <input name="plate" required>
    Horas (opcional):
    <input type="number" name="hours" min="1">
    <button type="submit">Registrar Saída</button>
</form>

<h2>Relatório</h2>
<a href="?action=report">Ver relatório (JSON)</a>

<hr>

<?php

$action = $_GET['action'] ?? null;

if ($action === null) {
    // Nenhuma ação, apenas mostra a tela
    exit;
}

/**
 * Entrada
 */
if ($action === 'entry') {
    try {
        $result = (new EntryController($parkingService))->handle();

        $plate       = $result['plate'] ?? '';
        $vehicleType = $result['type']  ?? '';

        echo "<p>O veículo do tipo <strong>{$vehicleType}</strong> com a placa <strong>{$plate}</strong> teve a entrada registrada.</p>";
    } catch (Exception $exception) {
        echo "<p style='color: red'>" . $exception->getMessage() . "</p>";
    }
}

/**
 * Saída
 */
if ($action === 'exit') {
    try {
        $result = (new ExitController($parkingService))->handle();

        $plate       = $result['plate']  ?? '';
        $vehicleType = $result['type']   ?? '';
        $amountToPay = $result['amount'] ?? 0.0;

        echo "<p>O veículo do tipo <strong>{$vehicleType}</strong> com a placa <strong>{$plate}</strong> teve a saída registrada.</p>";
        echo "<p>Total a pagar: R$ " . number_format((float) $amountToPay, 2, ',', '.') . "</p>";
    } catch (Exception $exception) {
        echo "<p style='color: red'>" . $exception->getMessage() . "</p>";
    }
}

/**
 * Relatório
 */
elseif ($action === 'report') {
    ob_start();
    (new ReportController($parkingService))->handle();
    $reportJson = ob_get_clean();

    $rawData = json_decode($reportJson, true);
    if (!is_array($rawData)) {
        $rawData = [];
    }

    $summary = [
        'carro' => ['label' => 'Carro', 'total' => 0, 'revenue' => 0.0],
        'moto' => ['label' => 'Moto', 'total' => 0, 'revenue' => 0.0],
        'caminhao' => ['label' => 'Caminhão', 'total' => 0, 'revenue' => 0.0],
    ];

    foreach ($rawData as $item) {
        $type = strtolower($item['type'] ?? '');
        if (!isset($summary[$type])) {
            continue;
        }

        $summary[$type]['total'] = (int) ($item['total'] ?? 0);
        $summary[$type]['revenue'] = (float) ($item['revenue'] ?? 0);
    }

    echo '<h2>Relatório de Faturamento</h2>';

    echo "<table border='1' cellpadding='8' cellspacing='0'>
            <tr>
                <th>Tipo</th>
                <th>Total</th>
                <th>Faturamento (R$)</th>
            </tr>";

    foreach ($summary as $row) {
        echo '<tr>
                <td>' . $row['label'] . '</td>
                <td>' . $row['total'] . '</td>
                <td>' . number_format($row['revenue'], 2, ',', '.') . '</td>
              </tr>';
    }

    echo '</table>';

    $totalRevenue = array_sum(array_column($summary, 'revenue'));
    echo '<p>Total geral faturado: R$ ' . number_format($totalRevenue, 2, ',', '.') . '</p>';
}

?>
</body>
</html>
