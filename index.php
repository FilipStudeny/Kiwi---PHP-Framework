<?php

    include_once './core/Router.php';
    include_once './core/Request.php';
   

    Router::get("/", "home.php");
    Router::get("/user/:id", function($Request,$Response){

        $Response->echo("Route reached");

    });

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

    Router::get("/profile/:user", function($Request,$Response){
        $Response->echo("Route reached");

    });

    Router::get("/profile/:user/:id",  function(Request $Request, Response $Response){

        $params = $Request->getParams();
        echo $params['id'];
        echo $Request->getParameter('user');

        $Response->echoMessage("Route reached");

    });

    Router::get("/user/profile/:id", "admin.php");
    Router::post("/user/new/:username", function($Request,$Response){

        $Response->echo("Route reached");
    });

    Router::get("/post/:id/comment/:id", function($Request,$Response){
        $params = $Request->getParams();
        
        print_r($params);


        $Response->echoMessage("Route reached");


    });
    Router::resolve();

?>