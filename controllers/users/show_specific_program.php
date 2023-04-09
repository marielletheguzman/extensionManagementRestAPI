<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: GET");
header ("Content-type: application/json; charset=UTF-8"); 

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

        if(isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $userDetails->id = $id;
            $programDetails = $userDetails->getSpecificProgram();


            $sql = "SELECT * FROM programmembers WHERE program_id = $id";
            $result = $connection->query($sql);
            while($row = $result->fetch_assoc()) {
                $user = $row['name'];
                $position = $row['position'];
                $user_id = $row['user_id'];
                $programmembers[] = array(
                    "user" => $user,
                    "position" => $position,
                    "userId" => $user_id,);
                
                }

                $sql = "SELECT * FROM programparticipant WHERE program_id = $id";
                $result = $connection->query($sql);
                while($row = $result->fetch_assoc()) {
                    $participant = $row['participant'];
                    $entity = $row['entity'];
                    $user_id = $row['user_id'];
                    $programparticipant[] = array(
                        "user" => $user,
                        "position" => $position,
                        "userId" => $user_id,);
                    
                    }



            if(!empty($programDetails)){ 
                $id = $programDetails['id'];
                $programTitle = $programDetails['programTitle'];
                $programLead = $programDetails['programLead'];
                $place = $programDetails['place'];
                $additionalDetails = $programDetails['additionalDetails'];
                $partner = $programDetails['partner'];
                $startDate = $programDetails['startDate'];
                $endDate = $programDetails['endDate'];

                http_response_code(200);
                echo json_encode(array(
                    "status" => 1,
                    "data" => array(
                        "id" => $id,
                        "programTitle" => $programTitle,
                        "programLead" => $programLead,
                        "place" => $place,
                        "additionalDetails" => $additionalDetails,
                        "partner" => $partner,
                        "startDate" => $startDate,
                        "endDate" => $endDate,
                    ),
                    "programMembers" =>$programmembers,
                    "programparticipant" =>$programparticipant 
           
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => 0,
                    "message" => "Data not found"
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
