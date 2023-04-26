<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Credentials: true"); // if using cookies
header("Access-Control-Expose-Headers: Authorization"); // if returning the Authorization header in the response


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type, authorization'); // add "authorization" here
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}


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
include_once("../../models/Users.php");

$db = new Database();
$connection = $db->connect();
$userObj = new Users($connection);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));
    if (!empty($data->email) && !empty($data->password)) {
        $userObj->email = $data->email;
        $userObj->password = $data->password;
    
        $userObj = $userObj->loginUser();
    
        if (!empty($userObj)) {
            $email = $userObj['email'];
            $password = $userObj['password'];
    
            if (password_verify($data->password, $password)) {
    
                // Check if the user is approved or not
                if ($userObj['isApprove'] == 'Yes') {
                    $iss = "localhost";
                    $iat = time();
                    $nbf = $iat + 2;
                    $exp = $iat + 1000000;
                    $aud = "myusers";
                    $adminArrData = array(
                        "email" => $userObj['email'],
                        "id" => $userObj['id']
                    );
    
                    //secretkey::::::::
                    $secret_key = "bawiAko";
                    $payload_info = array(
                        "iss" => $iss,
                        "iat" => $iat,
                        "nbf" => $nbf,
                        "exp" => $exp,
                        "aud" => $aud,
                        "data" => $adminArrData
                    );
    
                    $jwt = JWT::encode($payload_info, $secret_key, 'HS512');
    
                    http_response_code(200);
                    echo json_encode(array(
                        "status" => 1,
                        "token" => $jwt,
                        "message" => "User logged in successfully!"
                    ));
                } else {
                    http_response_code(404);
                    echo json_encode(array(
                        "status" => 0,
                        "message" => "User is not yet approved"
                    ));
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Invalid credentials"
                ));
            }
        } else {
            http_response_code(404);
            echo json_encode(array(
                "status" => 0,
                "message" => "Invalid credentials"
            ));
        }
    } else {
        http_response_code(404);
        echo json_encode(array(
            "status" => 0,
            "message" => "All data needed!"
        ));
    }
}    