<?php

    namespace App\Functions;
    use PDO;

    class  Variance {
        
        public $db;

        public function __construct($db){
            $this->db = $db;
        }

        public function addVariance($productID,$variance,$variances){
            $sql = "INSERT INTO `variance` (`productID`,`variance`,`variances`) VALUES (\"$productID\",\"$variance\",\"$variances\") ";
            $this->db->query($sql);
        }

        public function getVariances($productID){

            $sql = "SELECT * FROM `variance` WHERE `productID`=\"$productID\" ";
            $variances = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $variances;
        }

        public function deleteVariance($varianceID){

            $sql = "DELETE FROM `variance` WHERE `varianceID`=\"$varianceID\" ";
            $this->db->query($sql);
        }

    }
?>