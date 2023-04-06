<?php
ini_set("display_errors", 1);
header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: POST");
header ("Content-type: application/json; charset=UTF-8"); 

include_once("../../database/database.php");
include_once("../../models/Users.php");

$db = new Database();
$connection = $db->connect();
$userDetails = new Users ($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->fullName) && !empty($data->email) &&!empty($data->position) && !empty($data->password) && !empty($data->profilePicture)){

        $userObj = new Users($connection); // create a new instance of Users class
        $userObj->fullName = $data->fullName;
        $userObj->email = $data->email;
        $userObj->position = $data->position;

        //convert to hashhh
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        $userObj->password = $hashedPassword;
        $userObj->profilePicture = $data->profilePicture;

        $emailData = $userObj->ifExist();
        if(!empty($emailData)){
            http_response_code(500);
            echo json_encode(array(
                "status" => "failed",
                "message" => "Email is already used"
            ));
        }else{

            if($userObj->registerUser()){
                $userObj->addLogRegister();
                http_response_code(201);
                echo json_encode(array(
                    "status" => "success",
                    "message" => "User registered successfully"
                ));
            }else{
                http_response_code(500);
                echo json_encode(array(
                    "status" => "failed",
                    "message" => "Unable to register user"
                ));
            }
        }

    }else{
        http_response_code(500);
        echo json_encode(array(
            "status" => "failed",
            "message" => "All fields must have"
        ));
    }
}else{
    http_response_code(500);
    echo json_encode(array(
        "status" => "failed",
        "message" => "Access Denied"
    ));
}
?>