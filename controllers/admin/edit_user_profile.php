<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
header ("Content-type: application/json; charset=UTF-8"); 

include_once("../../database/database.php");
include_once("../../models/Admin.php");

$db = new Database();
$connection = $db->connect();
$adminDetails = new Admin ($connection);

if($_SERVER['REQUEST_METHOD'] === 'PUT'){

    $data = json_decode(file_get_contents("php://input"));
    if(!empty($data->fullName) && !empty($data->email) &&!empty($data->position) && !empty($data->password) && !empty($data->profilePicture)&& !empty($data->id)){
    
        try{

            $headers = getallheaders();
            $jwt = $headers['Authorization'];
            $secretKey = "labanLang";
            $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
            
            $adminDetails->id = $data->id;
            $adminDetails->fullName = $data->fullName;
            $adminDetails->email = $data->email;
            $adminDetails->position = $data->position;

            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
            $adminDetails->password = $hashedPassword;
            $adminDetails->profilePicture = $data->profilePicture;
     
            if($adminDetails->editUserProfile()){
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Profile has been updated!",
                    "data" => $decodedData
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