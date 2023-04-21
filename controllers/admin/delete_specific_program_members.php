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
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        
        $query = "DELETE FROM programmembers WHERE id = $id";
        $progMember = $connection->prepare($query);
    
        if($progMember->execute()){
            echo json_encode(array('success' => 'deleted'));
        }
      }
}else{
    echo json_encode(array("error"=>'error'));
}

