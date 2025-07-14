<?php 

date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// set_time_limit(300);

$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
 header('Access-Control-Allow-Credentials: true');
 /* 
 THE CACHE IS TO PREVENT THE PREFLIGHT FROM EXECUTING EVERY REQUEST.
 WHEN ADDING CACHE AGE, THE PREFLIGHT ONLY EXECUTE AT FIRST TIME REQUEST
 */ 
 header('Access-Control-Max-Age: -1');

 header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
 header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
 
 if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");    
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
   
    
    header($protocol . ' 200 ' . "OK");  
    die();
    
        
}

header('Content-Type: application/json; charset=utf-8'); 
header('Content-Type: application/json');

// //https://crazywinapiv2.gamewizard.online/pangmanagengdb.php?
// $prodx = 'breddas.cdige2gqqe12.ap-southeast-1.rds.amazonaws.com';
// $prod = 'crazy2.cdige2gqqe12.ap-southeast-1.rds.amazonaws.com';

$config['db_host']="db";
$config['db_username']='user';
$config['db_password']='hrms2025';
$config['db_name']='hrms';
 
$conn = null;
// Maximum number of retry attempts
$maxAttempts = 3;

// Delay between retry attempts (in seconds)
$retryDelay = 5;

// Attempt to connect to the database with retry logic

    try {
    $conn = new PDO("mysql:host=".$config['db_host'].";dbname=".$config['db_name'], $config['db_username'], $config['db_password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));    // set the PDO error mode to exception
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
  } catch(PDOException $e) {
 
    $response = array(
        'status'=>'failed',
        'message'=>"Connection failed: " . $e->getMessage()
    );

    header('Content-Type: application/json; charset=utf-8'); 
    header('Content-Type: application/json');
    echo  json_encode($response);
    
  }
