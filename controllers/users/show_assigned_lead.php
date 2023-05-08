
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
$userDetails = new Users ($connection);

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    
    $headers = getallheaders();

    $user_id = 1;
    try{
        $jwt = $headers['Authorization'];
        $secretKey = "bawiAko";
        $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
        $id = $decodedData->data->id;

        if(!empty($id))
        $userDetails->id =  $id;


        if($userDetails->viewProgramsLead()){
            $extProg = "SELECT * FROM extensionprograms WHERE lead_id = $id";
            $res = $connection->query($extProg);
        
            $extensionProgram = array();
            while($row = $res->fetch_assoc()) {
                $id = $row['id'];
                $programTitle = $row['programTitle'];
                $programLead = $row['programLead'];
                $place = $row['place'];
                $additionalDetails = $row['additionalDetails'];
                $partner = $row['partner'];
                $status = $row['status'];
                $startDate = $row['startDate'];
                $endDate = $row['endDate'];
                if (strtotime($endDate) < time()) {
                    $status = 'Expired';
                    $query = "UPDATE extensionprograms SET status = 'Expired' WHERE id = $id";
                    $connection->query($query);
                }else{
                    $status = 'Active';
                    $query = "UPDATE extensionprograms SET status = 'Active' WHERE id = $id";
                    $connection->query($query);
                }
                $extensionProgram[] = array(
                    "id" => $id,
                    "programTitle" => $programTitle,
                    "programLead" => $programLead,
                    "place" => $place,
                    "status" => $status,
                    "additionalDetails" => $additionalDetails,
                    "partner" => $partner,
                    "startDate" => $startDate,
                    "endDate" => $endDate,   
                );
            }
        
            http_response_code(200);
            echo json_encode(array(
                "status" => 1,
                "extensionPrograms" =>  $extensionProgram,
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "status" => 0,
                "message" => "No programs found for the lead",
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