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
    include_once("../../models/Admin.php");

    $db = new Database();
    $connection = $db->connect();
    $adminDetails = new Admin($connection);

    if ($_SERVER['REQUEST_METHOD'] === 'GET'){

        $headers = getallheaders();

        $sql = "SELECT * FROM extensionprograms ORDER BY id DESC LIMIT 1";
        $result = $connection->query($sql);
    
        if ($result->num_rows > 0) {
    
        while($row = $result->fetch_assoc()) {
            $selectedProgramId= $row["id"];
        }
        } else {
    
        }
    
        $query = "SELECT * FROM programflow WHERE program_id=".$selectedProgramId."";
        $progMember = $connection->query($query);
    
    
        $progMems = array();
        if($progMember->num_rows > 0){
            while ($row = $progMember->fetch_assoc()){
                $progMem = array(
                    "id" => $row['id'],
                    "eventName" => $row['eventName'],
                    "eventType" => $row['eventType'],
                    "selectedId" => $selectedProgramId,
                );
                array_push($progMems, $progMem);
            }
        }


            if ($progMems !== false) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array("flow" =>$progMems));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Unable to retrieve partner data."));
            
            
    }
    }