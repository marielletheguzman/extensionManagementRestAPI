<?php
 ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-type: application/json; charset=utf-8");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

include_once("../../database/database.php");
include_once("../../models/Admin.php");


$db = new Database();
$connection = $db->connect();
$adminObj = new Admin($connection);


if($_SERVER['REQUEST_METHOD'] === 'GET'){

    $getProfile = $adminObj->showAdminProfile();
    
    if($getProfile->num_rows > 0){
    $getProfilearr = array();
        while($row = $getProfile->fetch_assoc()){        
            http_response_code(200);
            echo json_encode (array(
                "Logo" => $row['Logo'],
                "WebsiteName" => $row['WebsiteName'],
                "ThemeColor" => $row['ThemeColor'],    
                "Description" => $row['Description'],    
                "MainImg" => $row['MainImg'],    
  
            ));
      

    }
        }
    
    }else{
        http_response_code(500);
        echo json_encode(array(
            "status" => "Failed",
            "message" =>"Access Denied"
        ));
    }
    
