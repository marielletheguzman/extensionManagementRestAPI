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

    $participant = isset($json['participant']) ? $json['participant'] : null;
    $entity = isset($json['entity']) ? $json['entity'] : null;
   

    if (empty($participant) || empty($entity)) {
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
    $query = "INSERT INTO programparticipant (program_id, participant, entity) VALUES (?, ?, ?)";
    $progParticipant = $connection->prepare($query);

    //sanitize::::
    $participant = htmlspecialchars(strip_tags($participant));
    $entity = htmlspecialchars(strip_tags($entity));

    //to bind::
    $progParticipant->bind_param("iss", $selectedProgramId, $participant, $entity);
    if($progParticipant->execute()){
        echo json_encode(array('participant' => $participant, 'entity' => $entity));
    }else{
        echo json_encode(array('participant' => "error"));
    }

}else{
    echo json_encode(array("error"=>'error'));
}
