<?php

    include_once './core/Response.php';
    include_once './core/Request.php';

    class Router{

        public static $routes = [];

        public static function get($route, $callback){
            self::$routes[] = [
                'route' => $route,
                'callback' => $callback,
                'method' => 'GET'
            ];
        }

        public static function post($route, $callback){
            self::$routes[] = [
                'route' => $route,
                'callback' => $callback,
                'method' => 'POST'
            ];
        }

        public static function put($route, $callback){
            self::$routes[] = [
                'route' => $route,
                'callback' => $callback,
                'method' => 'PUT'
            ];
        }

        public static function delete($route, $callback){
            self::$routes[] = [
                'route' => $route,
                'callback' => $callback,
                'method' => 'DELETE'
            ];
        }

        public static function resolve(){
            $path = Request::getURIpath(); //$_SERVER['REQUEST_URI'];
            $httpMethod = Request::getHTTPmethod(); //$_SERVER['REQUEST_METHOD'];

            $methodMatch = false;
            $routeMatch = false;

            //CHECK EACH ROUTE AND COMPARE IT WITH REQUEST URL
            foreach(self::$routes as $route){

                // CONVERTS URL TO REQULAR EXPRESSION
                $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['route'])) . "$@D";
                $matches = Array();

                if(preg_match($pattern, $path, $matches)){
                    $routeMatch = true;
                }else{
                    $routeMatch = false;
                }

                if($httpMethod === $route['method']){
                    $methodMatch = true;
                }else{
                    $methodMatch = false;
                }

                // CHECK IF ROTUE REQUEST MATCHES WITH REGISTERED ROUTES
                if($routeMatch && $methodMatch) {

                    array_shift($matches); //REMOVES FIRST ELEMENT

                    if(is_callable($route['callback'])){
                        echo Request::getURIpath() . "<br>";

                        echo Request::getParams($route)[0] . "<br>";
                        echo Request::getParam($route, 1) . "<br>";

                        call_user_func_array($route['callback'], $matches);
                    }else{
                        Response::render($route['callback'], $matches);
                    }
                    break;
                }
            }

            /*
            * CHECK AND RENDER WRONG REQUESTS
            */
            if(!$routeMatch && !$methodMatch){
                Response::notFound();
            }
        
            if(!$routeMatch && $methodMatch){
                Response::notFound();
            }

            if($routeMatch && !$methodMatch){
                Response::wrongMethod();

            }

        }


        


    }

?>