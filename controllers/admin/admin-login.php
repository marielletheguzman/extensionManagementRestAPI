<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, authorization");
header("Content-type: application/json; charset=utf-8");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type,authorization');
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

    if(!empty($data->username) && !empty($data->password) ){
        $adminObj->username = $data->username;
        $adminObj->password = $data->password;

        $adminData = $adminObj->adminLogin();

            if(!empty($adminData)){
                $username = $adminData['username'];
                $password = $adminData['password'];

                if(password_verify($data->password, $password)){
                    
                $iss = "localhost";
                $iat = time();
                $nbf = $iat + 2;
                $exp = $iat + 10000000;
                $aud = "myusers";
                $adminArrData = array(
                    "username" =>  $adminData['username'],
                );

                //secretkey::::::::
                $secret_key = "labanLang";
                $payload_info = array(
                    "iss" => $iss,
                    "iat" => $iat,
                    "nbf" => $nbf,
                    "exp" => $exp,
                    "aud" => $aud,
                    "data" =>  $adminArrData
                );

                $jwt = JWT::encode($payload_info, $secret_key, 'HS512');

                http_response_code(200);
                echo json_encode(array(
                    "status"=>1,
                    "token" => $jwt,
                    "message" => "User logged in successfully!"
                ));

                }else{
                http_response_code(404);
                echo json_encode(array(
                    "status"=> 0,
                    "message" => "invalid credentials"
                ));
            }
        }else{
            http_response_code(404);
            echo json_encode(array(
                "status"=> 0,
                "message" => "invalid credentials"
            ));
        }



    }else{
        http_response_code(404);
        echo json_encode(array(
            "status" => 0,
            "message" => "All data needed!",
            "username" =>"empty"
        ));
    }
}else{
    http_response_code(500);
    echo json_encode(array(
        "status" => 0,
        "message" => "Access Denied!"
    ));
}
