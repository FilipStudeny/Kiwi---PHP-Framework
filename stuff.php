
<?php
/*
    class Request{

        public function GetMethod(){
            return strtolower($_SERVER['REQUEST_METHOD']); //HTTP METHOD TO lower case
        }

        public function GetPath(){
            $path = $_SERVER['REQUEST_URI'] ?? '/'; 
            $paramPosition = strpos($path, '?');

            if($paramPosition === false){
                return $path;
            }

            return $path = substr($path, 0, $paramPosition); //RETURNS ROUTE BEFORE ? PARAM
        }
    }

    class Response{

        public function SetStatus($code){
            http_response_code($code);
        }

        public function resolve($request, $response, $routes, $viewsFolder){
            $path = $request->GetPath();  //GET ROUTE PATH
            $method = $request->GetMethod(); //GET HTTP METHOD
            $callback = $routes[$method][$path] ?? false; //CHECK IF CALLBACK FUNCTION IS PRESENT

            if($callback === false){
                $response->SetStatus(400);
                return "Not found";
            }
            
            if(is_string($callback)){
                
                return $this->RenderView($callback, $viewsFolder);
            }

            

            //EXECUTE CALLBACK
            return call_user_func($callback);
        }

        protected function RenderView($view, $viewsFolder){

            ob_start(); //STARTS OUTPUT CACHING
            include_once($viewsFolder . $view . ".php");
            return ob_get_clean();
        }
    }

    class Router{

        private Request $request;
        private Response $response;
        private $viewsFolder;
        protected array $routes = [];

        public function __construct($viewsFolder)
        {
            $this->viewsFolder = $viewsFolder;
            $this->request = new Request();
            $this->response = new Response();
        }

        public function get($route, $callback){
            $this->routes['get'][$route] = $callback; //SAVE CALLBACK TO BY METHOD AND ROUTE

            echo($this->response->resolve($this->request, $this->response, $this->routes, $this->viewsFolder));

        }



        
    }


    $router = new Router("./views/");
    $router->get('/', "home");
    $router->get('/user', "user");


   


    /*
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


    include_once './core/Request.php';
    include_once './core/Response.php';
    
    class Router
    {
        public static function get($app_route, $app_callback)
        {
            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
                return;
            }

            self::on($app_route, $app_callback);
        }

        public static function post($app_route, $app_callback)
        {
            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
                return;
            }

            self::on($app_route, $app_callback);
        }

        public static function on($exprr, $call_back)
        {
            $paramtrs = $_SERVER['REQUEST_URI'];
            $paramtrs = (stripos($paramtrs, "/") !== 0) ? "/" . $paramtrs : $paramtrs;
            $exprr = str_replace('/', '\/', $exprr);
            $matched = preg_match('/^' . ($exprr) . '$/', $paramtrs, $is_matched, PREG_OFFSET_CAPTURE);

            if ($matched) {
                // first value is normally the route, lets remove it
                array_shift($is_matched);
                // Get the matches as parameters
                $paramtrs = array_map(function ($paramtr) {
                    return $paramtr[0];
                }, $is_matched);
                $call_back(new Request($paramtrs), new Response());
            }
        }
    }

    class Request
    {
        public $paramtrs;
        public $req_method;
        public $content_type;
    
        public function __construct($paramtrs = [])
        {
            $this->paramtrs = $paramtrs;
            $this->req_method = trim($_SERVER['REQUEST_METHOD']);
            $this->content_type = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        }
    
        public function getBody()
        {
            if ($this->req_method !== 'POST') {
                return '';
            }
    
            $post_body = [];
            foreach ($_POST as $key => $value) {
                $post_body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
    
            return $post_body;
        }
    
        public function getJSON()
        {
            if ($this->req_method !== 'POST') {
                return [];
            }
    
            if (strcasecmp($this->content_type, 'application/json') !== 0) {
                return [];
            }
    
            // Receive the RAW post data.
            $post_content = trim(file_get_contents("php://input"));
            $p_decoded = json_decode($post_content);
    
            return $p_decoded;
        }
    }
?>


<?php
    
    class Response
    {
        private $p_status = 200;
    
        public function p_status(int $p_code)
        {
            $this->p_status = $p_code;
            return $this;
        }
        
        public function toJSON($data = [])
        {
            http_response_code($this->p_status);
            header('Content-Type: application/json');
            echo json_encode($data);
        }

    }
?>
*/

?>