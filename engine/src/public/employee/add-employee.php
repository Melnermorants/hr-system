<?php
ini_set('memory_limit', '1024M');
require '../../config/setting.php';
require '../../public/auth/jwt_helper.php';

//$payload = validate_jwt_or_die();

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

$fname = @$input['fname'];
$mname = @$input['mname'];
$lname = @$input['lname'];
$dob = @$input['dob'];
$nationality = @$input['nationality'];
$emailadd = @$input['emailadd'];
$gender = @$input['gender'];
$contact = @$input['contact'];

$jobTitle = @$input['jobTitle'];
$compensation = @$input['compensation'];
$dept = @$input['dept'];
$wEmail = @$input['wEmail'];
$empType = @$input['empType'];
$lmanager = @$input['lmanager'];
$wPosition = @$input['wPosition'];
$wLocation = @$input['wLocation'];

$bankName = @$input['bankName'];
$accName = @$input['accName'];
$accNum = @$input['accNum'];

$primaryAdd = @$input['primaryAdd'];
$country = @$input['country'];



function returnError($message)
{
    $response = array(
        'status' => 'failed',
        'message' => $message
    );
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    die();
}

// Validate required fields one by one
if ($fname == "") returnError("First name is required");
if ($lname == "") returnError("Last name is required");
if ($dob == "") returnError("Date of birth is required");
if ($nationality == "") returnError("Nationality is required");
if ($emailadd == "") returnError("Email address is required");
if ($gender == "") returnError("Gender is required");
if ($contact == "") returnError("Contact is required");

if ($jobTitle == "") returnError("Job title is required");
if ($compensation == "") returnError("Compensation is required");
if ($dept == "") returnError("Department is required");
if ($wEmail == "") returnError("Work email is required");
if ($empType == "") returnError("Employee type is required");
if ($lmanager == "") returnError("Line manager is required");
if ($wPosition == "") returnError("Work position is required");
if ($wLocation == "") returnError("Work location is required");

if ($bankName == "") returnError("Bank name is required");
if ($accName == "") returnError("Account name is required");
if ($accNum == "") returnError("Account number is required");

if ($primaryAdd == "") returnError("Primary address is required");
if ($country == "") returnError("Country is required");



// Main query (with potential for pagination or filters)
$sql1 = "SELECT id FROM users ORDER BY id DESC LIMIT 1;";


$sql2 = "INSERT INTO emp_tb (
        firstname, middlename, lastname, dob, nationality, email, gender, contact,
        job_title, compensation, dept, work_email, emp_type, line_manager, work_pos, work_loc,
        bank_name, acc_name, acc_number,
        primary_address, country,emp_id
    ) VALUES (
        :fname, :mname, :lname, :dob, :nationality, :emailadd, :gender, :contact,
        :jobTitle, :compensation, :dept, :wEmail, :empType, :lmanager, :wPosition, :wLocation,
        :bankName, :accName, :accNum,
        :primaryAdd, :country,:emp_id
    )";


$sql3 = "INSERT INTO users (username, password, emp_id, role) VALUES (:username, :password, :emp_id , :role)";

try {
    $sth1 = $conn->prepare($sql1);
    $sth1->execute();
    $id = $sth1->fetch(PDO::FETCH_ASSOC);
    echo $id["id"];
    $emp_id = "emp00" . str_pad($id["id"] + 1, 3, "0", STR_PAD_LEFT);
    $password = password_hash("company123", PASSWORD_DEFAULT);

    $sth = $conn->prepare($sql2);
    // Bind FIRST form group
    $sth->bindValue(':fname', $fname);
    $sth->bindValue(':mname', $mname);
    $sth->bindValue(':lname', $lname);
    $sth->bindValue(':dob', $dob);
    $sth->bindValue(':nationality', $nationality);
    $sth->bindValue(':emailadd', $emailadd);
    $sth->bindValue(':gender', $gender);
    $sth->bindValue(':contact', $contact);

    // Bind SECOND form group
    $sth->bindValue(':jobTitle', $jobTitle);
    $sth->bindValue(':compensation', $compensation);
    $sth->bindValue(':dept', $dept);
    $sth->bindValue(':wEmail', $wEmail);
    $sth->bindValue(':empType', $empType);
    $sth->bindValue(':lmanager', $lmanager);
    $sth->bindValue(':wPosition', $wPosition);
    $sth->bindValue(':wLocation', $wLocation);

    // Bind THIRD form group
    $sth->bindValue(':bankName', $bankName);
    $sth->bindValue(':accName', $accName);
    $sth->bindValue(':accNum', $accNum);

    // Bind FOURTH form group
    $sth->bindValue(':primaryAdd', $primaryAdd);
    $sth->bindValue(':country', $country);
    $sth->bindValue(':emp_id', $emp_id);


    $sth->execute();
    $rows = $sth->fetchAll(PDO::FETCH_ASSOC);


        $sth3 = $conn->prepare($sql3);
    // Bind FIRST form group
    $sth3->bindValue(':username', $emp_id);
    $sth3->bindValue(':password', $password);
    $sth3->bindValue(':emp_id', $emp_id);
    $sth3->bindValue(':role', "employee");
    $sth3->execute();
    $rows3 = $sth->fetchAll(PDO::FETCH_ASSOC);



    echo json_encode([
        "status" => "success",
        "code" => 200,
        "message"=>"Employee successfully inserted.",
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
