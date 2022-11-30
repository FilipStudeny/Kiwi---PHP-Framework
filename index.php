<?php

    include_once './core/Request.php';
    include_once './core/Router.php';
    include_once './core/Route.php';


    //NEW ROUTE
    $route = new Route("./views/");

    $route->get("/", "home.php");
    $route->get("/user/{id}", "user.php");
    $route->notFound("404.php")

    /*
    $router = new Router(new Request);

    $router->get('/', function() {
      return <<<HTML
      <h1>Hello world</h1>
    HTML;
    });
    
    
    $router->get('/profile', function($request) {
      return <<<HTML
      <h1>Profile</h1>
    HTML;
    });
    
    $router->post('/data', function($request) {
    
      return json_encode($request->getBody());
    });
    */
?>