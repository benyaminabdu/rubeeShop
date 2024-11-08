<?php

    namespace App\Functions;
    use PDO;

    class  AdminAuthFunctions {
        
        public $db;

        public function __construct($db){
            $this->db = $db;
        }

        public function checkIfExists($username){

            $sql = "SELECT * FROM `adminData` WHERE `username`=\"$username\" ";
            $userData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            if(!empty($userData)){
                return true;
            }   
            else {
                return false;
            }
        }   

        public function getAuthData($username){

            $sql = "SELECT * FROM `adminData` WHERE `username`=\"$username\" ";
            $userData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $userData;
        }

        public function checkIfTokenMatches($accessToken,$tokenVerify){

            $sql = "SELECT * FROM `adminData` WHERE `accessToken`=\"$accessToken\" AND `tokenVerify`=\"$tokenVerify\" ";
            $authData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            if(!empty($authData)){
                return true;
            }
            else {
                return false;
            }
        }

        public function getCredentialDataByToken($accessToken,$tokenVerify){

            $sql = "SELECT * FROM `adminData` WHERE `accessToken`=\"$accessToken\" AND `tokenVerify`=\"$tokenVerify\" ";
            $authData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $authData;
        }

    }

?>