<?php 

    include "./core/Response.php";
    include "./core/Request.php";

    class Router{


        static string $viewsFolder;
        public static $routes = Array();


        function __construct($viewsFolder)
        {
            self::$viewsFolder = $viewsFolder;
        }


        
        public static function get($route, $callback=""){
            array_push(self::$routes,Array(
                'route' => $route,
                'callback' => $callback,
              ));

            if($_SERVER['REQUEST_METHOD'] != 'GET'){
                echo "Wrong HTTP request";
                exit();
            }
            self::$routes[Request::getHTTPMethod()][$_SERVER['REQUEST_URI']] = $route;

            

        }



        //404 TEXT
        public static function notFound($file){
    
            http_response_code(404);
            include(self::$viewsFolder . $file);
            exit(); 
            
        }
    }
?>