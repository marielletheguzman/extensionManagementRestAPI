<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
header ("Content-type: application/json; charset=UTF-8"); 
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
$adminDetails = new Admin ($connection);

if($_SERVER['REQUEST_METHOD'] === 'PUT'){

    $data = json_decode(file_get_contents("php://input"));
    if( !empty($data->Logo) && !empty($data->WebsiteName)&& !empty($data->ThemeColor)){
    
        try{

            $headers = getallheaders();
            $jwt = $headers['Authorization'];
            $secretKey = "labanLang";
            $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
            
            $adminDetails->Logo = $data->Logo;
            $adminDetails->WebsiteName = $data->WebsiteName;
            $adminDetails->ThemeColor = $data->ThemeColor;

     
            if($adminDetails->editAdminProfile()){
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Admin Profile has been updated!",
                    "AdminProfile" => array(
                        "Logo" => $data->Logo,
                        "WebsiteName" => $data->WebsiteName,
                        "ThemeColor" => $data->ThemeColor
                    )
                ));
            }else{
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Failed to update"
                ));
            }
        }    catch(Exception $ex){
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage()
                ));
        }
    } else{
        http_response_code(404);
        echo json_encode(array(
            "status"=>0,
            "message" => "All data needed" ,
            
        ));
    }
}else{
    http_response_code(500);
    echo json_encode(array(
        "status"=>0,
        "message" => "Access Denied"
    ));
}