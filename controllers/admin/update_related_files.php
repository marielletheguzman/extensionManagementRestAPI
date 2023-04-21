<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: POST");
header ("Content-type: application/json; charset=UTF-8"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type, Authorization');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

include_once("../../database/database.php");
include_once("../../models/Admin.php");

$db = new Database();
$connection = $db->connect();
$adminDetails = new Admin ($connection);


// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = isset($_GET['id']) ? $_GET['id'] : null;

    $certificate = null;
    if (isset($_FILES['certificate']) && !empty($_FILES['certificate']['name'])) {
        $fileTmpPath = $_FILES['certificate']['tmp_name'];
        $fileName = $_FILES['certificate']['name'];
        $fileSize = $_FILES['certificate']['size'];
        $fileType = $_FILES['certificate']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
    
        // create a unique file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
        // move the uploaded file to a new location
        $dest_path = '../../assets/relatedFiles/'. $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
          $certificate = $newFileName;
        }
    }    
    $attendance = null;
    if (isset($_FILES['attendance']) && !empty($_FILES['attendance']['name'])) {
        $fileTmpPath = $_FILES['attendance']['tmp_name'];
        $fileName = $_FILES['attendance']['name'];
        $fileSize = $_FILES['attendance']['size'];
        $fileType = $_FILES['attendance']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
    
        // create a unique file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
        // move the uploaded file to a new location
        $dest_path = '../../assets/relatedFiles/'. $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
          $attendance = $newFileName;
        }
    }   
     $invitation = null;
    if (isset($_FILES['invitation']) && !empty($_FILES['invitation']['name'])) {
        $fileTmpPath = $_FILES['invitation']['tmp_name'];
        $fileName = $_FILES['invitation']['name'];
        $fileSize = $_FILES['invitation']['size'];
        $fileType = $_FILES['invitation']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
    
        // create a unique file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
        // move the uploaded file to a new location
        $dest_path = '../../assets/relatedFiles/'. $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
          $invitation = $newFileName;
        }
    }
    if (empty($invitation)||empty($attendance)||empty($certificate)) {
        http_response_code(400);
        echo json_encode(array('error' =>   $invitation, $attendance, $id, $certificate   ));
        exit();
    }

    $query = "UPDATE extensionprograms SET invitation = ?, attendance=?, certificate=? WHERE id=?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssi", $invitation, $attendance, $certificate, $id);

    if ($stmt->execute()) {

        echo json_encode(array('goods' =>   $invitation,$attendance,$id, $certificate   ));
        exit();
    }else{
        echo json_encode(array('goods' =>   $invitation,$attendance,$id, $certificate   ));
    }
}