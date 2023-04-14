    <?php
    ini_set("display_errors", 1);
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
    header("Content-type: multipart/form-data; charset=UTF-8");
    die();
    }

    include_once("../../database/database.php");
    include_once("../../models/Admin.php");



    $db = new Database();
    $connection = $db->connect();
    $adminObj = new Admin($connection);

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $partnerName =  $_POST['partnerName'];
        echo json_encode(array(
            "status" => 0,
            "message" => $fullName
        ));
        $partnerName = isset($data->partnerName) ? $data->partnerName : null;
        $partnerAddress = isset($data->partnerAddress) ? $data->partnerAddress : null;
        $partnerContactPerson = isset($data->partnerContactPerson) ? $data->partnerContactPerson : null;
        $partnerContactNumber = isset($data->partnerContactNumber) ? $data->partnerContactNumber : null;
        $partnerStartDate = isset($data->partnerStartDate) ? $data->partnerStartDate : null;
        $partnerEndDate = isset($data->partnerEndDate) ? $data->partnerEndDate : null;
    

        if (!empty($partnerName) && !empty($partnerAddress) && !empty($partnerContactPerson) && !empty($partnerContactNumber) && !empty($partnerStartDate) && !empty($partnerEndDate)) {
            //for logo
            $partnerLogo = null;
            if (isset($_FILES['partnerLogo']) && !empty($_FILES['partnerLogo']['name'])) {
                $fileTmpPath = $_FILES['partnerLogo']['tmp_name'];
                $fileName = $_FILES['partnerLogo']['name'];
                $fileSize = $_FILES['partnerLogo']['size'];
                $fileType = $_FILES['partnerLogo']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // create a unique file name
                $newFileName1 = md5(time() . $fileName) . '.' . $fileExtension;

                // move the uploaded file to a new location
                $uploadFileDir = '../../assets/extensionProfile/';
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $partnerLogo = $dest_path;
                }
            }

            //for MOA
            $partnerMoaFile = null;
            if (isset($_FILES['partnerMoaFile']) && !empty($_FILES['partnerMoaFile']['name'])) {
                $fileTmpPath = $_FILES['partnerMoaFile']['tmp_name'];
                $fileName = $_FILES['partnerMoaFile']['name'];
                $fileSize = $_FILES['partnerMoaFile']['size'];
                $fileType = $_FILES['partnerMoaFile']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                    // create a unique file name
                    $newFileName2 = md5(time() . $fileName) . '.' . $fileExtension;
                
                    // move the uploaded file to a new location
                    $uploadFileDir = '../../assets/extensionFiles/';
                    $dest_path = $uploadFileDir . $newFileName;
                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $partnerMoaFile = $dest_path;
                    }
                }

                $adminObj = new Admin($connection);
                $adminObj->partnerName = $partnerName;
                $adminObj->partnerAddress = $partnerAddress;
                $adminObj->partnerContactPerson = $partnerContactPerson;
                $adminObj->partnerContactNumber = $partnerContactNumber;
                $adminObj->partnerLogo = $newFileName1;
                $adminObj->partnerMoaFile = $newFileName2;
                $adminObj->partnerStartDate = $partnerStartDate;
                $adminObj->partnerEndDate = $partnerEndDate;

                if($adminObj->createExtensionPartner()){
                    http_response_code(200);
                    echo json_encode(array(
                        "status" => 1,
                        "message" => "Extension partner has been added"
                    ));
                }else{
                    http_response_code(500);
                    echo json_encode(array(
                        "status" => 0,
                        "message" => "Failed to add extension"
                    ));
                } 
            
        } else{
            http_response_code(404);
            echo json_encode(array(
                "status"=>0,
                "message" => var_dump($partnerName),
            ));
        }
    }else{
        http_response_code(500);
        echo json_encode(array(
            "status"=>0,
            "message" => "Access Denied"
        ));
    }