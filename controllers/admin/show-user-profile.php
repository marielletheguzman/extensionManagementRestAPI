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
$admin = new Admin($connection);

if ($_SERVER['REQUEST_METHOD'] === 'GET'){

    $id = isset($_GET['id']) ? $_GET['id'] : null;

    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $getProfilearr = array();
        while($row = $result->fetch_assoc()){        
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
            "message" => "User details not found"
        ));
    }
}
