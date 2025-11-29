<?php

$pdo = new PDO("sqlite:" . __DIR__ . "/parking.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

return $pdo;
