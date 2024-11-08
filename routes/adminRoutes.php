<?php

    namespace App\Controllers;

    use App\Middleware\AdminAuth;

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Admin Routes

    $app->group('/admin', function(){ 

        //Dashboard
        $this->get('', AdminController::class.':dashboard')->setName('admin.dashboard');

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Categories
        $this->get('/categories', AdminController::class.':categories')->setName('admin.categories');
        
        $this->get('/createCategory', AdminController::class.':createCategory')->setName('admin.createCategory');
        $this->post('/createCategory', AdminController::class.':categoryCreator')->setName('admin.createCategory');

        $this->get('/categories/details/{categoryID}', AdminController::class.':categoryDetails')->setName('admin.categories.details');

        $this->post('/categories/edit', AdminController::class.':editCategory')->setName('admin.categories.edit');
        $this->get('/categories/toggle/{categoryID}', AdminController::class.':toggleCategory')->setName('admin.categories.toggle');

        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Subcategories
        $this->get('/categories/{categoryID}/subCategories', AdminController::class.':subCategories')->setName('admin.categories.subcategories');
        
        $this->get('/categories/{categoryID}/subCategories/create', AdminController::class.':createSubcategory')->setName('admin.categories.createSubcategory');
        $this->post('/categories/{categoryID}/subCategories/create', AdminController::class.':subcategoryCreator')->setName('admin.categories.createSubcategory');

        $this->get('/categories/{categoryID}/subcategory/details/{subcategoryID}', AdminController::class.':subcategoryDetails')->setName('admin.categories.subcategory.details');
        $this->post('/subcategories/edit', AdminController::class.':editSubcategory')->setName('admin.subcategories.edit');
        
        $this->get('/subcategory/toggle/{subcategoryID}', AdminController::class.':toggleSubcategory')->setName('admin.subcategory.toggle');


        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Products
        $this->get('/products', AdminController::class.':products')->setName('admin.products');

        $this->get('/products/create', AdminController::class.':createProduct')->setName('admin.products.create');
        $this->get('/products/createII/{categoryID}', AdminController::class.':createProductII')->setName('admin.products.createII');
        $this->get('/products/createIII/{categoryID}/{subcategoryID}', AdminController::class.':createProductIII')->setName('admin.products.createIII');        
        $this->post('/products/createIV', AdminController::class.':createProductIV')->setName('admin.products.createIV');

        $this->get('/products/details/{productID}', AdminController::class.':productDetails')->setName('admin.products.details');
        $this->get('/products/price/{productID}', AdminController::class.':productPrice')->setName('admin.products.price');
        $this->get('/products/inventory/{productID}', AdminController::class.':productInventory')->setName('admin.products.inventory');
        $this->get('/products/media/{productID}', AdminController::class.':productMedia')->setName('admin.products.media');
        $this->get('/products/variance/{productID}', AdminController::class.':productVariance')->setName('admin.products.variance');
        
        $this->post('/products/edit', AdminController::class.':editProduct')->setName('admin.products.edit');
        $this->post('/products/changeCategory', AdminController::class.':changeCategory')->setName('admin.products.changeCategory');
        $this->post('/products/changeSubcategory', AdminController::class.':changeSubcategory')->setName('admin.products.changeSubcategory');
        $this->post('/products/changePrice', AdminController::class.':changePrice')->setName('admin.products.changePrice');
        $this->post('/products/changeInventory', AdminController::class.':changeInventory')->setName('admin.products.changeInventory');
        $this->post('/products/changeImage', AdminController::class.':changeImage')->setName('admin.products.changeImage');
        $this->post('/products/addProductImage', AdminController::class.':addProductImage')->setName('admin.products.addProductImage');
        $this->get('/products/deleteImage/{productID}/{imageID}', AdminController::class.':deleteImage')->setName('admin.products.deleteImage');
        $this->post('/products/addVariance', AdminController::class.':addVariance')->setName('admin.products.variance.add');
        $this->get('/products/deleteVariance/{productID}/{varianceID}', AdminController::class.':deleteVariance')->setName('admin.products.variance.delete');


        $this->get('/product/toggle/{productID}', AdminController::class.':toggleProduct')->setName('admin.products.toggle');



        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Orders
        $this->get('/ordersPending',AdminController::class.':orders')->setName('admin.orders.pending');
        $this->get('/ordersFulfilled',AdminController::class.':ordersFulfilled')->setName('admin.orders.fulfilled');

        $this->get('/ordersDetails/{orderID}',AdminController::class.':orderDetails')->setName('admin.orders.details');

        $this->get('/order/fulfillOrder/{orderID}',AdminController::class.':fullfillOrder')->setName('admin.orders.fulfillOrder');


    })->add(new AdminAuth($container));

?>