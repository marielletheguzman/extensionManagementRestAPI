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
    $userDetails = new Admin ($connection);


    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        $headers = getallheaders();

        $token = $_GET['token'];
        echo $token;
        // $jwt = $headers['Authorization'];
        $secretKey = "bawiAko";
        $decodedData = JWT::decode($token, new Key($secretKey,  'HS512'));
        $id = $decodedData->data->id;
        $password = isset($data->password) ? $data->password : null;

            $pw=$password;
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try{

                $query = "UPDATE users SET password=? WHERE id=?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("si", $hashedPassword, $id);

                if (!$stmt->execute()) {
                    http_response_code(500);
                    echo json_encode(array('error' => 'Internal Server Error'));
                    exit();
                }
                if ($stmt->execute()) {
                    http_response_code(200);
                    echo json_encode(array(
                        "status" => 1,
                        "message" => "Admin Profile has been updated!",
                        "AdminProfile" => array(
                            "Description" =>$hashedPassword,
                            "password" => $pw
                        )
                    ));
    
            }  }catch(Exception $ex){
                http_response_code(500);
                echo json_encode(array(
                    "status"=>0,
                    "message" => "Invalid user"
                ));
            }
        
    }else{
        http_response_code(500);
        echo json_encode(array(
            "status"=>0,
            "message" => "Access Denied"
        ));
    }