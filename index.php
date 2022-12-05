<?php

    class Router{

        public static $routes = [];

        public static function get($route, $callback){
            self::$routes[] = [
                'route' => $route,
                'callback' => $callback,
                'method' => 'GET'
            ];

           
        }

        public static function resolve(){
            $path = $_SERVER['REQUEST_URI'];
            $httpMethod = $_SERVER['REQUEST_METHOD'];

            $methodMatch = false;
            $routeMatch = false;

            foreach(self::$routes as $route){

                // convert urls like '/users/:uid/posts/:pid' to regular expression
                $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['route'])) . "$@D";
                $matches = Array();


                // check if the current request matches the expression
                if(preg_match($pattern, $path, $matches) && $httpMethod === $route['method']) {
                    $routeMatch = true;
                    // remove the first match
                    array_shift($matches);
                    // call the callback with the matched positions as params

                    if(is_callable($route['callback'])){
                        call_user_func_array($route['callback'], $matches);
                    }else{
                        self::render($route['callback']);
                    }

                }

            }

            if(!$routeMatch){
                self::notFound();

            }

            
          
        }

        public static function render($file, $viewsFolder='./views/'){
            include($viewsFolder . $file);

        }

        public static function notFound(){
            http_response_code(400);
            include('./views/404.php');
            exit();
        }
    }

    Router::get("/", "home.php");
    Router::get("/user/:id", function($val1) {
        $data = array(
            "Nicole",
            "Sarah",
            "Jinx",
            "Sarai"
        );

        echo $data[$val1] ?? "No data";
    });

    Router::get("/user/profile/:id", "admin.php");
    Router::resolve();

?>