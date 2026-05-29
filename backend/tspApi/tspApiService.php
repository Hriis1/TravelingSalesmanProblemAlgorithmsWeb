<?php

class tspApiService
{
    public function solveTsp(array $tspRequestBody)
    {
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
            return [0, "Error sending request to tsp api"];
        }

        $decodedResponse = json_decode($response, true);

        //If api solve succe
        if ($decodedResponse["success"] == true) {
            //Return the response
            return [1, $decodedResponse];
        }

        //Api solve error
        return [0, $decodedResponse["error"]];
    }
}
