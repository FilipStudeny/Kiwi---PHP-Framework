<?php
    include_once './core/Request.php';
    include_once './core/Response.php';

    class Router{

        public static $routes = [];
        public static $rootDir = __DIR__;
        public static $viewFolder = "../../views/";
        public static $componentFolder = "components";

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
                }

                if($httpMethod === $route['method']){
                    $methodMatch = true;
                }

                // CHECK IF ROTUE REQUEST MATCHES WITH REGISTERED ROUTES
                if($routeMatch && $methodMatch) {

                    array_shift($matches); //REMOVES FIRST ELEMENT

                     // GET PARAMETER NAME FROM URI
                    $uriExplosion = explode('/', $route['route']);
                    array_shift($uriExplosion);

                    $parameters = array();

                    if(count($uriExplosion) != 1){
                        $routeParameterName = [];
                        foreach ($uriExplosion as $value) {
                            if($value[0] == ":"){
                                array_push($routeParameterName,str_replace(":", "", $value));
                            }
                        }
    
                        // ASSEMBLE PARAMETER TABLE
                        for ($i=0; $i < count($matches); $i++) { 
    
                            /**
                              * CHECK IF ARRAY ALREADY HAS PARAMETER OF SAME NAME, IF YES ADD NUMBER TO IT
                              */
                            if(array_key_exists($routeParameterName[$i], $parameters)){
                                $parameters[$routeParameterName[$i] . "_" . $i] = $matches[$i];
                                break;
                            }
    
                            $parameters[$routeParameterName[$i]] = $matches[$i];
                        }
                    }


                    if(is_callable($route['callback']) || is_array($route['callback'])){
                        call_user_func($route['callback'], new Request($parameters), new Response());
                        break;
                    }

                    if(is_string($route['callback'])){
                        Response::render($route['callback'], $parameters);
                        break;
                    }

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