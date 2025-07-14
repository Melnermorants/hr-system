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

$username = @$input['username'];

if($username == ""){
	    $response = array(
    'status'=>'failed',
    'message'=>"Username is required"
    );

    header('Content-Type: application/json; charset=utf-8'); 
    header('Content-Type: application/json');
    echo  json_encode($response);
    die();
}

// Main query (with potential for pagination or filters)
$sql = "SELECT * FROM users WHERE username = :username";

try {
    $sth = $conn->prepare($sql);
	$sth->bindvalue(':username', $username );
    $sth->execute();
    $rows = $sth->fetchAll(PDO:: FETCH_ASSOC);

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
