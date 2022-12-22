<?php

    include_once './core/Router.php';
    include_once './core/Request.php';
    include_once './controllers/UserController';
   

    
    Router::get("/", "home.php");
    Router::get("/user", [UserController::class, 'index'] );

    Router::post("/user",  function(Request $Request, Response $Response){

        echo "FORM POST ";
    });

    Router::get("/user/:id", 'user.php');

    Router::get("/:id/:id", function(Request $Request, Response $Response){

        //$params = $Request->getParams();

        $Response->echoMessage("Route reached");

    });

    Router::get("/user/:id/post/:id", function(Request $Request, Response $Response){

        $params = $Request->getParams();
        echo "User:" . $params['id'] . "<br>";
        echo "ID:" . $params['id_1'] . "<br>";

        $Response->echoMessage("Route reached");

    });

    Router::get("/profile/:user",  function(Request $Request, Response $Response){
        $Response->echoMessage("Route reached");

    });

    Router::get("/profile/:user/:id",  function(Request $Request, Response $Response){

        $params = $Request->getParams();
        echo $params['id'];
        echo $Request->getParameter('user');

        $Response->echoMessage("Route reached");

    });

    Router::get("/user/profile/:id", "admin.php");
    Router::post("/user/new/:username", function(Request $Request, Response $Response){

        Response::render('admin.php', $Request->getParams());

    });

    Router::get("/post/:id/comment/:id", function($Request,$Response){
        $params = $Request->getParams();
        
        print_r($params);


        $Response->echoMessage("Route reached");


    });

    Router::get("/404", "404.php");


    Router::resolve();

?>