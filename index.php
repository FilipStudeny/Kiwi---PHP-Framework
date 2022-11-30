<?php


    class Request{
        public function getPath(){
            $path = $_SERVER['REQUEST_URI'] ?? '/'; 
            $paramPosition = strpos($path, '?');

            if($paramPosition === false){
                return $path;
            }

            return $path = substr($path, 0, $paramPosition); //RETURNS ROUTE BEFORE ? PARAM
        }

        public function getHTTPMethod(){
            return strtolower($_SERVER['REQUEST_METHOD']); //HTTP METHOD TO lower case
        }
    }

    class Response{
        public function setStatusCode(int $code){
            http_response_code($code);
        }
    }

    class Router{

        private Request $request;
        private Response $response;
        protected array $routes = [];
        private $viewsFolder;

        public static string $rootDir;

        function __construct($viewsFolder)
        {   
            $this->viewsFolder = $viewsFolder;
            $this->request = new Request();
            $this->response = new Response();
        }

        public function get($path, $callback){
            $this->routes['get'][$path] = $callback; //SAVE CALLBACK TO BY METHOD AND ROUTE
        }

        public function resolve(){
            $path = $this->request->getPath();  //GET ROUTE PATH
            $method = $this->request->getHTTPMethod(); //GET HTTP METHOD
            $callback = $this->routes[$method][$path] ?? false; //CHECK IF CALLBACK FUNCTION IS PRESENT

            if($callback === false){
                $this->response->setStatusCode(400);
                return "Not found";
            }

            if(is_string($callback)){
                return $this->renderView($callback);
            }

            //EXECUTE CALLBACK
            return call_user_func($callback);
        }

        function renderView($view){
            
            $layoutContent = $this->layoutContent();
            $viewContet = $this->renderOnlyView($view);
            return str_replace('{{content}}', $viewContet, $layoutContent);
        }

        function layoutContent(){

            ob_start(); //STARTS OUTPUT CACHING
            include_once("./views/layouts/mainLayout.php");
            return ob_get_clean();
        }

        protected function renderOnlyView($view){
            ob_start(); //STARTS OUTPUT CACHING
            include_once($this->viewsFolder . $view . ".php");
            return ob_get_clean();
        }
    }

    $router = new Router('./views/');

    $router->get('/', 'home');
    $router->get('/user', 'user');

    echo($router->resolve());

?>