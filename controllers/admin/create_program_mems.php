<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
  header('Access-Control-Allow-Headers: token, Content-Type,Authorization');
  header('Access-Control-Max-Age: 1728000');
  header('Content-Length: 0');
  header('Content-Type: text/plain');
}

include_once("../../database/database.php");
include_once("../../models/Admin.php");

$db = new Database();
$connection = $db->connect();
$userDetails = new Admin($connection);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');
    $json = json_decode($data, true);

    $name = isset($json['name']) ? $json['name'] : null;
    $position = isset($json['position']) ? $json['position'] : null;
    $user_id= isset($json['user_id']) ? $json['user_id'] : null;

    if (empty($name) || empty($position) || empty($user_id)) {
        http_response_code(400);
        echo json_encode(array('error' => $data));
        exit();
    }

    $sql = "SELECT * FROM extensionprograms ORDER BY id DESC LIMIT 1";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
        $selectedProgramId= $row["id"];
    }
    } else {

    }

    $query = "INSERT INTO programmembers SET program_id=".$selectedProgramId.", name=?, position=?, user_id=?";
    $progMember = $connection->prepare($query);

    //sanitize
    $name = htmlspecialchars(strip_tags($name));
    $position = htmlspecialchars(strip_tags($position));
    $user_id = htmlspecialchars(strip_tags($user_id));

    //to bind!
    $progMember->bind_param("ssi", $name, $position, $user_id);
    if($progMember->execute()){
        echo json_encode(array('success' => 'success'));
    }
    
    echo json_encode(array('name' => $name, 'position' => $position, 'user_id' => $user_id));
}else{
    echo json_encode(array("error"=>'error'));
}

