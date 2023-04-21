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
                    $involvement = $row['involvement'];
                    $programmembers[] = array(
                        "user" => $user,
                        "position" => $position,
                        "userId" => $user_id,
                        "involvement" => $involvement);
                    
                    }if(empty($programmembers)){
                        $programmembers='';
                    }
                    
                    $sql1 = "SELECT * FROM programparticipant WHERE program_id = $id";
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

                        $sql2 = "SELECT * FROM programflow WHERE program_id = $id";
                        $result2 = $connection->query($sql2);
                
                  
                        while($row = $result2->fetch_assoc()) {

                            $eventName = $row['eventName'];
                            $eventType = $row['eventType'];
                            $events[] = array(
                                "eventName" => $eventName,
                                "eventType" => $eventType,
                             
                            );}if(empty($events)) {
                                $events = '';
                            }

                $sql3 = "SELECT * FROM programflow WHERE program_id = $id";
                $result3 = $connection->query($sql3);
                while($row = $result3->fetch_assoc()) {
                     $eventName = $row['eventName'];
                     $eventType = $row['eventType'];
                     $program_id = $row['program_id'];
                     $programflow[] = array(
                           "eventName" => $eventName,
                          "eventType" => $eventType,
                          "program_id" => $program_id);
                     
                     }if(empty($programflow)){
                          $programflow='';
                      }



                        $sql = "SELECT * FROM relatedfiles WHERE extension_id = $id";
                        $result = $connection->query($sql);
                        if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $certificate = $row['certificate'];
                            $attendance = $row['attendance'];
                            $invitation = $row['invitation'];
                            $relatedFiles[] = array(
                                "certificate" => $certificate,
                                "attendance" => $attendance,
                                "invitation" => $invitation,);
                            }}else{
                                $relatedFiles = array();
                            
                            }if(empty($relatedFiles)) {
                                $relatedFiles[] = array(
                                    "certificate" => "",
                                    "attendance" => "",
                                    "invitation" => "",);
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
                    $certificate = $programDetails['certificate'];
                    $attendance = $programDetails['attendance'];
                    $invitation = $programDetails['invitation'];

                    if(empty($certificate)){
                        $certificate='';
                    }
                    if(empty($attendance)){
                        $attendance='';
                    }
                    if(empty($invitation)){
                        $invitation='';
                    }

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
                            "certificate" => $certificate,
                            "attendance" => $attendance,
                            "invitation" => $invitation,
                        ),
                        "programMembers" =>$programmembers,
                        "programparticipant" =>$participants,
                        "programflow" =>$programflow,
                        "events" =>$events,
                        "relatedFiles" =>$relatedFiles 
            
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
       
    }
