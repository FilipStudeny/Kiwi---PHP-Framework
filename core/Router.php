<?php 

    class Router{


        protected array $routes = [];
        protected string $viewsFolder;

        function __construct($viewsFolder)
        {
            $this->viewsFolder = $viewsFolder;
        }

        private function getSimpleRoute($route, $callback){

            if(!empty($_REQUEST['uri'])){
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $requestURI = preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
            }else{
                $requestURI = "/";
            }

            if($requestURI == $route){

                if(is_string($callback)){
                    include($this->viewsFolder . $callback);
                    exit();
                }else{
                    //EXECUTE CALLBACK FUNCTION
                    call_user_func($callback);
                    exit();
                }
            }
        }

        private function getRouteWithParams($route, $paramMatches ,$callback){
            $params = []; //STORE ALL PARAMETER VALUES
            $paramKey = []; //STORES ALL PARAMETER NAMES

            //SET PARAMETER NAMES 
            foreach($paramMatches[0] as $key){
                $paramKey[] = $key;
            }

            if(!empty($_REQUEST['uri'])){
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $requestURI = preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
            }else{
                $requestURI = "/";
            }

            $requestURI = explode("/", $requestURI);
            $uri = explode("/", $route);
            //STORE PARAM INDEX POSITION 
            $paramIndexNumber = [];
            foreach($uri as $index => $param){
                if(preg_match("/{.*}/", $param)){
                    $paramIndexNumber[] = $index;
                }
            }


            foreach($paramIndexNumber as $key => $index){
                //IF URI PARAM IS EMPTY RETURN BECAUSE URL IS NOT VALID FOR THIS ROUTE
                if(empty($requestURI[$index])){
                    return;
                }
                //SET PARAMETER WITH PARAM NAME
                $params[$paramKey[$key]] = $requestURI[$index];
                //REGEX FOR COMPARING ROUTE ADRESS
                $requestURI[$index] = "{.*}";
            }

            $requestURI = implode("/", $requestURI);
            $requestURI = str_replace("/", "\/", $requestURI);//REPLACE SLASHES 

            if(is_string($callback)){
                //MATCH ROUTE WITH REGEX
                if(preg_match("/$requestURI/", $route)){
                    include($this->viewsFolder . $callback);
                    exit();
                }
            }else{
                //EXECUTE CALLBACK FUNCTION
                call_user_func($callback);
                exit();
            }
            
        }

        public function getQueryString($route){
            $params = []; //STORE ALL PARAMETER VALUES
            $paramKey = []; //STORES ALL PARAMETER NAMES
            preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

            //SET PARAMETER NAMES 
            foreach($paramMatches[0] as $key){
                $paramKey[] = $key;
            }

            if(!empty($_REQUEST['uri'])){
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $requestURI = preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
            }else{
                $requestURI = "/";
            }

            $requestURI = explode("/", $requestURI);
            $uri = explode("/", $route);
            //STORE PARAM INDEX POSITION 
            $paramIndexNumber = [];
            foreach($uri as $index => $param){
                if(preg_match("/{.*}/", $param)){
                    $paramIndexNumber[] = $index;
                }
            }


            foreach($paramIndexNumber as $key => $index){
                //IF URI PARAM IS EMPTY RETURN BECAUSE URL IS NOT VALID FOR THIS ROUTE
                if(empty($requestURI[$index])){
                    return;
                }
                //SET PARAMETER WITH PARAM NAME
                $params[$paramKey[$key]] = $requestURI[$index];
                //REGEX FOR COMPARING ROUTE ADRESS
                $requestURI[$index] = "{.*}";
            }

            $requestURI = implode("/", $requestURI);
            $requestURI = str_replace("/", "\/", $requestURI);//REPLACE SLASHES 

            return $params;
        }



        public function get($route, $callback){
            if($_SERVER['REQUEST_METHOD'] != 'GET'){
                echo "Wrong HTTP request";
                exit();
            }

        
            preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

            //CHECK IF PATH CONTAINS PARAMETERS IF NOT EXECUTE ROUTE WITHOUT THEM
            if(empty($paramMatches[0])){
                $this->getSimpleRoute($route, $callback);
                return;
            }else{
                $this->getRouteWithParams($route, $paramMatches, $callback);
                return;
            }

        }

        //GET PATH FROM URI
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

        //404 TEXT
        public function notFound($file){
            http_response_code(404);
            include($this->viewsFolder . $file);
            exit();        
        }
    }



?>