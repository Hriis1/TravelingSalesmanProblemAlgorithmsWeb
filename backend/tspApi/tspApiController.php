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
    if (($_POST["action"] ?? "") == "solveTsp") {
        $tspRequestBody = json_decode($_POST['tspRequestBody'] ?? '[]', true);


        if (!is_array($tspRequestBody)) {
            echo json_encode(["success" => false, "error" => "Invalid TSP request body"]);
            return;
        }

        echo json_encode($tspApiService->solveTsp($tspRequestBody));
    }
}