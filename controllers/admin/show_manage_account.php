<?php
 ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type,Authorization');
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

    

    $pendings = $adminObj->listOfManageAccount();
    $counts =0;
    if($pendings->num_rows > 0){
    $pendings_array = array();
        while($row = $pendings->fetch_assoc()){
            $counts++;
            $pendings_array[] = array(
                "id" => $row['id'],
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
            "counts"=> $counts,
            "pending"=> $pendings_array
        ));
        }
   
    }else{
        http_response_code(500);
        echo json_encode(array(
            "status" => "Failed",
            "message" =>"Access Denied"
        ));
    }
    
