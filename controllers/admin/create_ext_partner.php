<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type: application/json; charset=UTF-8");
header("Content-type: multipart/form-data; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
  header('Access-Control-Allow-Headers: token, Content-Type');
  header('Access-Control-Max-Age: 1728000');
  header('Content-Length: 0');
  header('Content-Type: text/plain');
  die();
}
include_once("../../database/database.php");
include_once("../../models/Admin.php");

$db = new Database();
$connection = $db->connect();
$userDetails = new Admin($connection);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partnerName = isset($_POST['partnerName']) ? $_POST['partnerName'] : null;
    $partnerAddress = isset($_POST['partnerAddress']) ? $_POST['partnerAddress'] : null;
    $partnerContactPerson= isset($_POST['partnerContactPerson']) ? $_POST['partnerContactPerson'] : null;
    $partnerContactNumber= isset($_POST['partnerContactNumber']) ? $_POST['partnerContactNumber'] : null;
    $partnerStartDate= isset($_POST['partnerStartDate']) ? $_POST['partnerStartDate'] : null;
    $partnerEndDate= isset($_POST['partnerEndDate']) ? $_POST['partnerEndDate'] : null;
    
    $partnerLogo = null;
    if (isset($_FILES['partnerLogo']) && !empty($_FILES['partnerLogo']['name'])) {
        $fileTmpPath = $_FILES['partnerLogo']['tmp_name'];
        $fileName = $_FILES['partnerLogo']['name'];
        $fileSize = $_FILES['partnerLogo']['size'];
        $fileType = $_FILES['partnerLogo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
    
        // create a unique file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
        // move the uploaded file to a new location
        $dest_path = '../../assets/extensionProfile/' . $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
          $partnerLogo = $newFileName;
        }
    }
    
    $partnerMoaFile = null;
    if (isset($_FILES['partnerMoaFile']) && !empty($_FILES['partnerMoaFile']['name'])) {
        $fileTmpPath = $_FILES['partnerMoaFile']['tmp_name'];
        $fileName = $_FILES['partnerMoaFile']['name'];
        $fileSize = $_FILES['partnerMoaFile']['size'];
        $fileType = $_FILES['partnerMoaFile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
    
        // create a unique file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
        // move the uploaded file to a new location
        $dest_path = '../../assets/extensionFiles/'. $newFileName;
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
          $partnerMoaFile = $newFileName;
        }
    }
    
    
    // validate the form data
    if (empty($partnerName) || empty($partnerAddress)||empty($partnerContactPerson)||empty($partnerContactNumber)||empty($partnerStartDate)||empty($partnerEndDate)||empty($partnerLogo)||empty($partnerMoaFile)) {
        http_response_code(400);
        echo json_encode(array('error' => 'Bad Request'));
        exit();
    }
    
    // insert the data into the users table
    $query = "INSERT INTO extensionpartner (partnerName, partnerAddress, partnerContactPerson,partnerContactNumber, partnerStartDate,partnerEndDate, partnerLogo, partnerMoaFile) VALUES (?, ?, ?,?,?,?,?,?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssssss", $partnerName, $partnerAddress, $partnerContactPerson, $partnerContactNumber,$partnerStartDate, $partnerEndDate, $partnerLogo, $partnerMoaFile );
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(array('error' => 'Internal Server Error'));
        exit();
    }
    
    $postData = array('username' => $partnerName, 'email' => $partnerAddress, 'picture' => $partnerContactPerson);
    echo json_encode(array('success' => true, 'post' => $postData));
} else {
    http_response_code(405);
    echo json_encode(array('error' => 'Method Not Allowed'));
}
