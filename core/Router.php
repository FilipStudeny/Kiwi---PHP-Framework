<?php

use core\http\Next;
use core\http\Request;
use core\http\Response;

require_once './core/http/Next.php';
require_once './core/http/Request.php';
require_once './core/http/Response.php';


class Router{
    public static array $routes = [];
    public static string $viewsFolder = "";
    public static array $middleware = [];

    private static int $COMPONENT_RENDER_DEPTH = 2;
    private static string $basePath = ''; // New static property for base route
    private static string $errorViews = "Errors";

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

    /**
     * @throws Exception
     */
    public static function resolve(): void {
        $urlPath = Request::getURIpath();
        $urlMethod = Request::getHTTPmethod();

        $wrongMethod = false;
        $routeFound = false;
        $modifiedData = null;

        foreach (self::$routes as $route) {
            $pattern = preg_replace('/:[a-zA-Z0-9]+/', '([a-zA-Z0-9-]+)', $route['route']);
            if (preg_match("#^$pattern$#", $urlPath, $matches)) {
                $routeFound = true;
            }

            if($routeFound){
                if($urlMethod == $route['method']){
                    $wrongMethod = false;
                }else{
                    $wrongMethod = true;
                }
            }

            if($routeFound && !$wrongMethod){
                array_shift($matches);
                $parameters = RouteParameterAssembler::assembleParameterTable($route, $matches);

                if (is_callable($route['middleware'])) {
                    $next = new Next($parameters);
                    $next = call_user_func($route['middleware'], new Request($parameters), $next); // Update $next with the modified data
                    $modifiedData = $next->getModifiedData(); // Retrieve the modified data from $next
                }

                $callback = $route['callback'];
                if (is_callable($callback)) {
                    // If the middleware has modified data, pass it to the route
                    $requestToRoute = new Request($modifiedData ?? $parameters);
                    call_user_func($callback, $requestToRoute, new Response());
                } else if (is_string($callback)) {
                    Response::render($callback);
                }
                break;
            }
        }

        if (!$routeFound) {
            Response::notFound();
        }

        if ($routeFound && $wrongMethod) {
            Response::wrongMethod($urlMethod);
        }

    }

    public static function getErrorViews(): string
    {
        return self::$errorViews;
    }

    public static function setErrorViews(string $errorViews): void
    {
        self::$errorViews = $errorViews;
    }

    public static function getViewsFolder(): string{
        return self::$viewsFolder;
    }

    public static function getComponentRenderDepth(): int
    {
        return self::$COMPONENT_RENDER_DEPTH;
    }

    public static function setComponentRenderDepth(int $COMPONENT_RENDER_DEPTH): void
    {
        self::$COMPONENT_RENDER_DEPTH = $COMPONENT_RENDER_DEPTH;
    }

    public static function getRoutes(): array {
        $all_routes = [];
        foreach (self::$routes as $route) {
            $all_routes[] = [
                'route' => $route['route'],
                'method' => $route['method']
            ];
        }
        return $all_routes;
    }
}

class RouteParameterAssembler {

    public static function assembleParameterTable(array $route, array $matches): array{
        // GET PARAMETER NAME FROM URI
        $uriExplosion = explode('/', $route['route']);
        array_shift($uriExplosion);

        // Define the callback function to filter the array
        $callback = function($value) {
            return str_starts_with($value, ':'); // Check if the string starts with ":"
        };
        // Use array_filter to remove strings that don't start with ":"
        $result = array_filter($uriExplosion, $callback);
        $parameters = array();
        if(count($uriExplosion) != 0){

            $parameterNames = [];
            foreach ($result as $param){
                $parameterNames[] = str_replace(":", "", $param);
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


