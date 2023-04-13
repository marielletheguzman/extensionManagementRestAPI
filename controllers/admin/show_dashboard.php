<?php
 ini_set("display_errors", 1);
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
USE \Firebase\JWT\Key;



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-type: application/json; charset=utf-8");

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
$adminObj = new Admin($connection);

$headers = getallheaders();

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    try{
        $jwt = $headers['Authorization'];
        $secretKey = "bawiAko";
        $decodedData = JWT::decode( $jwt, new Key($secretKey,  'HS512'));
    

    $faculty = $adminObj->getFacultyNumber();
    $pending = $adminObj->getPendingAccountsNumber();
    $extension = $adminObj->getExtensionPartnerNumber();
    $program = $adminObj->getActiveProgramNumber();
    $renew = $adminObj->getPartner30Days();

    $facultynum=0;
    $pendingAccounts=0;
    $extensionNum=0;
    $programNum = 0;

    if($faculty->num_rows > 0){
        while($row = $faculty->fetch_assoc()){
            $facultynum = $facultynum +1;
        }
    }  
    if($pending->num_rows > 0){
        while($row = $pending->fetch_assoc()){
            $pendingAccounts = $pendingAccounts +1;
        }
    }
    if($extension->num_rows > 0){
        while($row = $extension->fetch_assoc()){
            $extensionNum = $extensionNum +1;
        }
    }
    if($program->num_rows > 0){
        while($row = $program->fetch_assoc()){
            $programNum = $programNum +1;
        }
    }
    
    if($renew->num_rows > 0){
        while($row = $renew->fetch_assoc()){
            $name = $row['partnerName'];
            $id = $row['id'];
            $partnerAddress = $row['partnerAddress'];
            $partnerContactPerson = $row['partnerContactPerson'];
            $partnerContactNumber = $row['partnerContactNumber'];
            $partnerLogo = $row['partnerLogo'];
            $partnerMoaFile = $row['partnerMoaFile'];
            $partnerStartDate = $row['partnerStartDate'];
            $partnerEndDate = $row['partnerEndDate'];
            $extensionPartners[] = array(
                "id" => $id,
                "extensionPartner" => $name,
                "partnerAddress" => $partnerAddress,
                "partnerContactPerson" => $partnerContactPerson,
                "partnerContactNumber" => $partnerContactNumber,
                "partnerLogo" => $partnerLogo,
                "partnerMoaFile" => $partnerMoaFile,
                "partnerStartDate" => $partnerStartDate,
                "partnerEndDate" => $partnerEndDate,
            );
        }
    }
        http_response_code(200);
        echo json_encode (array(
            "status"=>"Success",
            "numbersOfFaculty"=>$facultynum,
            "numbersOfPendingAccounts"=>$pendingAccounts,
            "numbersOfExtensionPartner"=>$extensionNum,
            "numbersOfActivePrograms"=>$programNum,
            "partners" => $extensionPartners,

        ));
        }
    
    catch(Exception $ex){
        http_response_code(500);
        echo json_encode(array(
            "status" => "Failed",
            "message" =>"Authorization Failed"
        ));
        }
    }else{
        http_response_code(500);
        echo json_encode(array(
            "status" => "Failed",
            "message" =>"Access Denied"
        ));
    }
    
