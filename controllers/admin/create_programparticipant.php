<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type: application/json; charset=utf-8");


include_once("../../database/database.php");
include_once("../../models/Admin.php");



$db = new Database();
$connection = $db->connect();
$adminObj = new Admin($connection);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $headers = getallheaders();

    if(!empty($data->participant) && !empty($data->entity) && !empty($data->user_id)){
    
        try{
            $jwt = $headers['Authorization'];
            $secretKey = "labanLang";
            $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
            
            
            $adminObj->program_id = $data->program_id;
            $adminObj->participant = $data->participant;
            $adminObj->entity = $data->entity;
            $adminObj->user_id = $data->user_id;

            if($adminObj->createProgramParticipant()){
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Participant member has been added",
                    "user" => $adminObj->program_id
                ));
            }else{
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Failed to add participant"
                ));
            }
        }catch(Exception $ex){
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage()
                ));
        
        }
}else{
    http_response_code(404);
    echo json_encode(array(
        "status"=>0,
        "message" => "All data needed" ,
        "error" => var_dump($data)
    ));
}
}else{
    http_response_code(500);
    echo json_encode(array(
        "status"=>0,
        "message" => "Access Denied"
    ));
}
