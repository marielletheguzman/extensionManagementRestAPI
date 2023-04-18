<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
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


    $WebsiteName = isset($_POST['WebsiteName']) ? $_POST['WebsiteName'] : null;
    $ThemeColor = isset($_POST['ThemeColor']) ? $_POST['ThemeColor'] : null;
    $Description = isset($_POST['Description']) ? $_POST['Description'] : null;


            if (empty($Description)||empty($WebsiteName)||empty($ThemeColor)) {
                http_response_code(400);
                echo json_encode(array('error' =>   $Description,$WebsiteName,$ThemeColor   ));
                exit();
            }

         
            $query = "UPDATE system_profile SET WebsiteName=?, ThemeColor=?, Description = ? WHERE id=1";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sss", $WebsiteName, $ThemeColor, $Description);
            if (!$stmt->execute()) {
                http_response_code(500);
                echo json_encode(array('error' => 'Internal Server Error'));
                exit();
            }
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Admin Profile has been updated!",
                    "AdminProfile" => array(
                        "Description" =>$Description,
                        "WebsiteName" =>$WebsiteName,
                        "ThemeColor" =>$ThemeColor,
                    )
                ));
   
        }  
       
}else{
    http_response_code(500);
    echo json_encode(array(
        "status"=>0,
        "message" => "Access Denied"
    ));
}