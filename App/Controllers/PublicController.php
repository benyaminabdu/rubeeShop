<?php

    namespace App\Controllers;

    use App\Functions\Product;
    use App\Functions\Category;
    use App\Functions\Cart;
    use App\Functions\Order;
    use App\Functions\SantimPay;

    class PublicController extends Controller {

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Landing

        public function landing($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/landing.html',compact('data'));
        }


        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Categories

        public function categories($request,$response){

            $category = new Category($this->c->db);

            $categories = $category->getActiveCategories();

            foreach($categories as $thisCategory){
                $thisCategory->productCount = $category->getProductCountCategory($thisCategory->categoryID);
            }

            $data = array(
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/categories.html',compact('data'));
        }


        public function category($request,$response,$args){

            $categoryID = $args['categoryID'];

            if($categoryID == ""){
                return $response->withRedirect($this->c->router->pathFor('landing'));                
            }

            $category = new Category($this->c->db);
            $product = new Product($this->c->db);

            if($category->checkIfCategoryExists($categoryID)){

            }
            else {
                return $response->withRedirect($this->c->router->pathFor('landing'));                
            }

            $categoryData = $category->getCategory($categoryID);

            if($categoryData->isActive){ 

            } 
            else {
                return $response->withRedirect($this->c->router->pathFor('landing'));
            }

            $categories = $category->getActiveCategories();
            $subcategories = $category->getActiveSubcategories($categoryID);
            $products = $product->getProductsByCategory($categoryID); 

            $data = array(
                "category" => $categoryData,
                "categories" => $categories,
                "subcategories" => $subcategories,
                "products" => $products
            );

            return $this->c->view->render($response,'public/category.html',compact('data'));
        }


        public function subcategory($request,$response,$args){

            $subcategoryID = $args['subcategoryID'];

            if($subcategoryID == ""){
                return $response->withRedirect($this->c->router->pathFor('landing'));                
            }

            $category = new Category($this->c->db);
            $product = new Product($this->c->db);

            if($category->checkIfSubcategoryExists($subcategoryID)){

            }
            else {
                return $response->withRedirect($this->c->router->pathFor('landing'));                
            }

            $subcategoryData = $category->getSubcategory($subcategoryID);

            if($subcategoryData->isActive){ 

            } 
            else {
                return $response->withRedirect($this->c->router->pathFor('landing'));
            }

            $categories = $category->getActiveCategories();
            $subcategory = $category->getSubcategory($subcategoryID);
            $subcategory->parent = $category->getCategory($subcategory->parentID)->title;
            $products = $product->getProductsBySubcategory($subcategoryID); 

            $data = array(
                "subcategory" => $subcategory,
                "categories" => $categories,
                "products" => $products
            );

            return $this->c->view->render($response,'public/subcategory.html',compact('data'));
        }




        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Search

        public function search($request,$response){

            $search = $_GET['search'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $categories = $category->getActiveCategories();
            $products = $product->search($search);

            $data = array(
                "search" => $search,
                "categories" => $categories,
                "products" => $products
            );

            return $this->c->view->render($response,'public/search.html',compact('data'));
        }

        

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Product

        public function product($request,$response,$args){

            $productID = $args['productID'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            if($product->checkIfProductExists($productID)){

            }
            else {
                return $response->withRedirect($this->c->router->pathFor('landing'));
            }

            $productData = $product->getProduct($productID);

            if($productData->isActive){

            }
            else {
                return $response->withRedirect($this->c->router->pathFor('landing'));
            }

            $productData->category = $category->getCategory($productData->categoryID)->title;
            $productData->subcategory = $category->getSubcategory($productData->subcategoryID)->title;

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $messages = $this->c->flash->getMessages();

            $data = array(
                "product" => $productData,
                "categories" => $categories,
                "featuredProducts" => $featuredProducts,
                "messages" => $messages
            );

            return $this->c->view->render($response,'public/product.html',compact('data'));
        }




        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Buy Now
        public function buyNow($request,$response,$args){

            $cartID = $request->getAttribute('cartID');
            $productID = $args['productID'];
            $itmeCount = 1;

            $cart = new Cart($this->c->db);
            $cart->addToCart($cartID,$productID,$itmeCount);
            
            return $response->withRedirect($this->c->router->pathFor('cart'));
        }
        
        
        
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Cart

        public function addToCart($request,$response,$args){

            $cartID = $request->getAttribute('cartID');
            $productID = $args['productID'];
            $itmeCount = 1;

            $cart = new Cart($this->c->db);
            $cart->addToCart($cartID,$productID,$itmeCount);
            
            $this->c->flash->addMessage('cart', 'Product Added to Cart Succesfully');

            return $response->withRedirect($this->c->router->pathFor('product',['productID'=>$productID]));
        }


        public function cart($request,$response){

            $cartID = $request->getAttribute('cartID');

            $cart = new Cart($this->c->db);
            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $categories = $category->getActiveCategories();

            $cartCount = $cart->getCartCount($cartID);
            $cartData = $cart->getCurrentCartData($cartID);
            $totalPrice = $cart->calculatePriceFromCart($cartData);

            foreach($cartData as $cartItem){
                $cartItem->product = $product->getProduct($cartItem->productID);
            }

            $data = array(
                "categories" => $categories,
                "cartCount" => $cartCount,
                "cartData" => $cartData,
                "totalPrice" => $totalPrice,
            );

            return $this->c->view->render($response,'public/cart.html',compact('data'));
        }


        public function minusItem($request,$response){

            $cartID = $request->getParam('cartID');
            $productID = $request->getParam('productID');

            $cart =  new Cart($this->c->db);
            $itemCount = $cart->getItemCount($cartID,$productID);

            if($itemCount == "1"){
                $cart->removeItem($cartID,$productID);
            }
            else {
                $itemCount = $itemCount-1;
                $cart->setItemCount($cartID,$productID,$itemCount);
            }

            return $response->withRedirect($this->c->router->pathFor('cart'));
        }


        public function plusItem($request,$response){

            $cartID = $request->getParam('cartID');
            $productID = $request->getParam('productID');

            $cart =  new Cart($this->c->db);
            $itemCount = $cart->getItemCount($cartID,$productID);

            $itemCount = $itemCount + 1;
            $cart->setItemCount($cartID,$productID,$itemCount);

            return $response->withRedirect($this->c->router->pathFor('cart'));
        }


        public function deleteFromCart($request,$response){

            $cartID = $request->getParam('cartID');
            $productID = $request->getParam('productID');

            $cart = new Cart($this->c->db);
            $cart->removeItem($cartID,$productID);

            return $response->withRedirect($this->c->router->pathFor('cart'));
        }



        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Order

        public function order($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/order.html',compact('data'));
        }


        public function generateOrder($request,$response){

            $orderID = bin2hex(openssl_random_pseudo_bytes(4))."-".bin2hex(openssl_random_pseudo_bytes(2))."-".bin2hex(openssl_random_pseudo_bytes(2))."-".bin2hex(openssl_random_pseudo_bytes(2))."-".bin2hex(openssl_random_pseudo_bytes(6));
            $cartID = $request->getAttribute('cartID');

            $firstName = $request->getParam('firstName');
            $lastName = $request->getParam('lastName');
            $phone = $request->getParam('phone');
            $email = $request->getParam('email');
            $deliveryType = $request->getParam('deliveryType');
            $selectedStore = $request->getParam('selectedStore');

            $cart = new Cart($this->c->db);
            $cartData = $cart->getCurrentCartData($cartID);
            $cartPrice = $cart->calculatePriceFromCart($cartData);

            $order = new Order($this->c->db);
            $order->generateOrder($orderID,$cartData,$firstName,$lastName,$phone,$email,$deliveryType,$selectedStore);

            $paymentRef = $orderID;

            $PRIVATE_KEY_IN_PEM = "-----BEGIN EC PRIVATE KEY-----\nMHcCAQEEIOvGaJLgB9vBAPkQs/OR7F3o2spBeVYQ0amdzueWcgpaoAoGCCqGSM49\nAwEHoUQDQgAEa3FUF8DTC3raS4/Zv6QDCDDvhdbYTj7UMy3Lg3cFztOT8/dfQOkU\nFWzxqr9dd3/b0mtf2lOt+ToHC+XPP0fj3Q==\n-----END EC PRIVATE KEY-----";
            $GATEWAY_MERCHANT_ID = 'e60c25d8-d302-430a-aa5b-d731c3066d4d';

            $santimPay =  new SantimPay($GATEWAY_MERCHANT_ID, $PRIVATE_KEY_IN_PEM);
            
            $successRedirectUrl = 'https://rubee.et/success';
            $failureRedirectUrl = 'https://rubee.et/failed';

            $notifyUrl = 'https://rubee.et/notify';
            $cancelRedirectUrl = 'https://rubee.et/cancel';

            $urlData = $santimPay->generatePaymentURL($paymentRef, $cartPrice, 'Rubee Store Order', $successRedirectUrl, $failureRedirectUrl, $notifyUrl, $cancelRedirectUrl);

            $data = json_decode($urlData, true);
            $url = $data['url'];

            return $response->withStatus(302)->withHeader('Location', $url);
        }



        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Payment
        public function success($request,$response){

            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/success.html',compact('data'));
        }

        public function failed($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/failed.html',compact('data'));
        }

        public function cancel($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/cancel.html',compact('data'));
        }

        public function notify($request,$response){

            $status = $request->getParam('Status');
            $uuid = $request->getParam('thirdPartyId');
            $transactionId = $request->getParam('txnId');

            if($status == "COMPLETED"){

                $sql = "UPDATE `orders` SET `isCompleted`=1, `isPaid`=1, `paidWith`=\"SantimPay\", `transactionID`=\"$transactionId\" WHERE `orderID`=\"$uuid\" ";
                $this->c->db->query($sql);
                
            }
            else {
                //Nothing
            }
            
        }





        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Common

        public function about($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/about.html',compact('data'));
        }


        public function contact($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/contact.html',compact('data'));
        }


        public function terms($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/terms.html',compact('data'));
        }


        public function privacy($request,$response){

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            $featuredProducts = $product->getRandomProducts(6);
            $categories = $category->getActiveCategories();

            $data = array(
                "featuredProducts" => $featuredProducts,
                "categories" => $categories
            );

            return $this->c->view->render($response,'public/privacy.html',compact('data'));
        }



    }

?>