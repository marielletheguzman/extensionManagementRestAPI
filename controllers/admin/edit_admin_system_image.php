<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
header ("Content-type: application/json; charset=UTF-8"); 
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
$adminDetails = new Admin ($connection);

if($_SERVER['REQUEST_METHOD'] === 'PUT'){

    $data = json_decode(file_get_contents("php://input"));
  
    
        try{

            $headers = getallheaders();
            $jwt = $headers['Authorization'];
            $secretKey = "bawiAko";
            $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
            
            $adminDetails->Description = $data->Description;
            $adminDetails->WebsiteName = $data->WebsiteName;
            $adminDetails->ThemeColor = $data->ThemeColor;

            $Logo = null;
            if (isset($_FILES['Logo']) && !empty($_FILES['Logo']['name'])) {
                $fileTmpPath = $_FILES['Logo']['tmp_name'];
                $fileName = $_FILES['Logo']['name'];
                $fileSize = $_FILES['Logo']['size'];
                $fileType = $_FILES['Logo']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
            
                // create a unique file name
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            
                // move the uploaded file to a new location
                $dest_path = '../../assets/extensionProfile/' . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                  $Logo = $newFileName;
                }
            

    
            $query = "UPDATE system_profile SET Logo = ?WHERE id=1";
            $stmt = connection->prepare($query);
            $stmt->bind_param("s", $Logo);
            if (!$stmt->execute()) {
                http_response_code(500);
                echo json_encode(array('error' => 'Internal Server Error'));
                exit();
            }
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "message" => "Admin Profile has been updated!",
                    "AdminProfile" => array(
                        "Description" =>$Description,
                        "WebsiteName" =>$WebsiteName,
                        "ThemeColor" =>$ThemeColor,
                        "Main" =>$MainImg,
                        "Logo" =>$Logo
                    )
                ));
            }else{
                http_response_code(500);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Failed to update"
                ));
            }
        }   
    }
    catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array(
            "status" => 0,
            "message" => $ex->getMessage()
            ));
    
    }
}