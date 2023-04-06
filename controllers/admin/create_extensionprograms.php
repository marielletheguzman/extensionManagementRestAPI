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


    if(!empty($data->programTitle) && !empty($data->programLead) && !empty($data->place) && !empty($data->additionalDetails) && !empty($data->partner) && !empty($data->startDate) && !empty($data->endDate)) {

    try{
        $jwt = $headers['Authorization'];
        $secretKey = "labanLang";
        $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));

        $adminObj->programTitle = $data->programTitle;
        $adminObj->programLead = $data->programLead;
        $adminObj->place = $data->place;
        $adminObj->additionalDetails = $data->additionalDetails;
        $adminObj->partner = $data->partner;
        $adminObj->startDate = $data->startDate;
        $adminObj->endDate = $data->endDate;

        if($adminObj->createExtensionProgram()){
            http_response_code(200);
            echo json_encode(array(
                "status" => 1,
                "message" => "Project has been created"
            ));
        }else{
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => "Failed to create project"
            ));
        }
    }
    
    catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array(
            "status" => 0,
            "message" => $ex->getMessage()
            ));
    }
}
    else{
        http_response_code(404);
        echo json_encode(array(
            "status"=>0,
            "message" => "All data needed" ,
            "error" => "sadasd"
        ));
    }
}else{
    http_response_code(500);
    echo json_encode(array(
        "status"=>0,
        "message" => "Access Denied"
    ));
}