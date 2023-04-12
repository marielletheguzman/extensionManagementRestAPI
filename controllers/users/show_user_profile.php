<?php
    ini_set("display_errors", 1);
    require '../../vendor/autoload.php';
    use \Firebase\JWT\JWT;
    USE \Firebase\JWT\Key;

    header ("Access-Control-Allow-Origin: *"); 
    header ("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header ("Content-type: application/json; charset=UTF-8"); 

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
    include_once("../../models/Users.php");

    $db = new Database();
    $connection = $db->connect();
    $userDetails = new Users($connection);

    if ($_SERVER['REQUEST_METHOD'] === 'GET'){

        $headers = getallheaders();

        try {
            $jwt = $headers['Authorization'];
            $secretKey = "labanLang";
            $decodedData = JWT::decode($jwt, new Key($secretKey, 'HS512'));
            $userIddd = $decodedData->data->id;

                $userDetails->id =$userIddd;
                $userInfo = $userDetails->getUserProfile();

        
                if($userInfo->num_rows > 0){
                    $getProfilearr = array();
                        while($row = $userInfo->fetch_assoc()){        
                            http_response_code(200);
                            echo json_encode (array(
                                "fullName" => $row['fullName'],
                                "email" => $row['email'],
                                "position" => $row['position'],    
                                "profilePicture" => $row['profilePicture'],    
                            ));
                    
                        
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "ID parameter not found in request"
                ));
            }
        } catch(Exception $ex){
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => $ex->getMessage()
            ));
        }
    
    }