<?php

    class Admin {
        public $username;
        public $password;

        //create extension program
        public $programTitle;
        public $programLead;
        public $place;
        public $additionalDetails;
        public $partner;
        public $startDate;
        public $endDate;

        //add program members
        public $program_id;
        public $user_id;
        public $name;
        public $position;

        // add program participant
        public $participant;
        public $entity;

        //add event 
        public $eventName;
        public $eventType;

        //add extension partner
        public $partnerName;
        public $partnerAddress;
        public $partnerContactPerson;
        public $partnerContactNumber;
        public $partnerLogo;
        public $partnerMoaFile;
        public $partnerStartDate;
        public $partnerEndDate;
        public $partnerIsExpired;

        public $selectedProgramId;
        //connection and table declaration
        private $conn;
        private $extensionprograms;
        private $programflow;
        private $programmembers;
        private $programparticipant;
        private $admin_account;
        private $extensionPartner;

        public function __construct($db){
            $this->conn = $db;

            $this->extensionprograms = "extensionprograms";
            $this->programflow = "programflow";
            $this->programmembers = "programmembers";
            $this->programparticipant = "programparticipant";
            $this->admin_account = "admin_account";
            $this->extensionPartner = "extensionpartner";
            $this->userstbl = "users";
        }

        
        //for admin to login::
            public function adminLogin(){
                $query = "SELECT * FROM ".$this->admin_account." WHERE username = ?";
                $adminObj = $this->conn->prepare($query);
                $adminObj->bind_param("s", $this->username);

                if($adminObj->execute()){
                    $data= $adminObj->get_result();
                    return $data->fetch_assoc();
                }return array();
            }
        //for admin to create a extension program 
            public function createExtensionProgram(){
                $query = "INSERT INTO ".$this->extensionprograms." SET programTitle=?, programLead=?, place=?, additionalDetails=?, partner=?, startDate=?, endDate=?";
                $extensionObj = $this->conn->prepare($query);

                //sanitizing:::;
                $programTitle = htmlspecialchars(strip_tags($this->programTitle));
                $programLead = htmlspecialchars(strip_tags($this->programLead));
                $place = htmlspecialchars(strip_tags($this->place));
                $additionalDetails = htmlspecialchars(strip_tags($this->additionalDetails));
                $partner = htmlspecialchars(strip_tags($this->partner));
                $startDate = htmlspecialchars(strip_tags($this->startDate));
                $endDate = htmlspecialchars(strip_tags($this->endDate));

                //to binddd:::::
                $extensionObj->bind_param("sssssss", $this->programTitle, $this->programLead, $this->place, $this->additionalDetails, $this->partner, $this->startDate, $this->endDate);

                if($extensionObj->execute()){
                    return true;
                }return false;
            }
        // to add program members inside extension program
            public function createProgramMembers(){
                    $sql = "SELECT * FROM extensionprograms ORDER BY id DESC LIMIT 1";
                    $result = $this->conn->query($sql);

                    if ($result->num_rows > 0) {
  
                    while($row = $result->fetch_assoc()) {
                        $selectedProgramId= $row["id"];
                    }
                    } else {
         
                    }
                    
                $query = "INSERT INTO programmembers SET program_id=".$selectedProgramId.", name=?, position=?, user_id=?";
                $progMember = $this->conn->prepare($query);

                //sanitize
                $name = htmlspecialchars(strip_tags($this->name));
                $position = htmlspecialchars(strip_tags($this->position));
                $user_id = htmlspecialchars(strip_tags($this->user_id));

                //to bind!
                $progMember->bind_param("ssi", $this->name, $this->position, $this->user_id);
                if($progMember->execute()){
                    return true;
                }else{
                    return false;
                }
            }
        //to add program participants
            public function createProgramParticipant(){
                $sql = "SELECT * FROM extensionprograms ORDER BY id DESC LIMIT 1";
                $result = $this->conn->query($sql);

                if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                    $selectedProgramId= $row["id"];
                }
                } else {
     
                }
                $query = "INSERT INTO ".$this->programparticipant."  program_id=".$selectedProgramId.", participant=?, entity=?, user_id=?";
                $progParticipant = $this->conn->prepare($query);

                //sanitize::::
                $participant = htmlspecialchars(strip_tags($this->participant));
                $entity = htmlspecialchars(strip_tags($this->entity));
                $user_id = htmlspecialchars(strip_tags($this->user_id));

                //to bind::
                $progParticipant->bind_param("isss", $this->participant, $this->entity, $this->user_id );
                if($progParticipant->execute()){
                    return true;
                }else{
                    return false;
                }
            }
        //to add program flow
            public function createProgramFlow(){
                $query = "INSERT INTO ".$this->programflow." SET eventName=?, eventType=?, program_id=?";
                $flow = $this->conn->prepare($query);

                //sanitize----------
                $eventName = htmlspecialchars(strip_tags($this->eventName));
                $eventType = htmlspecialchars(strip_tags($this->eventType));
                $program_id = htmlspecialchars(strip_tags($this->program_id));

                //to bind----
                $flow->bind_param("sss", $this->eventName,$this->eventType,$this->program_id);
                if($flow->execute()){
                    return true;
                }else{
                    return false;
                }
            }

            public function createExtensionPartner(){
                $query = "INSERT INTO extensionpartner SET partnerName=?, partnerAddress=?, partnerContactPerson=?, partnerContactNumber =?, partnerLogo=?, partnerMoaFile=?, partnerStartDate=?, partnerEndDate=?, 	partnerIsExpired='No'";
                $partner = $this->conn->prepare($query);

                //--sanitize--
                $partnerName = htmlspecialchars(strip_tags($this->partnerName));
                $partnerAddress = htmlspecialchars(strip_tags($this->partnerAddress));
                $partnerContactPerson = htmlspecialchars(strip_tags($this->partnerContactPerson));
                $partnerContactNumber = htmlspecialchars(strip_tags($this->partnerContactNumber));
                $partnerLogo = htmlspecialchars(strip_tags($this->partnerLogo));
                $partnerMoaFile = htmlspecialchars(strip_tags($this->partnerMoaFile));
                $partnerStartDate = htmlspecialchars(strip_tags($this->partnerStartDate));
                $partnerEndDate = htmlspecialchars(strip_tags($this->partnerEndDate));

                $partner->bind_param("ssssssss", $this->partnerName, $this->partnerAddress, $this->partnerContactPerson, $this->partnerContactNumber, $this->partnerLogo, $this->partnerMoaFile, $this->partnerStartDate, $this->partnerEndDate);
                if($partner->execute()){
                    return true;
                }else{
                    return false;
                }

            }

            public function listOfPendingAccounts(){
                $query = "SELECT * FROM ".$this->userstbl." WHERE isApprove = 'No'";
                $pending = $this->conn->prepare($query);
                $pending->execute();
                return $pending->get_result();
            }

            public function listOfManageAccount(){
                $query = "SELECT * FROM ".$this->userstbl." WHERE isApprove = 'Yes' AND isArchive='No'";
                $pending = $this->conn->prepare($query);
                $pending->execute();
                return $pending->get_result();
            }

            public function showUserProfileDetails(){
                $query = "SELECT * FROM users WHERE id=?";
                $showDetails = $this->conn->prepare($query);
                $showDetails->bind_param("i", $this->id);
                if($showDetails->execute()){
                    return $showDetails; // return the prepared statement object
                }else{
                    return false;
                }
            }

            public function editUserProfile(){
                $query = "UPDATE ".$this->userstbl." SET fullname = ?, email = ?, position = ?, password = ?, profilePicture = ? WHERE id=?";
                $adminObj = $this->conn->prepare($query);
                $adminObj->bind_param("sssssi", $this->fullName, $this->email, $this->position,$this->password,$this->profilePicture, $this->id);

                if($adminObj->execute()){
                    return true;
                }else{
                    return false;
                }
            }

            public function approveAccount(){
                $query = "UPDATE users SET isApprove = 'Yes' WHERE id=?";
                $adminObj = $this->conn->prepare($query);
                $adminObj->bind_param("i", $this->id);

                if($adminObj->execute()){
                    return true;
                }else{
                    return false;
                }
            }

            public function declineAccount(){
                $query = "UPDATE users SET isApprove = 'Declined' WHERE id=?";
                $adminObj = $this->conn->prepare($query);
                $adminObj->bind_param("i", $this->id);

                if($adminObj->execute()){
                    return true;
                }else{
                    return false;
                }
            }

            public function archiveAccount(){
                $query = "UPDATE users SET isArchive = 'Yes' WHERE id=?";
                $adminObj = $this->conn->prepare($query);
                $adminObj->bind_param("i", $this->id);

                if($adminObj->execute()){
                    return true;
                }else{
                    return false;
                }
            }
    }