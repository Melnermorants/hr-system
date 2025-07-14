<?php
ini_set('memory_limit', '1024M');
require '../config/setting.php';
require '../public/auth/jwt_helper.php';

$payload = validate_jwt_or_die(); 

date_default_timezone_set('Asia/Manila');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Only POST allowed"]);
    exit;
}

// Read JSON input (if needed later)
$input = json_decode(file_get_contents('php://input'), true);

$username = @$input['username'];
$password = @$input['password'];
$roles = @$input['role'];



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

if($password == ""){
	    $response = array(
    'status'=>'failed',
    'message'=>"Password is required"
    );

    header('Content-Type: application/json; charset=utf-8'); 
    header('Content-Type: application/json');
    echo  json_encode($response);
    die();
}


if($roles == ""){
	    $response = array(
    'status'=>'failed',
    'message'=>"roles is required"
    );

    header('Content-Type: application/json; charset=utf-8'); 
    header('Content-Type: application/json');
    echo  json_encode($response);
    die();
}

// Main query (with potential for pagination or filters)
$sql = "INSERT INTO users VALUES (id, :username, :pass,:roles )";

try {
    $sth = $conn->prepare($sql);
	$sth->bindvalue(':username', $username );
	$sth->bindvalue(':pass', $password );
	$sth->bindvalue(':roles', $roles );

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
