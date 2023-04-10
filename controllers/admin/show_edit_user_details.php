<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type: application/json; charset=UTF-8");
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
$adminDetails = new Admin($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $headers = getallheaders();
    if (!empty($data->id)) {
        try {
            $jwt = $headers['Authorization'];
            $secretKey = "labanLang";
            $decodedData = JWT::decode($jwt, new Key($secretKey, 'HS512'));

            $adminDetails->id = $data->id;
            $userDetails = $adminDetails->showUserProfileDetails();
            
            if ($userDetails) {
                $result = $userDetails->get_result(); // fetch the result set using get_result()
                if ($result->num_rows > 0) {
                    $userDetails = array();
                    while ($row = $result->fetch_assoc()) {
                        $userDetails[] = array(
                            "fullName" => $row['fullName'],
                            "email" => $row['email'],
                            "position" => $row['position'],
                            "profilePicture" => $row['profilePicture'],
                            "created_at" => $row['created_at'],
                        );
                    }
                    http_response_code(200);
                    echo json_encode(array(
                        "status" => "Success",
                        "userDetails" => $userDetails
                    ));
                } else {
                    http_response_code(404);
                    echo json_encode(array(
                        "status" => 0,
                        "message" => "User not found"
                    ));
                }
            } else {
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Failed to retrieve user details"
                ));
            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage()
            ));
        }
    } else {
        http_response_code(400);
        echo json_encode(array(
            "status" => 0,
            "message" => "Missing user ID"
        ));
    }
} else {
    http_response_code(405);
    echo json_encode(array(
        "status" => 0,
        "message" => "Method not allowed"
    ));
}