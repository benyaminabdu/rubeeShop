<?php

    namespace App\Middleware;

    use Psr\Container\ContainerInterface;
    use PDO;

    class Cart {
        protected $c;

        public function __construct(ContainerInterface $c){
            $this->c = $c; 
        }

        public function __invoke ($request,$response,$next){

            if(isset($_COOKIE['cart'])){
                $cartID = $_COOKIE['cart'];
            }
            else {
                $cartID = bin2hex(openssl_random_pseudo_bytes(4))."-".bin2hex(openssl_random_pseudo_bytes(2))."-".bin2hex(openssl_random_pseudo_bytes(2))."-".bin2hex(openssl_random_pseudo_bytes(2))."-".bin2hex(openssl_random_pseudo_bytes(6));
                setcookie('cart',$cartID,time() + (86400 * 30),"/");
            }

            $request = $request->withAttribute('cartID',$cartID);

            return $next($request,$response);
        }

    }


?>