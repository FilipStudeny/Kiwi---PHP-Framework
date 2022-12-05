<?php

    class Router{

        private static $routes = Array();
        private static $pathNotFound = null;
        private static $methodNotAllowed = null;
    
        public static function get($route, $callback){
            array_push(self::$routes,Array(
                'route' => $route,
                'callback' => $callback,
                'method' => $_SERVER['REQUEST_METHOD']
            ));
        }
 
    
        public static function resolve(){
            $basepath = '/';
            // Parse current url
            $parsed_url = parse_url($_SERVER['REQUEST_URI']);//Parse Uri
        
            if(isset($parsed_url['path'])){
                $path = $parsed_url['path'];
            }else{
                $path = '/';
            }
        
            // Get current request method
            $method = $_SERVER['REQUEST_METHOD'];
        
            $path_match_found = false;
        
            $route_match_found = false;
        
            foreach(self::$routes as $route){
        
                // If the method matches check the path
        
                // Add basepath to matching string
                if($basepath!=''&&$basepath!='/'){
                    $route['route'] = '('.$basepath.')'.$route['route'];
                }
        
                // Add 'find string start' automatically
                $route['route'] = '^'.$route['route'];
        
                // Add 'find string end' automatically
                $route['route'] = $route['route'].'$';
        
                // echo $route['expression'].'<br/>';
        
                // Check path match	
                if(preg_match('#'.$route['route'].'#',$path,$matches)){
        
                    $path_match_found = true;
        
                    // Check method match
                    if(strtolower($method) == strtolower($route['method'])){
            
                        array_shift($matches);// Always remove first element. This contains the whole string
            
                        if($basepath!=''&&$basepath!='/'){
                            array_shift($matches);// Remove basepath
                        }
            
                        if(is_callable($route['callback'])){
                            call_user_func_array($route['callback'], $matches);

                        }else{
                            echo "asdads";
                        }
            
                        $route_match_found = true;
                        break;
                    }
                }
            }
    
            // No matching route was found
            if(!$route_match_found){
        
                // But a matching path exists
                if($path_match_found){
                    header("HTTP/1.0 405 Method Not Allowed");
                    if(self::$methodNotAllowed){
                        call_user_func_array(self::$methodNotAllowed, Array($path,$method));
                    }
                }else{
                    echo "<NOT FOUND>";

                    header("HTTP/1.0 404 Not Found");
                    if(self::$pathNotFound){
                        call_user_func_array(self::$pathNotFound, Array($path));
                    }
                }
            }
        }

           
        public static function pathNotFound($function){
            echo "<NOT FOUND>";
            self::$pathNotFound = $function;
        }
        
        public static function methodNotAllowed($function){
            self::$methodNotAllowed = $function;
        }
    }

?>