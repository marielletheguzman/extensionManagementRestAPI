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
include_once("../../models/Users.php");

$db = new Database();
$connection = $db->connect();
$userDetails = new Users($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : null;
  $email = isset($_POST['email']) ? $_POST['email'] : null;
  $position = isset($_POST['position']) ? $_POST['position'] : null;
  $password = isset($_POST['password']) ? $_POST['password'] : null;

  // process the uploaded file
  $profilePicture = null;
  if (isset($_FILES['profilePicture']) && !empty($_FILES['profilePicture']['name'])) {
    $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
    $fileName = $_FILES['profilePicture']['name'];
    $fileSize = $_FILES['profilePicture']['size'];
    $fileType = $_FILES['profilePicture']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // create a unique file name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // move the uploaded file to a new location
    $uploadFileDir = '../../assets/profile/';
    $dest_path = $uploadFileDir . $newFileName;
    if(move_uploaded_file($fileTmpPath, $dest_path)) {
      $profilePicture = $dest_path;
    }
  }

  if (!empty($fullName) && !empty($email) && !empty($position) && !empty($password)) {

    $userObj = new Users($connection);
    $userObj->fullName = $fullName;
    $userObj->email = $email;
    $userObj->position = $position;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userObj->password = $hashedPassword;
    $userObj->profilePicture = $newFileName;

    $emailData = $userObj->ifExist();
    if (!empty($emailData)) {
      http_response_code(500);
      echo json_encode(array(
        "status" => "failed",
        "message" => "Email is already used"
      ));
    } else {

      if ($userObj->registerUser()) {
        $userObj->addLogRegister();
        http_response_code(201);
        echo json_encode(array(
          "status" => "success",
          "message" => "User registered successfully"
        ));
      } else {
        http_response_code(500);
        echo json_encode(array(
          "status" => "failed",
          "message" => "Unable to register user"
        ));
      }
    }
  } else {
    http_response_code(500);
    echo json_encode(array(
      "status" => "failed",
      "message" => "All fields must have a value"
    ));
  }
}else{
    http_response_code(500);
    echo json_encode(array(
        "status" => "failed",
        "message" => "Access Denied"
    ));
}
?>