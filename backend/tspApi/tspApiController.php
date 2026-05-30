<?php
require_once __DIR__ . "/tspApiService.php";

use App\Services\TspApiService;

$tspApiService = new TspApiService();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (($_GET["action"] ?? "") == "getTspInstanceCoords") {
        $instanceName = $_GET["instance"] ?? "";

        echo json_encode($tspApiService->getInstanceCoords($instanceName));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
}