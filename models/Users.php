<?php
ini_set("display_errors", 1);
class Users{
    
    public $fullName;
    public $position;
    public $password;
    public $email;
    public $profilePicture;
    public $isApprove;
    public $isArchive;
    public $created_at;

    private $conn; 
    private $users;
    private $user_activity_log;

    public function __construct($db){
        $this->conn = $db;
        $this->users = "users";
        $this->user_activity_log = "user_activity_log";
    }

    public function registerUser(){
        $query = "INSERT INTO ".$this->users." SET fullName= ?, email =?, position= ?, password= ?, profilePicture= ?, isApprove= 'No', isArchive = 'No'";
        $userObj = $this->conn->prepare($query);
        $userObj->bind_param("sssss", $this->fullName, $this->email, $this->position,$this->password,$this->profilePicture);

        $date = date("Y-m-dhis");
        $file_name = $date . "_" . $_FILES['profilePicture']['name'];
        $target_path = "../assets/" . $file_name;
        move_uploaded_file($_FILES['profilePicture']['tmp_name'], $target_path);
    
        if($userObj->execute()){
            return true;
        }
        return false;
    }


    public function ifExist(){
        $query = "SELECT * FROM ".$this->users." WHERE email= ?";
        $userObj = $this->conn->prepare($query);
        $userObj->bind_param("s", $this->email);

        if($userObj->execute()){
            $data = $userObj->get_result();
            return $data->fetch_assoc();
        }
        return array();
    }

    public function addLogRegister(){
        $query = "INSERT INTO user_activity_log SET fullName= ?, type_of_activity = 'User Registered',  email =? ,position= ?";
        $userObj = $this->conn->prepare($query);
        $userObj->bind_param("sss",  $this->fullName, $this->email, $this->position);
        if($userObj->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function loginUser(){
        $query = "SELECT * FROM users WHERE email = ? AND (isApprove = 'Yes' OR isApprove = 'No')";
        $login = $this->conn->prepare($query);
        $login->bind_param("s", $this->email);
        
        //execute
        if($login->execute()){
            $data = $login->get_result();
            return $data->fetch_assoc();

        }
        return array();
    }

    public function resetPass(){
        $query = "SELECT * FROM users WHERE email = ?";
        $login = $this->conn->prepare($query);
        $login->bind_param("s", $this->email);
        
        //execute
        if($login->execute()){
            $data = $login->get_result();
            return $data->fetch_assoc();

        }
        return array();
    }


    public function loginUserNotApprove(){
        $query = "SELECT * FROM users WHERE email = ? and isApprove='No'";
        $login = $this->conn->prepare($query);
        $login->bind_param("s", $this->email);
        
        //execute
        if($login->execute()){
            $data = $login->get_result();
            return $data->fetch_assoc();

        }
        return array();
    }
    public function viewPrograms(){
        $query = "SELECT * FROM users WHERE id = ?";
        $user_view = $this->conn->prepare($query);
        $user_view->bind_param("s", $this->id);
        
        //execute
        if($user_view->execute()){
            $data = $user_view->get_result();
            return $data->fetch_assoc();

        }
        return array();
    }
    public function viewProgramsLead(){
        $query = "SELECT * FROM extensionprograms WHERE lead_id = ? AND endDate >= DATE_ADD(NOW(), INTERVAL 1 DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->id);
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $num_rows = mysqli_num_rows($result);
        if ($num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function  updateProfile(){
        $query = "UPDATE ".$this->users." SET fullname = ?, email = ?, position = ? WHERE id=?";
        $userObj = $this->conn->prepare($query);
        $userObj->bind_param("sssi", $this->fullName, $this->email, $this->position, $this->id);
        if($userObj->execute()){
            return true;
        }else{
            return false;
        }
    }

        public function showListOfAssigned(){
            $query = "SELECT * FROM users WHERE id=?";
            $showDetails = $this->conn->prepare($query);
            $showDetails->bind_param("i", $this->id);
            if($showDetails->execute()){
                return $showDetails; // return the prepared statement object
            }else{
                return false;
            }
        }

        public function getRelatedPrograms(){
            $query = "SELECT * FROM programmembers WHERE program_id = ?";
            $showDetails = $this->conn->prepare($query);
            $showDetails->bind_param("i", $this->id);
            $showDetails->execute();
            return $showDetails->get_result();
        }

        public function getSpecificProgram(){
            $extProg = "SELECT * FROM extensionprograms WHERE id = ? ";
            $showDetails = $this->conn->prepare($extProg);
            $showDetails->bind_param("i", $this->id);
            $showDetails->execute();
            return $showDetails->get_result()->fetch_assoc();
        }

        public function getUserProfile(){
            $query = "SELECT * FROM users WHERE id = ?";
            $showDetails = $this->conn->prepare($query);
            $showDetails->bind_param("i", $this->id);
            $showDetails->execute();
            return $showDetails->get_result();
        }
}


