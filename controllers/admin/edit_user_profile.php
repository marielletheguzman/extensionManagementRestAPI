<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: POST");
header ("Content-type: application/json; charset=UTF-8"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type, Authorization');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

include_once("../../database/database.php");
include_once("../../models/Admin.php");

$db = new Database();
$connection = $db->connect();
$adminDetails = new Admin ($connection);


// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = isset($_GET['id']) ? $_GET['id'] : null;

    // $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : null;
    // $email = isset($_POST['email']) ? $_POST['email'] : null;
    // $position = isset($_POST['position']) ? $_POST['position'] : null;

    $request_body = file_get_contents('php://input');

    // Decode the JSON data into a PHP object
    $data = json_decode($request_body);
    
    // Set the variables
    $fullName = isset($data->fullName) ? $data->fullName : null;
    $email = isset($data->email) ? $data->email : null;
    $position = isset($data->position) ? $data->position : null;

    
    if (empty($fullName)||empty($email)||empty($position)) {
        http_response_code(400);
        echo json_encode(array('error' =>   $fullName,$email,$id,$position  ));
        exit();
    }

    $query = "UPDATE users SET fullName = ?, email=?, position=? WHERE id=?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssi", $fullName, $email,  $position,$id);

    if ($stmt->execute()) {
        echo json_encode(array('goods' =>   $fullName,$email,$id,$position  ));
        exit();
    }
}