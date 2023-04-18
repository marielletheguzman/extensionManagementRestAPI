<?php
ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

header ("Access-Control-Allow-Origin: *"); 
header ("Access-Control-Allow-Methods: PUT");
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
    $partnerStartDate = isset($_POST['partnerStartDate']) ? $_POST['partnerStartDate'] : null;
    $partnerEndDate = isset($_POST['partnerEndDate']) ? $_POST['partnerEndDate'] : null;

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
    if (empty($partnerStartDate)||empty($partnerEndDate)||empty($partnerMoaFile)) {
        http_response_code(400);
        echo json_encode(array('error' =>   $partnerStartDate,$partnerEndDate,$id, $partnerMoaFile   ));
        exit();
    }

    $query = "UPDATE extensionpartner SET partnerStartDate = ?, partnerEndDate=?, partnerMoaFile=? WHERE id=?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssi", $partnerStartDate, $partnerEndDate, $partnerMoaFile, $id);

    if ($stmt->execute()) {

        echo json_encode(array('goods' =>   $partnerStartDate,$partnerEndDate,$id, $partnerMoaFile   ));
        exit();
    }
}