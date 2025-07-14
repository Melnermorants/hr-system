<?php
ini_set('memory_limit', '1024M');
require '../config/setting.php';

date_default_timezone_set('Asia/Manila');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "code" => 403,
        "message" => "Forbidden: Only POST requests are allowed"
    ]);
    $conn = null;
    exit;
}

// Read JSON input (if needed later)
$input = json_decode(file_get_contents('php://input'), true);

// Main query (with potential for pagination or filters)
$sql = "SELECT * FROM users";

try {
    $sth = $conn->prepare($sql);
    $sth->execute();
    $rows = $sth->fetchAll(PDO:: FETCH_ASSOC);

    print_r($rows);

    $lastId = $conn->lastInsertId();

    echo $lastId. 'is the last id';

    echo json_encode([
        "status" => "success",
        "code" => 200,
        "data" => $rows
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "code" => 500,
        "message" => "Database error: " . $e->getMessage()
    ]);
} finally {
    $conn = null;
}
