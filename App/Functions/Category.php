<?php

    namespace App\Functions;
    use PDO;

    class  Category {
        
        public $db;

        public function __construct($db){
            $this->db = $db;
        }

        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        //Category

        public function checkIfCategoryExists($categoryID){

            $sql = "SELECT * FROM `categories` WHERE `categoryID`=\"$categoryID\" ";
            $data = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            if(!empty($data)){
                return true;
            }   
            else {
                return false;
            }
        }

        public function getCategory($categoryID){

            $sql = "SELECT * FROM `categories` WHERE `categoryID`=\"$categoryID\" ";
            $categoryData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $categoryData;
        }

        public function getAllCategories(){

            $sql = "SELECT * FROM `categories` ORDER BY `title` ASC";
            $categories = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $categories;
        }

        public function getActiveCategories(){

            $sql = "SELECT * FROM `categories` WHERE `isActive`=1 ORDER BY `title` ASC";
            $categories = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $categories;
        }  

        public function createCategory($title,$description,$img,$username){

            $time = time();
            $slug = strtolower(str_replace(" ", "-", $title));

            $sql = "INSERT INTO `categories` (`slug`,`title`,`description`,`image`,`createdAt`,`createdBy`) 
                VALUES (\"$slug\",\"$title\",\"$description\",\"$img\",\"$time\",\"$username\")
            ";

            echo $sql;

            $this->db->query($sql);
        }

        public function editCategory($categoryID,$title,$description,$username){

            $time = time();
            $slug = strtolower(str_replace(" ", "-", $title));

            $sql = "UPDATE `categories` SET `slug`=\"$slug\", `title`=\"$title\", `description`=\"$description\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `categoryID`=$categoryID";
            $this->db->query($sql);

        }

        public function toggleCategory($categoryID, $isActive, $username){

            $time = time();

            $sql = "UPDATE `categories` SET `isActive`=\"$isActive\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `categoryID`=\"$categoryID\" ";
            $this->db->query($sql);
        }


        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        //Subcategory

        public function checkIfSubcategoryExists($subcategoryID){

            $sql = "SELECT * FROM `subcategories` WHERE `subcategoryID`=\"$subcategoryID\" ";
            $data = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            if(!empty($data)){
                return true;
            }   
            else {
                return false;
            }

        }

        public function getSubcategory($subcategoryID){

            $sql = "SELECT * FROM `subcategories` WHERE `subcategoryID`=\"$subcategoryID\" ";
            $subcategory = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $subcategory;
        } 

        public function getAllSubcategories($categoryID){

            $sql = "SELECT * FROM `subcategories` WHERE `parentID`=\"$categoryID\" ORDER BY `title` ASC ";
            $subcategories = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $subcategories;
        }

        public function getActiveSubcategories($categoryID){

            $sql = "SELECT * FROM `subcategories` WHERE `parentID`=\"$categoryID\" AND `isActive`=1 ORDER BY `title` ASC ";
            $subcategories = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);

            return $subcategories;
        }

        public function createSubcategory($categoryID,$title,$description,$img,$username){

            $time = time();
            $slug = strtolower(str_replace(" ", "-", $title));

            $sql = "INSERT INTO `subcategories` (`slug`,`title`,`description`,`parentID`,`image`,`createdAt`,`createdBy`) 
                VALUES (\"$slug\",\"$title\",\"$description\",\"$categoryID\",\"$img\",\"$time\",\"$username\")
            ";

            $this->db->query($sql);
        }

        public function toggleSubcategory($subcategoryID, $isActive, $username){

            $time = time();

            $sql = "UPDATE `subcategories` SET `isActive`=\"$isActive\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `subcategoryID`=\"$subcategoryID\" ";
            $this->db->query($sql);

        }


        public function editSubCategory($subcategoryID,$title,$description, $username){

            $time = time();
            $slug = strtolower(str_replace(" ", "-", $title));

            $sql = "UPDATE `subcategories` SET `slug`=\"$slug\", `title`=\"$title\", `description`=\"$description\", `lastUpdatedBy`=\"$username\", `lastUpdatedAt`=\"$time\" WHERE `subcategoryID`=$subcategoryID";
            $this->db->query($sql);

        }



        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        //Count
        public function getProductCountCategory($categoryID){

            $sql = "SELECT COUNT(*) from `products` WHERE `categoryID`=\"$categoryID\" ";

            $count = $this->db->query($sql)->fetchColumn();
            return $count;
        }

    }   

?>