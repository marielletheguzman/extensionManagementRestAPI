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

        try {
            $jwt = $headers['Authorization'];
            $secretKey = "bawiAko";
            $decodedData = JWT::decode($jwt, new Key($secretKey, 'HS512'));

            if(isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $adminDetails->id = $id;
                $programDetails = $adminDetails->getSpecificPartner();





                if(!empty($programDetails)){ 
                    $id = $programDetails['id'];
                    $partnerName = $programDetails['partnerName'];
                    $partnerAddress = $programDetails['partnerAddress'];
                    $partnerContactPerson = $programDetails['partnerContactPerson'];
                    $partnerLogo = $programDetails['partnerLogo'];
                    $partnerMoaFile = $programDetails['partnerMoaFile'];
                    $partnerStartDate = $programDetails['partnerStartDate'];
                    $partnerEndDate = $programDetails['partnerEndDate'];

                    http_response_code(200);
                    echo json_encode(array(
                        "status" => 1,
                        "data" => array(
                            "id" => $id,
                            "partnerName" => $partnerName,
                            "partnerAddress" => $partnerAddress,
                            "partnerContactPerson" => $partnerContactPerson,
                            "partnerLogo" => $partnerLogo,
                            "partnerMoaFile" => $partnerMoaFile,
                            "partnerStartDate" => $partnerStartDate,
                            "partnerEndDate" => $partnerEndDate,
                        ),
            
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
