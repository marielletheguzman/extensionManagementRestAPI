<?php
// ini_set("display_errors", 1);
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

$headers = getallheaders();

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    try{
        $jwt = $headers['Authorization'];
        $secretKey = "labanLang";
        $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
    

    $pendings = $adminObj->listOfPendingAccounts();
    
    if($pendings->num_rows > 0){
    $pendings_array = array();
        while($row = $pendings->fetch_assoc()){
            $pendings_array[] = array(
                "fullName" => $row['fullName'],
                "email" => $row['email'],
                "position" => $row['position'],
                "profilePicture" => $row['profilePicture'],
                "created_at" => $row['created_at'],
            );
    }
        http_response_code(200);
        echo json_encode (array(
            "status"=>"Success",
            "pending"=> $pendings_array
        ));
        }
    }
    catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array(
            "status" => "Failed",
            "message" =>"Authorization Failed"
        ));
        }
    }else{
        http_response_code(500);
        echo json_encode(array(
            "status" => "Failed",
            "message" =>"Access Denied"
        ));
    }
    
