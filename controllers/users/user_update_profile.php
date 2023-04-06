<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
header ("Content-type: application/json; charset=UTF-8"); 

include_once("../../database/database.php");
include_once("../../models/Users.php");

$db = new Database();
$connection = $db->connect();
$userDetails = new Users ($connection);

if($_SERVER['REQUEST_METHOD'] === 'PUT'){
    $data = json_decode(file_get_contents("php://input"));
    $headers = getallheaders();


    $jwt = $headers['Authorization'];
    $secretKey = "labanLang";
    $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
    $id = $decodedData->data->id;
    var_dump($data->fullName, $data->email, $data->position, $data->password, $data->profilePicture, $id);
    var_dump($id);
    if(!empty($data->fullName) && !empty($data->email) &&!empty($data->position) && !empty($data->password) && !empty($data->profilePicture)&& !empty($id)){
    
        try{
            $userDetails->id = $id;
            $userDetails->fullName = $data->fullName;
            $userDetails->email = $data->email;
            $userDetails->position = $data->position;

            $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
            $userDetails->password = $hashedPassword;
            $userDetails->profilePicture = $data->profilePicture;
     
            if($userDetails->updateProfile()){
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