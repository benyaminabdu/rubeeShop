<?php

    namespace App\Controllers;

    use App\Functions\Category;
    use App\Functions\Order;
    use App\Functions\Product;
    use App\Functions\Variance;

    class AdminController extends Controller {

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Dashboard

        public function dashboard($request,$response){
            
            $username = $request->getAttribute('username');

            $data = array(
                "username" => $username
            );

            return $this->c->view->render($response,'admin/dashboard.html',compact('data'));
        }


        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Categories

        public function categories($request,$response){

            $username = $request->getAttribute('username');

            $category = new Category($this->c->db);
            $categories = $category->getAllCategories();            

            $data = array(
                "username" => $username,
                "categories" => $categories
            );

            return $this->c->view->render($response,'admin/categories.html',compact('data'));
        }


        public function createCategory($request,$response){

            $username = $request->getAttribute('username');

            $data = array(
                "username" => $username,
            );

            return $this->c->view->render($response,'admin/createCategory.html',compact('data'));
        }


        public function categoryCreator($request,$response){
            
            $username = $request->getAttribute('username');

            $title = $request->getParam('title');
            $description = $request->getParam('description');
            $img = bin2hex(openssl_random_pseudo_bytes(16));

            $uploader = new \Verot\Upload\Upload($_FILES['img']);

            if($uploader->uploaded){
                $uploader->file_new_name_body = $img;
                $uploader->image_resize = true;
                $uploader->image_convert = 'jpg';
                $uploader->image_x = 500;
                $uploader->image_ratio_y = true;
                $uploader->process('images/');
                if($uploader->processed){
                    $uploader->clean();
                }
                else {}
            }

            $category = new Category($this->c->db);
            $category->createCategory($title,$description,$img,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.categories'));
        }


        public function categoryDetails($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];

            $category = new Category($this->c->db);

            if($category->checkIfCategoryExists($categoryID)){
                $categoryData = $category->getCategory($categoryID);

                $categoryData->createdAt = date('d/m/Y', $categoryData->createdAt);
                $categoryData->lastUpdatedAt = date('d/m/Y', $categoryData->lastUpdatedAt);

                $data = array(
                    "username" => $username,
                    "category" => $categoryData
                );

                return $this->c->view->render($response,'admin/categoryDetails.html',compact('data'));
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }
        }


        public function editCategory($request,$response){

            $username = $request->getAttribute('username');

            $categoryID = $request->getParam('categoryID');
            $title = $request->getParam('title');
            $description = $request->getParam('description');

            $category = new Category($this->c->db);
            $category->editCategory($categoryID,$title,$description,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.categories.details',["categoryID" => $categoryID]));
        }


        public function toggleCategory($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];

            $category = new Category($this->c->db);

            if($category->checkIfCategoryExists($categoryID)){
                $categoryData = $category->getCategory($categoryID);

                if($categoryData->isActive){
                    $category->toggleCategory($categoryID, 0, $username);
                }
                else {
                    $category->toggleCategory($categoryID, 1, $username);
                }

                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }            
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }
        }


        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Subcategories

        public function subcategories($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];

            $category = new Category($this->c->db);
           
            if($category->checkIfCategoryExists($categoryID)){
                $categoryData = $category->getCategory($categoryID);
                $categoryData->createdAt = date('d/m/Y', $categoryData->createdAt);
                
                $subcategories = $category->getAllSubcategories($categoryID);

                $data = array(
                    "username" => $username,
                    "category" => $categoryData,
                    "subcategories" => $subcategories
                );

                return $this->c->view->render($response,'admin/subcategories.html',compact('data'));
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }

            $data = array(
                "username" => $username,
            );

            return $this->c->view->render($response,'admin/subcategories.html',compact('data'));
        }


        public function createSubcategory($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];

            $category = new Category($this->c->db);

            if($category->checkIfCategoryExists($categoryID)){

                $categoryData = $category->getCategory($categoryID);
                $categoryData->createdAt = date('d/m/Y', $categoryData->createdAt);   
                
                $data = array(
                    "username" => $username,
                    "category" => $categoryData,
                );

                return $this->c->view->render($response,'admin/createSubcategory.html',compact('data'));
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }  
        }


        public function subcategoryCreator($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];

            $title = $request->getParam('title');
            $description = $request->getParam('description');

            $img = bin2hex(openssl_random_pseudo_bytes(16));

            $uploader = new \Verot\Upload\Upload($_FILES['img']);

            if($uploader->uploaded){
                $uploader->file_new_name_body = $img;
                $uploader->image_resize = true;
                $uploader->image_convert = 'jpg';
                $uploader->image_x = 500;
                $uploader->image_ratio_y = true;
                $uploader->process('images/');
                if($uploader->processed){
                    $uploader->clean();
                }
                else {}
            }

            $category = new Category($this->c->db);
            $category->createSubcategory($categoryID,$title,$description,$img,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.categories.subcategories',['categoryID' => $categoryID]));
        }


        public function toggleSubcategory($request,$response,$args){

            $username = $request->getAttribute('username');
            $subcategoryID = $args['subcategoryID'];

            $category = new Category($this->c->db);

            if($category->checkIfSubcategoryExists($subcategoryID)){
                $subcategoryData = $category->getSubcategory($subcategoryID);

                if($subcategoryData->isActive){
                    $category->toggleSubcategory($subcategoryID, 0, $username);
                }
                else {
                    $category->toggleSubcategory($subcategoryID, 1, $username);
                }

                return $response->withRedirect($this->c->router->pathFor('admin.categories.subcategories',["categoryID" => $subcategoryData->parentID, "subcategoryID" => $subcategoryData->subcategoryID ]));
            }            
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }
        }


        public function subcategoryDetails($request,$response,$args){

            $username = $request->getAttribute('username');

            $categoryID = $args['categoryID'];
            $subcategoryID = $args['subcategoryID'];   
            
            $category = new Category($this->c->db);
            
            if($category->checkIfSubcategoryExists($subcategoryID)){

                $subcategoryData = $category->getSubcategory($subcategoryID);
                $subcategoryData->createdAt = date('d/m/Y', $subcategoryData->createdAt);
                $subcategoryData->lastUpdatedAt = date('d/m/Y', $subcategoryData->lastUpdatedAt);

                $categoryData = $category->getCategory($subcategoryData->parentID);

                $data = array(
                    "username" => $username,
                    "subcategory" => $subcategoryData,
                    "category" => $categoryData
                );

                return $this->c->view->render($response,'admin/subcategoryDetails.html',compact('data'));
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.categories'));
            }

        }


        public function editSubcategory($request,$response){

            $username = $request->getAttribute('username');

            $subcategoryID = $request->getParam('categoryID');
            $title = $request->getParam('title');
            $description = $request->getParam('description');

            $category = new Category($this->c->db);
            $subcategoryData = $category->getSubcategory($subcategoryID);
            $category->editSubCategory($subcategoryID,$title,$description, $username);

            return $response->withRedirect($this->c->router->pathFor('admin.categories.subcategory.details',["categoryID" => $subcategoryData->parentID, "subcategoryID" => $subcategoryID]));
        }


        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Products

        public function products($request,$response){

            $username = $request->getAttribute('username');

            $product = new Product($this->c->db);
            $products = $product->getAllProducts();

            $category = new Category($this->c->db);

            foreach($products as $product){
                $product->category = $category->getCategory($product->categoryID)->title;
            }

            $data = array(
                "username" => $username,
                "products" => $products
            );

            return $this->c->view->render($response,'admin/products.html',compact('data'));

        }


        public function createProduct($request,$response){

            $username = $request->getAttribute('username');

            $category = new Category($this->c->db);
            $categories = $category->getActiveCategories();

            foreach($categories as $category){
                $category->description = strlen($category->description) > 30 ? substr($category->description, 0, 30) . "..." : $category->description;
            }

            $data = array(
                "username" => $username,
                "categories" => $categories
            );

            return $this->c->view->render($response,'admin/createProduct.html',compact('data'));

        }


        public function createProductII($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];

            $category = new Category($this->c->db);

            if($category->checkIfCategoryExists($categoryID)){

            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.dashboard'));
            }

            $subcategories = $category->getActiveSubcategories($categoryID);

            foreach($subcategories as $subcategory){
                $subcategory->description = strlen($subcategory->description) > 30 ? substr($subcategory->description, 0, 30) . "..." : $subcategory->description;
            }

            $data = array(
                "username" => $username,
                "categoryID" => $categoryID,
                "subcategories" => $subcategories
            );

            return $this->c->view->render($response,'admin/createProductII.html',compact('data'));

        }



        public function createProductIII($request,$response,$args){

            $username = $request->getAttribute('username');
            $categoryID = $args['categoryID'];
            $subcategoryID = $args['subcategoryID'];

            $category = new Category($this->c->db);

            
            if($category->checkIfCategoryExists($categoryID)){
                if(($subcategoryID == "skip") || ($category->checkIfSubcategoryExists($subcategoryID))){

                }
                else{
                    return $response->withRedirect($this->c->router->pathFor('admin.dashboard'));
                }
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.dashboard'));
            }

            $data = array(
                "username" => $username,
                "categoryID" => $categoryID,
                "subcategoryID" => $subcategoryID
            );

            return $this->c->view->render($response,'admin/createProductIII.html',compact('data'));
        }


        public function createProductIV($request,$response,$args){

            $username = $request->getAttribute('username');

            $title = $request->getParam('title');
            $description = $request->getParam('description');
            $categoryID = $request->getParam('categoryID');
            $subcategoryID = $request->getParam('subcategoryID');

            $img = bin2hex(openssl_random_pseudo_bytes(16));

            $uploader = new \Verot\Upload\Upload($_FILES['img']);

            if($uploader->uploaded){
                $uploader->file_new_name_body = $img;
                $uploader->image_resize = true;
                $uploader->image_convert = 'jpg';
                $uploader->image_x = 500;
                $uploader->image_ratio_y = true;
                $uploader->process('images/');
                if($uploader->processed){
                    $uploader->clean();
                }
                else {}
            }

            $product = new Product($this->c->db);
            $productID = $product->createProduct($title,$categoryID,$subcategoryID,$description,$img,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.products.details',["productID"=> $productID]));
        }


        public function toggleProduct($request,$response,$args){

            $username = $request->getAttribute('username');
            $productID = $args['productID'];

            $product = new Product($this->c->db);

            if($product->checkIfProductExists($productID)){
                $productData = $product->getProduct($productID);

                if($productData->isActive){
                    $product->toggleProduct($productID, 0, $username);
                }
                else {
                    $product->toggleProduct($productID, 1, $username);
                }

                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }            
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }
        }


        public function productDetails($request,$response,$args){

            $username = $request->getAttribute('username');
            $productID = $args['productID'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            if($product->checkIfProductExists($productID)){
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }

            $productData = $product->getProduct($productID);
            $productData->category = $category->getCategory($productData->categoryID);
            $productData->subcategory = $category->getSubcategory($productData->subcategoryID);
            $productData->createdAt = date('d/m/Y', $productData->createdAt);
            $productData->lastUpdatedAt = date('d/m/Y', $productData->lastUpdatedAt);

            $categories = $category->getAllCategories();
            $subcategories = $category->getAllSubcategories($productData->categoryID);

            $data = array(
                "username" => $username,
                "product" => $productData,
                "categories" => $categories,
                "subcategories" => $subcategories
            );

            return $this->c->view->render($response,'admin/productDetails.html',compact('data'));   
        }


        public function editProduct($request,$response,$args){

            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');
            $title = $request->getParam('title');
            $description = $request->getParam('description');

            $product = new Product($this->c->db);
            $product->editProduct($productID,$title,$description,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.products.details',["productID" => $productID]));
        }


        public function changeCategory($request,$response){

            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');
            $categoryID = $request->getParam('categoryID');

            $product = new Product($this->c->db);
            $product->auditUpdate($productID,$username);
            $product->setCategory($productID,$categoryID);

            return $response->withRedirect($this->c->router->pathFor('admin.products.details',["productID" => $productID]));
        }
        
        
        public function changeSubcategory($request,$response){

            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');
            $subcategoryID = $request->getParam('subcategoryID');

            $product = new Product($this->c->db);
            $product->auditUpdate($productID,$username);
            $product->setSubcategory($productID,$subcategoryID);

            return $response->withRedirect($this->c->router->pathFor('admin.products.details',["productID" => $productID]));
        }


        public function productPrice($request,$response,$args){

            $username = $request->getAttribute('username');
            $productID = $args['productID'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            if($product->checkIfProductExists($productID)){
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }

            $productData = $product->getProduct($productID);
            $productData->category = $category->getCategory($productData->categoryID);
            $productData->subcategory = $category->getSubcategory($productData->subcategoryID);
            $productData->createdAt = date('d/m/Y', $productData->createdAt);
            $productData->lastUpdatedAt = date('d/m/Y', $productData->lastUpdatedAt);


            $data = array(
                "username" => $username,
                "product" => $productData
            );

            return $this->c->view->render($response,'admin/productPrice.html',compact('data'));   
        }


        public function changePrice($request,$response){

            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');
            $price = $request->getParam('price');

            $product = new Product($this->c->db);
            $product->editPrice($productID,$price,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.products.price',["productID" => $productID]));
        }


        public function productInventory($request,$response,$args){

            $username = $request->getAttribute('username');
            $productID = $args['productID'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            if($product->checkIfProductExists($productID)){
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }

            $productData = $product->getProduct($productID);
            $productData->category = $category->getCategory($productData->categoryID);
            $productData->subcategory = $category->getSubcategory($productData->subcategoryID);
            $productData->createdAt = date('d/m/Y', $productData->createdAt);
            $productData->lastUpdatedAt = date('d/m/Y', $productData->lastUpdatedAt);


            $data = array(
                "username" => $username,
                "product" => $productData
            );

            return $this->c->view->render($response,'admin/productInventory.html',compact('data'));   
        }


        public function changeInventory($request,$response){

            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');
            $inventory = $request->getParam('inventory');

            $product = new Product($this->c->db);
            $product->editInventory($productID,$inventory,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.products.inventory',["productID" => $productID]));
        }


        public function productMedia($request,$response,$args){

            $username = $request->getAttribute('username');
            $productID = $args['productID'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);

            if($product->checkIfProductExists($productID)){
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }

            $productData = $product->getProduct($productID);
            $productData->category = $category->getCategory($productData->categoryID);
            $productData->subcategory = $category->getSubcategory($productData->subcategoryID);
            $productData->createdAt = date('d/m/Y', $productData->createdAt);
            $productData->lastUpdatedAt = date('d/m/Y', $productData->lastUpdatedAt);

            $productImages = $product->getProductImages($productID);

            $data = array(
                "username" => $username,
                "product" => $productData,
                "productImages" => $productImages
            );

            return $this->c->view->render($response,'admin/productMedia.html',compact('data'));
        }


        public function changeImage($request,$response){

            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');

            $img = bin2hex(openssl_random_pseudo_bytes(16));
            $uploader = new \Verot\Upload\Upload($_FILES['img']);

            if($uploader->uploaded){
                $uploader->file_new_name_body = $img;
                $uploader->image_resize = true;
                $uploader->image_convert = 'jpg';
                $uploader->image_x = 500;
                $uploader->image_ratio_y = true;
                $uploader->process('images/');
                if($uploader->processed){
                    $uploader->clean();
                }
                else {}
            }


            $product = new Product($this->c->db);
            $product->editPicture($productID,$img,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.products.media',["productID" => $productID]));            
        }


        public function addProductImage($request,$response){

            $username = $request->getAttribute('username');
            $productID = $request->getParam('productID');

            $product = new Product($this->c->db);

            $count = count(array_filter($_FILES['image']['name']));
            $images = array();

            for ($i=0; $i<$count; $i++){
                $name = $_FILES['image']['name'][$i];
                $type = $_FILES['image']['type'][$i];
                $tmp_name = $_FILES['image']['tmp_name'][$i];
                $error = $_FILES['image']['error'][$i];
                $size = $_FILES['image']['size'][$i];
                
                $image = array(
                    "name" => $name,
                    "type" => $type,
                    "tmp_name" => $tmp_name,
                    "error" => $error,
                    "size" => $size
                );   


                $img = bin2hex(openssl_random_pseudo_bytes(24));
                $foo = new \Verot\Upload\Upload($image);
                if($foo->uploaded){
                    $foo->file_new_name_body = $img;
                    $foo->image_resize = true;
                    $foo->image_convert = 'jpg';
                    $foo->image_x = 400;
                    $foo->image_ratio_y = true;
                    $foo->process('images/');
                    if ($foo->processed) {
                        //
                    } else {
                        //echo 'error : ' . $foo->error;
                        die("Img Controller Error, 500");
                    }
                }
                else {
                    echo "Error";
                }

                $product->addImage($productID, $img, $username);
            }

            return $response->withRedirect($this->c->router->pathFor('admin.products.media',["productID" => $productID]));            
        }


        public function deleteImage($request,$response,$args){
            
            $username = $request->getAttribute('username');

            $productID = $args['productID'];
            $imageID = $args['imageID'];

            $product = new Product($this->c->db);
            $product->deleteImage($productID,$imageID,$username);

            return $response->withRedirect($this->c->router->pathFor('admin.products.media',["productID" => $productID]));            
        }


        public function productVariance($request,$response,$args){

            $username = $request->getAttribute('username');
            $productID = $args['productID'];

            $product = new Product($this->c->db);
            $category = new Category($this->c->db);
            $variance = new Variance($this->c->db);

            if($product->checkIfProductExists($productID)){
            }
            else {
                return $response->withRedirect($this->c->router->pathFor('admin.products'));
            }

            $productData = $product->getProduct($productID);
            $productData->category = $category->getCategory($productData->categoryID);
            $productData->subcategory = $category->getSubcategory($productData->subcategoryID);
            $productData->createdAt = date('d/m/Y', $productData->createdAt);
            $productData->lastUpdatedAt = date('d/m/Y', $productData->lastUpdatedAt);

            $variance = new Variance($this->c->db);
            $variances = $variance->getVariances($productID);

            $data = array(
                "username" => $username,
                "product" => $productData,
                "variances" => $variances
            );

            return $this->c->view->render($response,'admin/productVariance.html',compact('data'));
        }


        public function addVariance($request,$response){
            
            $username = $request->getAttribute('username');

            $productID = $request->getParam('productID');
            $varianceName = $request->getParam('variance');
            $variances = $request->getParam('variances');

            $product = new Product($this->c->db);
            $variance = new Variance($this->c->db);

            $product->auditUpdate($productID,$username);
            $variance->addVariance($productID,$varianceName,$variances);

            return $response->withRedirect($this->c->router->pathFor('admin.products.variance',["productID" => $productID]));            
        }


        public function deleteVariance($request,$response,$args){
            
            $username = $request->getAttribute('username');

            $productID = $args['productID'];
            $varianceID = $args['varianceID'];

            $product = new Product($this->c->db);
            $variance = new Variance($this->c->db);

            $product->auditUpdate($productID,$username);
            $variance->deleteVariance($varianceID);

            return $response->withRedirect($this->c->router->pathFor('admin.products.variance',["productID" => $productID]));            
        }



        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Orders

        public function orders($request,$response){
            
            $username = $request->getAttribute('username');

            $order = new Order($this->c->db);
            $orders = $order->getPendingOrders();

            $data = array(
                "username" => $username,
                "orders" => $orders
            );

            return $this->c->view->render($response,'admin/ordersPending.html',compact('data'));
        }


        public function ordersFulfilled($request,$response){
            
            $username = $request->getAttribute('username');

            $order = new Order($this->c->db);
            $orders = $order->getFulfilledOrders();

            $data = array(
                "username" => $username,
                "orders" => $orders
            );

            return $this->c->view->render($response,'admin/ordersFulfilled.html',compact('data'));
        }


        public function orderDetails($request,$response,$args){
            
            $username = $request->getAttribute('username');
            $orderID = $args['orderID'];

            $order = new Order($this->c->db);
            $orderData = $order->getOrder($orderID);
            $orderData->orderedTime = date('M d, Y', $orderData->orderedTime);

            $data = array(
                "username" => $username,
                "order" => $orderData
            );

            return $this->c->view->render($response,'admin/orderDetails.html',compact('data'));        
        }


        public function fullfillOrder($request,$response,$args){
            
            $username = $request->getAttribute('username');
            $orderID = $args['orderID'];

            $order = new Order($this->c->db);
            $order->fulfillOrder($orderID);

            return $response->withRedirect($this->c->router->pathFor('admin.orders.details',['orderID' => $orderID]));
        }
            

    }

?>