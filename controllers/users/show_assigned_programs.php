<?php
ini_set("display_errors", 1);
header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: GET");
header ("Content-type: application/json; charset=UTF-8"); 

include_once("../../database/database.php");
include_once("../../models/Users.php");

$db = new Database();
$connection = $db->connect();
$userDetails = new Users ($connection);

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $user_id = getUserIDFromToken();

    $headers = getallheaders();
    if(!empty($data->$user_id))
    
    try{
        $jwt = $headers['Authorization'];
        $secretKey = "labanLang";
        $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
            
        $userDetails->user_id =  $user_id;

        if($userDetails->viewPrograms()){
            http_response_code(200);
            echo json_encode(array(
                "status" => 1,
                "message" => "Program view"
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
}