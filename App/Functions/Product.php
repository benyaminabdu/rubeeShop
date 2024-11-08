<?php

    namespace App\Functions;
    use PDO;

    class  Product {
        
        public $db;

        public function __construct($db){
            $this->db = $db;
        }


        public function search($search){

            $sql = "SELECT * FROM `products` WHERE `title` LIKE '%$search%' ";
            $products = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $products;
        }

        public function getRandomProducts($count){

            $sql = "SELECT * FROM `products` ORDER BY RAND() LIMIT $count";
            $products = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $products;
        }

        public function getProductsByCategory($categoryID){

            $sql = "SELECT * FROM `products` WHERE `categoryID`=\"$categoryID\" ORDER BY RAND()";
            $products = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $products;
        }

        public function getProductsBySubcategory($subcategoryID){

            $sql = "SELECT * FROM `products` WHERE `subcategoryID`=\"$subcategoryID\" ORDER BY RAND()";
            $products = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $products;
        }

        public function getProduct($productID){

            $sql = "SELECT * FROM `products` WHERE `productID`=\"$productID\"";
            $productData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $productData;
        }

        public function getProductImages($productID){

            $sql = "SELECT * FROM `productimages` WHERE `productID`=\"$productID\"";
            $productImages = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $productImages;
        }

        public function getAllProducts(){

            $sql = "SELECT * FROM `products`";
            $products = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $products;
        }

        public function createProduct($title,$categoryID,$subcategoryID,$description,$img,$username){

            $time = time();
            $slug = strtolower(str_replace(" ", "-", $title));

            $sql = "INSERT INTO `products` (`slug`,`title`,`categoryID`,`subcategoryID`,`description`,`image`,`createdBy`,`createdAt`) 
                VALUES (\"$slug\",\"$title\",\"$categoryID\",\"$subcategoryID\",\"$description\",\"$img\",\"$username\",\"$time\")
            ";

            $this->db->query($sql);

            return $this->db->lastInsertId();
        }

        public function checkIfProductExists($productID){

            $sql = "SELECT * FROM `products` WHERE `productID`=\"$productID\" ";
            $data = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            if(!empty($data)){
                return true;
            }   
            else {
                return false;
            }

        }

        public function toggleProduct($productID, $isActive, $username){
            
            $time = time();

            $sql = "UPDATE `products` SET `isActive`=\"$isActive\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);

        }

        public function editProduct($productID,$title,$description,$username){

            $time = time();
            $slug = strtolower(str_replace(" ", "-", $title));

            $sql = "UPDATE `products` SET `slug`=\"$slug\", `title`=\"$title\", `description`=\"$description\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function setCategory($productID,$categoryID){

            $time = time();

            $sql = "UPDATE `products` SET `categoryID`=\"$categoryID\", `subcategoryID`=\"null\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function setSubcategory($productID,$subcategoryID){

            $time = time();

            $sql = "UPDATE `products` SET `subcategoryID`=\"$subcategoryID\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function editPrice($productID,$price,$username){

            $time = time();

            $sql = "UPDATE `products` SET `price`=\"$price\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function editInventory($productID,$inventory,$username){

            $time = time();

            $sql = "UPDATE `products` SET `inventory`=\"$inventory\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function editPicture($productID,$img,$username){

            
            $time = time();

            $sql = "UPDATE `products` SET `image`=\"$img\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function addImage($productID,$img,$username){

            $time = time();
            
            $sql = "UPDATE `products` SET `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);

            $sql = "INSERT INTO `productimages` (`productID`,`image`) VALUES (\"$productID\",\"$img\") ";
            $this->db->query($sql);

        }

        public function deleteImage($productID,$imgID,$username){

            $time = time();
            
            $sql = "UPDATE `products` SET `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);

            $sql = "DELETE FROM `productimages` WHERE `imageID`=\"$imgID\" ";
            $this->db->query($sql);
        }

        public function auditUpdate($productID,$username){

            $time = time();
            
            $sql = "UPDATE `products` SET `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

    }

?>