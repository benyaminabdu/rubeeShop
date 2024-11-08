<?php

    namespace App\Middleware;
    
    use Psr\Container\ContainerInterface;
    use App\Functions\AdminAuthFunctions;

    class AdminAuth {

        protected $c;

        public function __construct(ContainerInterface $c){
            $this->c = $c;
        }

        public function __invoke($request,$response,$next){

            if(isset($_SESSION['accessToken']) && isset($_SESSION['tokenVerify'])){

                $accessToken = $_SESSION['accessToken'];
                $tokenVerify = $_SESSION['tokenVerify'];

                $auth = new AdminAuthFunctions($this->c->db);

                if($auth->checkIfTokenMatches($accessToken,$tokenVerify)){

                    $authData = $auth->getCredentialDataByToken($accessToken,$tokenVerify);
                    
                    $request = $request->withAttribute('username', $authData->username);

                }
                else {
                    session_destroy();
                    return $response->withRedirect($this->c->router->pathFor('login'));
                }


                return $next($request,$response);
            }
            else {
                session_destroy();
                return $response->withRedirect($this->c->router->pathFor('login'));
            }

        }
    }

?>