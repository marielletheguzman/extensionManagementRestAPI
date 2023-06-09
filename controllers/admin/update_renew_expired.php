<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
header ("Content-type: application/json; charset=UTF-8"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type, Authorization');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

include_once("../../database/database.php");
include_once("../../models/Admin.php");

$db = new Database();
$connection = $db->connect();
$adminDetails = new Admin ($connection);

if($_SERVER['REQUEST_METHOD'] === 'PUT'){

    $data = json_decode(file_get_contents("php://input"));
    if(!empty($_GET['id'])) {
        $id = $_GET['id'];
    
        try{

            $headers = getallheaders();
            $jwt = $headers['Authorization'];
            $secretKey = "bawiAko";
            $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
            

            if(!empty($data->partnerStartDate) && !empty($data->partnerEndDate)){


                if (isset($_FILES['partnerMoaFile']) && !empty($_FILES['partnerMoaFile']['name'])) {
                    $fileName = $_FILES['partnerMoaFile']['name'];
                    $fileTmpPath = $_FILES['partnerMoaFile']['tmp_name'];
                    $fileSize = $_FILES['partnerMoaFile']['size'];
                    $fileType = $_FILES['partnerMoaFile']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));
                
                    // create a unique file name
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                
                    // move the uploaded file to a new location
                    $dest_path = '../../assets/extensionFiles/' . $newFileName;
                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $partnerMoaFile = $newFileName;
                    }
                }

                $adminDetails->id = $id;
                $adminDetails->partnerMoaFile = $partnerMoaFile;
                $adminDetails->partnerStartDate = $data->partnerStartDate;
                $adminDetails->partnerEndDate = $data->partnerEndDate;
                
            
                if($adminDetails->renewExpiredPartner()){
                    http_response_code(200);
                    echo json_encode(array(
                        "status" => 1,
                        "message" => $adminDetails,
                    ));
                }else{
                    http_response_code(500);
                    echo json_encode(array(
                        "status" => 0,
                        "message" => "Failed to update"
                    ));
                }
            }else{
                http_response_code(400);
                echo json_encode(array(
                    "status" => 0,
                    "message" =>   $adminDetails
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
            "message" => $id
            
        ));
    }
}else{
    http_response_code(500);
    echo json_encode(array(
        "status"=>0,
        "message" => "Access Denied",
        
    ));
}