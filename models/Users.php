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

        if($userObj->execute()){
            return true;
        }else{
            return false;
        }
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

    public function updateUser($userId){
        $query = "UPDATE ".$this->users." SET fullName= ?, email =?, position= ?, password= ?, profilePicture= ? WHERE userId = ?";
        $userObj = $this->conn->prepare($query);
        $userObj->bind_param("sssssi", $this->fullName, $this->email, $this->position,$this->password,$this->profilePicture, $userId);
    
        if($userObj->execute()){
            return true;
        }else{
            return false;
        }
    }

}


