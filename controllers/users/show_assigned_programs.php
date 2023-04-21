
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


        $pid=0;

        if($userDetails->viewPrograms()){
            
            $sql = "SELECT * FROM programmembers WHERE user_id = $id";
            $result = $connection->query($sql);
    
      
            while($row = $result->fetch_assoc()) {
                $user = $row['name'];
                $position = $row['position'];
                $user_id = $row['user_id'];
                $program_id = $row['program_id'];
                $pid =  $program_id;
                $members[] = array(
                    "user" => $user,
                    "position" => $position,
                    "userId" => $user_id,
                    "programID" => $program_id,
                 
        );
            
        //added extension programs in loop
        $extProg = "SELECT * FROM extensionprograms WHERE id = $pid AND endDate >= DATE_ADD(NOW(), INTERVAL 1 DAY)";
        $res = $connection->query($extProg);
        
        while($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $programTitle = $row['programTitle'];
            $programLead = $row['programLead'];
            $place = $row['place'];
            $additionalDetails = $row['additionalDetails'];
            $partner = $row['partner'];
            $startDate = $row['startDate'];
            $endDate = $row['endDate'];
            $extensionProgram[] = array(

                "id" => $id,
                "programTitle" => $programTitle,
                "programLead" => $programLead,
                "place" => $place,
                "additionalDetails" => $additionalDetails,
                "partner" => $partner,
                "startDate" => $startDate,
                "endDate" => $endDate,   
    );

        }
        $sql1 = "SELECT * FROM programparticipant WHERE program_id = $pid";
        $result1 = $connection->query($sql1);

  
        while($row = $result1->fetch_assoc()) {
            $participant = $row['participant'];
            $entity = $row['entity'];
            $user_id = $row['user_id'];
            $program_id = $row['program_id'];
            $pid =  $program_id;
            $participants[] = array(
                "participant" => $participant,
                "entity" => $entity,
                "userId" => $user_id,
                "programID" => $program_id,
             
    );}if(empty($participants)) {
        $participants = '';
    }

    
            }

            http_response_code(200);
            echo json_encode(array(
                "status" => 1,
                "message" => "Program view". $pid ,
                "extensionPrograms" =>  $extensionProgram,
                "programMembers"=> $members,
                "participant"=> $participants,
                
            ));
        }else{
            http_response_code(500);
            echo json_encode(array(
                "status" => 0,
                "message" => "Failed to add participant",
                "data"=> $decodedData
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