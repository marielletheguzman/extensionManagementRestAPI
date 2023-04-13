<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
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

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    $headers = getallheaders();


    if(!empty($data->partnerName)&& !empty($data->partnerAddress) && !empty($data->partnerContactPerson) && !empty($data->partnerContactNumber)&& !empty($data->partnerLogo)&& !empty($data->partnerMoaFile)&& !empty($data->partnerStartDate)&& !empty($data->partnerEndDate)){

        try{
            $jwt = $headers['Authorization'];
            $secretKey = "bawiAko";
            $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));

            $adminObj->partnerName = $data->partnerName;
            $adminObj->partnerAddress = $data->partnerAddress;
            $adminObj->partnerContactPerson = $data->partnerContactPerson;
            $adminObj->partnerContactNumber = $data->partnerContactNumber;
            $adminObj->partnerLogo = $data->partnerLogo;
            $adminObj->partnerMoaFile = $data->partnerMoaFile;
            $adminObj->partnerStartDate = $data->partnerStartDate;
            $adminObj->partnerEndDate = $data->partnerEndDate;

            if($adminObj->createExtensionPartner()){
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Extension partner has been added"
                ));
            }else{
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Failed to add extension"
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