<?php

    session_start();

    require '../vendor/autoload.php';

    $app =  new \Slim\App([
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ]);

    $container = $app->getContainer();

    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig(__DIR__.'/../views',[
            'cache' => false
        ]);

        $basePath = rtrim(str_ireplace('index.php','',$container['request']->getUri()->getBasePath()),'/');
        $view->addExtension(new Slim\Views\TwigExtension($container['router'],$basePath));

        return $view;
    };

    $container['db'] = function(){
        return new PDO('mysql:host=localhost;dbname=commerce','root','');
        // return new PDO('mysql:host=localhost;dbname=ecopiava_ecopiacosmetics','ecopiava_ecopiacosmetics','kk969wddu7x6esrlwu');   
    };

    $container['flash'] = function(){
        return new \Slim\Flash\Messages;
    };


    require '../routes/router.php';
?>