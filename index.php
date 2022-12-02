<?php

    include_once './core/Router.php';

    $router = new Router("./views/");
    
    $router->get("/", "home.php");
    $router->get("/user", function() {
        echo "Path /user traversed ==== ";
    });

    $router->get("/user/admin", "sads");
    $router->get("/admin", "asd");
    $router->get("/user/{id}", "user.php");
    $router->get("/user/{id}/admin", function(){

        $query = new Router("./views/");
        $s = $query->getQueryString("/user/{id}/admin");
        
        echo "Parameter => " . $s['id'];

    });

    $router->notFound("404.php");


?>