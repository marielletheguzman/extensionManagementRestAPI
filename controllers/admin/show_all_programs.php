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

                // Create SQL query
                $sql = "SELECT * FROM extensionprograms WHERE endDate >= DATE_ADD(NOW(), INTERVAL 1 DAY);";

                // Execute query
                $result = $connection->query($sql);


                // Create an array to hold the results
                $partners = array();

                // Check if there are any results
                if ($result->num_rows > 0) {
                // Loop through the results and add them to the array
                while($row = $result->fetch_assoc()) {
                    $partner = array(
                    "id" => $row["id"],
                    "programTitle" => $row["programTitle"],
                    "programLead" => $row["programLead"],
                    "place" => $row["place"],
                    "additionalDetails" => $row["additionalDetails"],
                    "partner" => $row["partner"],
                    "startDate" => $row["startDate"],
                    "endDate" => $row["endDate"],
                    "certificate" => $row["certificate"],
                    "attendance" => $row["attendance"],
                    "invitation" => $row["invitation"]
                    );
                    // Add the partner to the array
                    array_push($partners, $partner);
                }
                }


            if ($partners !== false) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode(array("programs" =>$partners));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Unable to retrieve partner data."));
            
            
    }
    }