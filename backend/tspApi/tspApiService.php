<?php

namespace App\Services;
class TspApiService
{
    public function getInstanceCoords(string $instanceName): array
    {
        //Send req
        $ch = curl_init('http://localhost:8080/getTspInstanceCoords?instance=' . urlencode($instanceName));

        curl_setopt_array($ch, [
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            return ["success" => false, "error" => "Error sending request to tsp api"];
        }

        $decodedResponse = json_decode($response, true);

        return $decodedResponse;
    }

    public function solveTsp(array $tspRequestBody): array
    {
        //Send req
        $ch = curl_init('http://localhost:8080/solveTSP');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($tspRequestBody),
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            return ["success" => false, "error" => "Error sending request to tsp api"];
        }

        $decodedResponse = json_decode($response, true);

        return $decodedResponse;
    }
}
