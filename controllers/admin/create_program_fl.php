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

    $eventName = isset($json['eventName']) ? $json['eventName'] : null;
    $eventType = isset($json['eventType']) ? $json['eventType'] : null;
   

    if (empty($eventName) || empty($eventType)) {
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
    $query = "INSERT INTO programflow (program_id, eventName, eventType) VALUES (?, ?, ?)";
    $progParticipant = $connection->prepare($query);

    //sanitize::::
    $eventName = htmlspecialchars(strip_tags($eventName));
    $eventType = htmlspecialchars(strip_tags($eventType));

    //to bind::
    $progParticipant->bind_param("iss", $selectedProgramId, $eventName, $eventType);
    if($progParticipant->execute()){
        echo json_encode(array('eventName' => $eventName, 'eventType' => $eventType, "selectedId:"=> $selectedProgramId));
    }else{
        echo json_encode(array('participant' => "error"));
    }

}else{
    echo json_encode(array("error"=>'error'));
}
