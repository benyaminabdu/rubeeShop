<?php

    use App\Controllers\PublicController;  

    use App\Middleware\Cart;

    //Landing 
    $app->get('/', PublicController::class.':landing')->setName('landing')->add(new Cart($container)); 

    $app->group('',function(){

        //Commerce
        $this->get('/categories', PublicController::class.':categories')->setName('categories');
        $this->get('/category/{categoryID}', PublicController::class.':category')->setName('category');
        $this->get('/category/subcategory/{subcategoryID}', PublicController::class.':subcategory')->setName('subcategory');

        //Search
        $this->get('/search', PublicController::class.':search')->setName('search');

        //Product Page
        $this->get('/product/{productID}', PublicController::class.':product')->setName('product');

        //Cart
        $this->get('/cart', PublicController::class.':cart')->setName('cart');
        $this->get('/addToCart/{productID}', PublicController::class.':addToCart')->setName('addToCart');
        $this->get('/buyNow/{productID}', PublicController::class.':buyNow')->setName('buyNow');

        //Cart Acc
        $this->post('/cartMinusItem',PublicController::class.':minusItem')->setName('cartMinusItem');
        $this->post('/cartPlusItem',PublicController::class.':plusItem')->setName('cartPlusItem');
        $this->post('/deleteFromCart',PublicController::class.':deleteFromCart')->setName('deleteFromCart');

        //Order
        $this->get('/order', PublicController::class.':order')->setName('order');
        $this->post('/order', PublicController::class.':generateOrder')->setName('order');
        
        //Payment
        $this->get('/success', PublicController::class.':success')->setName('success');
        $this->get('/failed', PublicController::class.':failed')->setName('failed');
        $this->get('/cancel', PublicController::class.':cancel')->setName('cancel');

        $this->post('/notify', PublicController::class.':notify')->setName('notify');


        /////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////
        //Common
        $this->get('/about', PublicController::class.':about')->setName('about');
        $this->get('/contact', PublicController::class.':contact')->setName('contact');
        $this->get('/terms', PublicController::class.':terms')->setName('terms');
        $this->get('/privacy', PublicController::class.':privacy')->setName('privacy');

    })->add(new Cart($container));

?>