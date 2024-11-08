<?php

    namespace App\Controllers;

    use App\Functions\AdminAuthFunctions;

    class AuthController extends Controller {

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Authentication

        public function login($request,$response){

            session_destroy();

            $messages = $this->c->flash->getMessages();

            $data = array(
                "messages" => $messages
            );

            return $this->c->view->render($response,'auth/login.html',compact('data'));
        }

        public function logger($request,$response){

            $username = $request->getParam('username');
            $password = $request->getParam('password');

            $auth = new AdminAuthFunctions($this->c->db);

            if($auth->checkIfExists($username)){

                $authData = $auth->getAuthData($username);     
                $pssdComputed = hash('SHA256', $password.$authData->pssdSalt);

                if($pssdComputed == $authData->pssd){

                    $accessToken = $authData->accessToken;
                    $tokenVerify = $authData->tokenVerify;

                    $_SESSION['accessToken'] = $accessToken;
                    $_SESSION['tokenVerify'] = $tokenVerify;

                    return $response->withRedirect($this->c->router->pathFor('admin.dashboard'));
                }
                else {
                    $this->c->flash->addMessage('error', 'Invalid Username/Password Combination');
                    return $response->withRedirect($this->c->router->pathFor('login'));
                }

            }
            else {
                $this->c->flash->addMessage('error', 'Invalid Username/Password Combination');
                return $response->withRedirect($this->c->router->pathFor('login'));
            }
        }



        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Logout

        public function logout($request,$response){

            session_destroy();
            return $response->withRedirect($this->c->router->pathFor('landing'));
        }

    }

?>