<?php

    namespace App\Functions;
    use PDO;

    class  Cart {
        
        public $db;

        public function __construct($db){
            $this->db = $db;
        }

        public function addToCart($cartID,$productID,$itemCount){

            if($this->checkIfProuctExistsInCart($productID,$cartID)){
                $productCount = $this->getProductCount($cartID,$productID);
                $newCount = $productCount+$itemCount;
                $sql = "UPDATE `cart` SET `itemCount`=\"$newCount\" WHERE `productID`=\"$productID\" ";
                $this->db->query($sql);
            }
            else {
                $sql = "INSERT INTO `cart` (`cartID`,`productID`,`itemCount`) VALUES (\"$cartID\",\"$productID\",\"$itemCount\")";
                $this->db->query($sql);
            }        
        }

        public function checkIfProuctExistsInCart($productID,$cartID){

            $sql = "SELECT * FROM `cart` WHERE `cartID`=\"$cartID\" AND `productID`=\"$productID\" ";
            $data = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            if(!empty($data)){
                return true;
            }   
            else {
                return false;
            }
        }

        public function getProductCount($cartID,$productID){

            $sql = "SELECT * FROM `cart` WHERE `cartID`=\"$cartID\" AND `productID`=\"$productID\" ";
            $cartData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);
            
            return $cartData->itemCount;
        }

        public function getCartCount($cartID){

            $sql = "SELECT SUM(itemCount) AS cartCount FROM `cart` WHERE `cartID`=\"$cartID\"";
            $cartCount = $this->db->query($sql)->fetch(PDO::FETCH_OBJ)->cartCount;

            return $cartCount;
        }

        public function getCurrentCartData($cartID){

            $sql = "SELECT * FROM `cart` WHERE `cartID`=\"$cartID\" ";
            $cartData = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
            
            return $cartData;
        }

        public function calculatePriceFromCart($cartData){

            $price = 0;

            foreach($cartData as $cartItem){
                $productID = $cartItem->productID;
                $itemCount = $cartItem->itemCount;
                $productPrice = $this->getProductPrice($productID);
                $priceMultiplied =  $productPrice * $itemCount;
                $price = $price + $priceMultiplied;
            }        

            return $price;
        }

        public function getProductPrice($productID){
            
            $sql = "SELECT * FROM `products` WHERE `productID`=\"$productID\" ";
            $productData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $productData->price;
        }

        public function getItemCount($cartID,$productID){

            $sql = "SELECT * FROM `cart` WHERE `cartID`=\"$cartID\" AND `productID`=\"$productID\" ";
            $count = $this->db->query($sql)->fetch(PDO::FETCH_OBJ)->itemCount;
            
            return $count;
        }

        public function setItemCount($cartID,$productID,$itemCount){
            
            $sql = "UPDATE `cart` SET `itemCount`=\"$itemCount\" WHERE `cartID`=\"$cartID\" AND `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

        public function removeItem($cartID,$productID){
            
            $sql = "DELETE FROM `cart` WHERE `cartID` = \"$cartID\" AND `productID`=\"$productID\" ";
            $this->db->query($sql);
        }

    }

?>