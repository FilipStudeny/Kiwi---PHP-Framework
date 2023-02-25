<?php
require_once './core/Request.php';
require_once './core/Response.php';

class Router{
    public static $routes = [];
    public static $viewsFolder = "";
    public static $middleware = [];

    public static function setViewsFolder(string $viewsFolder): void{
        self::$viewsFolder = $viewsFolder;
    }

    public static function get(string $route, $callback, callable $middleware=null): void{
        self::$routes[] = [
            'route' => $route,
            'callback' => $callback,
            'method' => 'GET',
            'middleware' => $middleware
        ];
    }

    public static function post(string $route, $callback, callable $middleware=null): void{
        self::$routes[] = [
            'route' => $route,
            'callback' => $callback,
            'method' => 'POST',
            'middleware' => $middleware
        ];
    }

    public static function delete(string $route, $callback, callable $middleware=null): void{
        self::$routes[] = [
            'route' => $route,
            'callback' => $callback,
            'method' => 'DELETE',
            'middleware' => $middleware
        ];
    }

    public static function put(string $route, $callback, callable $middleware=null): void{
        self::$routes[] = [
            'route' => $route,
            'callback' => $callback,
            'method' => 'PUT',
            'middleware' => $middleware
        ];
    }

    public static function use(callable $middleware): void {
        self::$middleware[] = $middleware;
    }
    
    public static function resolve(): void {
        $urlPath = Request::getURIpath();
        $urlMethod = Request::getHTTPmethod();

        $wrongMethod = false;
        $routeFound = false;
    
        foreach (self::$routes as $route) {
            // Replace any :param with a regular expression
            $pattern = preg_replace('/:[a-zA-Z0-9]+/', '([a-zA-Z0-9-]+)', $route['route']);


            // Check if the URL matches the pattern
            if (preg_match("#^$pattern$#", $urlPath, $matches)) {

                if($urlMethod != $route['method']){
                    $wrongMethod = true;
                    $routeFound = true;
                    break;
                }

                $routeFound = true;
                $wrongMethod = false;

                // Remove the first element, which is the entire matched string
                array_shift($matches);

                $parameters = RouteParameterAssembler::assembleParameterTable($route, $matches);



                if (is_callable($route['middleware'])){
                    call_user_func($route['middleware']);
                }

                
                // Execute the callback function
                $callback = $route['callback'];
                
                if(is_callable($callback)){
                    call_user_func($callback, new Request($parameters), new Response);
                }else{
                    Response::render($callback);
                }
                return;
            }
        }

        if(!$routeFound){
            Response::notFound();
        }

        if($routeFound && $wrongMethod){
            Response::wrongMethod($urlMethod);
        }   
    }
}

class RouteParameterAssembler {

    public static function assembleParameterTable(array $route, array $matches): array{
        // GET PARAMETER NAME FROM URI
        $uriExplosion = explode('/', $route['route']);
        array_shift($uriExplosion);

        // Define the callback function to filter the array
        $callback = function($value) {
            return strpos($value, ':') === 0; // Check if the string starts with ":"
        };
        // Use array_filter to remove strings that don't start with ":"
        $result = array_filter($uriExplosion, $callback);
        $parameters = array();
        if(count($uriExplosion) != 0){
            
            $parameterNames = [];
            foreach ($result as $param){
                array_push($parameterNames, str_replace(":", "", $param));
            }
            
            // ASSEMBLE PARAMETER TABLE
            for ($i=0; $i < count($matches); $i++) { 
                /**
                * CHECK IF ARRAY ALREADY HAS PARAMETER OF SAME NAME, IF YES ADD NUMBER TO IT
                */
                if(array_key_exists($parameterNames[$i], $parameters)){
                    $parameters[$parameterNames[$i] . "_" . $i] = $matches[$i];
                    break;
                }
                        
                $parameters[$parameterNames[$i]] = $matches[$i];
            }
        }

        return $parameters;
    }
}

?>

