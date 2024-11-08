<?php

    namespace App\Functions;
    use PDO;

    class  Order {
        
        public $db;

        public function __construct($db){
            $this->db = $db;
        }

        public function getOrder($orderID){
            $sql = "SELECT * FROM `orders` WHERE `orderID`=\"$orderID\" ";
            $orderData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);
            return $orderData;
        }

        public function getPendingOrders(){
            $sql = "SELECT * FROM `orders` WHERE `isCompleted`=1 AND `isFullfilled`=0 ";
            $pendingOrders = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
            
            return $pendingOrders;
        }

        public function getFulfilledOrders(){
            $sql = "SELECT * FROM `orders` WHERE `isCompleted`=1 AND `isFullfilled`=1 ";
            $pendingOrders = $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
            
            return $pendingOrders;
        }

        public function generateOrder($orderID,$cartData,$firstName,$lastName,$phone,$email,$deliveryType,$selectedStore){

            $price = $this->calculatePriceFromCart($cartData);
            $orderDetail = $this->getOrderDetail($cartData);
            $timestamp = time();

            $sql = "INSERT INTO `orders` (`orderID`,`firstName`,`lastName`,`phone`,`email`,`orderDetail`,`deliveryType`,`selectedStore`,`amount`,`orderedTime`) 
                VALUES (\"$orderID\",\"$firstName\",\"$lastName\",\"$phone\",\"$email\",\"$orderDetail\",\"$deliveryType\",\"$selectedStore\",\"$price\",\"$timestamp\")
            ";

            $this->db->query($sql);
        }

        public function calculatePriceFromCart($cartData){
            
            $price = 0;
            
            foreach($cartData as $cartItem){
                $productPrice = $this->getProductPrice($cartItem->productID);
                $priceMultiplied =  $productPrice * $cartItem->itemCount;
                
                $price = $price + $priceMultiplied;
            }            
            
            return $price;
        }

        public function getProductPrice($productID){

            $sql = "SELECT * FROM `products` WHERE `productID`=\"$productID\" ";
            $productData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);

            return $productData->price;
        }

        public function getOrderDetail($cartData){

            $orderDetail = "";

            foreach($cartData as $cartItem){
                                
                $productName = $this->getProductData($cartItem->productID)->title;
                $itemCount = $cartItem->itemCount;

                $formattedText = "Product Name - <b>$productName</b>  <br /> Item(s) - $itemCount <br /> ";
                $orderDetail = $orderDetail.$formattedText;
            }

            return $orderDetail;   
        }

        public function getProductData($productID){
            $sql = "SELECT * FROM `products` WHERE `productID`=\"$productID\" ";
            $productData = $this->db->query($sql)->fetch(PDO::FETCH_OBJ);
            return $productData;
        }

        public function fulfillOrder($orderID){

            $sql = "UPDATE `orders` SET `isFullfilled`=1 WHERE `orderID`=\"$orderID\" ";
            $this->db->query($sql);

        }


    }

?>