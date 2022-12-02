<?php

/*
    class Router{

        public static array $routes = [];

        public static function get($route, $callback){

            self::$routes['GET'] = $route;

        }

        public static function resolve($route = "/"){

            $uri = $_SERVER['REQUEST_URI'];
            $paresedURL = parse_url($uri);

            $httpMethod = $_SERVER['REQUEST_METHOD'];
            $pathFound = false;
            $routeMatch = false;

            //CHECK FOR PATH
            if(isset($paresedURL['path'])){
                $path = $paresedURL['path'];
            }else{
                $path = "/";
            }

            print_r(self::$routes);


            


        }

        public static function pageNotFound(){
            echo "<NOT FOUND>";

            http_response_code(404);
            exit();
        }
    }


    Router::get("/", function(){
        echo "HOME PAGE";

    });
    Router::get("/profile", "asda");
    Router::get("/profile/([0-9]*)", "asda");
    Router::get("/profile/([0-9]*)/admin", "asda");
    Router::get("/profile/([0-9]*)/admin/([0-9]*)", "asda");

    Router::resolve("/");
/*
// Add base route (startpage)
Route::add('/',function(){
    echo 'Welcome :-)';
});

// Simple test route that simulates static html file
Route::add('/test.html',function(){
    echo 'Hello from test.html';
});

// Post route example
Route::add('/contact-form',function(){
    echo '<form method="post"><input type="text" name="test" /><input type="submit" value="send" /></form>';
},'get');

// Post route example
Route::add('/contact-form',function(){
    echo 'Hey! The form has been sent:<br/>';
    print_r($_POST);
},'post');

// Accept only numbers as parameter. Other characters will result in a 404 error
Route::add('/foo/([0-9]*)/bar',function($var1){
    echo $var1.' is a great number!';
});

Route::add('/foo/([aA-zZ]*)/bar',function($var1){
    echo $var1.' is a great number!';
});

Route::add('/foo/([0-9]*)/bar/admin/([0-9]*)',function($var1, $var2){
    echo $var1.' is a great number! ' . $var2;
});

Route::run('/');
*/

    include_once './core/Router.php';
    include_once './core/Response.php';
    include_once './core/Request.php';

    $router = new Router("./views/");
    
    Router::get("/", "home.php");
    Router::get("/user", function() {
        echo "Path /user traversed ==== ";
    });


    Router::notFound("404.php");
    Response::render();


?>