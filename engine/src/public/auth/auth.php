<?php
ini_set('memory_limit', '1024M');
require '../../config/setting.php';
require_once 'jwt_helper.php';


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
$pass = @$input['password'];


if ($username == "") {
    $response = array(
        'status' => 'failed',
        'message' => "Username is required"
    );

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Type: application/json');
    echo  json_encode($response);
    die();
}

if ($pass == "") {
    $response = array(
        'status' => 'failed',
        'message' => "password is required"
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
    $sth->bindvalue(':username', $username);


    $sth->execute();
    $rows = $sth->fetchAll(PDO::FETCH_ASSOC);


    if (count($rows) === 1) {

        $fetch_pass = $rows[0]['password'];

        if (password_verify($pass, $fetch_pass)) {

            $payload = [
                'user' => $username,
                'role' => $rows[0]['role'],
                'iat' => time(),
                'exp' => time() + 3600 // expires in 1 hour
            ];
            $jwt = generate_jwt($payload);

            // $output = array();

            // foreach ($rows as $item) {

            //     $line = array(
            //         "username" => $item['username'],
            //         "role" => $item['role'],
            //         "emp_id" => $item['emp_id'],
            //         "token" => $jwt
            //     );

            //     $output = $line;
            // }


            echo json_encode([
                "status" => "success",
                "code" => 200,
                "data" => $jwt,
                "message" => "Successfully Login.",

            ], JSON_PRETTY_PRINT);
        } else {
            echo json_encode([
                "status" => "failed",
                "code" => 401,
                "message" => "Wrong username or password .",

            ], JSON_PRETTY_PRINT);
        }
    } else {
        echo json_encode([
            "status" => "failed",
            "code" => 401,
            "message" => "Wrong username or password .",

        ], JSON_PRETTY_PRINT);
    }
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
