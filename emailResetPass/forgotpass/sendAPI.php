<?php
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;

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

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
include_once("../../database/database.php");
include_once("../../models/Users.php");

$db = new Database();
$connection = $db->connect();
$userObj = new Users($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    

    $data = file_get_contents('php://input');
    $json = json_decode($data, true);
    $email = isset($json['email']) ? $json['email'] : null;
    
    if (!empty($email)) {
        $userObj->email = $email ;
        $userObj = $userObj->resetPass();
    }
    if (!empty($userObj)) {
        $email = $userObj['email'];

            if ($userObj) {
                $iss = "localhost";
                $iat = time();
                $nbf = $iat + 2;
                $exp = $iat + 1000000;
                $aud = "myusers";
                $adminArrData = array(
                    $email = $userObj['email'],
                    "id" => $userObj['id']
                );

                //secretkey::::::::
                $secret_key = "bawiAko";
                $payload_info = array(
                    "iss" => $iss,
                    "iat" => $iat,
                    "nbf" => $nbf,
                    "exp" => $exp,
                    "aud" => $aud,
                    "data" => $adminArrData
                );

                $jwt = JWT::encode($payload_info, $secret_key, 'HS512');
            }
        }


    echo var_dump($_POST);
    // Create a new instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Set up SMTP
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'verify.your.mail.01@gmail.com';
        $mail->Password = 'fgkncgkpranrxuso';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Set up email content
        $mail->setFrom('verify.your.mail.01@gmail.com', 'Your Name');
        $mail->addAddress($email);
        $mail->Subject = "Forgot Password";
        $mail->isHTML(true);
        $resetUrl = 'http://localhost:4200/reset';
        $image_url = 'https://anonsharing.com/cache/plugins/filepreviewer/11352/e8494db3ceed53e8cf442bad284b1ceef6ed3fb35ac963e3d38cfee2a444f925/1100x800_cropped.jpg';
        $mail->Body = '
        <html>
        <body>
        <div style="background-color:white; margin-left: 40%;">
        <h1> Reset Password</h1>

        <p>Click the link to change your password</p>    <a href="'.$resetUrl.'?token='.$jwt.'">Click Here</a>
        </div>
            <br>
        <div style="width: 100%; height: 200px; width:100%; object-fit: cover; background-image: url(' . $image_url . '); background-repeat: no-repeat; background-size: cover;"></div>

            </body>
        </html>
        ';
        // Send the email
        $mail->send();
        echo 'Email sent successfully.';
    } catch (Exception $e) {
        echo 'Email could not be sent. Error message: ', $mail->ErrorInfo;
    }
    echo var_dump($_POST);
}else{
    echo var_dump($_POST);
}
