<?php
require '../../config/setting.php';
// Set the API URL
$apiUrl = "https://date.nager.at/api/v3/publicholidays/2025/ph";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute request and close connection
$response = curl_exec($ch);
curl_close($ch);

// Decode the JSON response
$data = json_decode($response, true);

// Prepare the array for events
$ph_events = [];

if (is_array($data)) {
    foreach ($data as $event) {
        $ph_events[] = [
            "date" => $event['date'],
            "title" => $event['name'],
        ];
    }
}

// Output the result as JSON
// header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "code" => 200,
    "rows" => $ph_events
], JSON_PRETTY_PRINT);
