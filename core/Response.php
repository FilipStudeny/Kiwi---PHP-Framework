<?php

    class Response{


        public static function render(){

            foreach(Router::$routes as $route){
                preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

            
                //CHECK IF PATH CONTAINS PARAMETERS IF NOT EXECUTE ROUTE WITHOUT THEM
                if(empty($paramMatches[0])){
                    self::getSimpleRoute($route, $route['callback']);
                    return;
                }else{
                    self::getRouteWithParams($route, $paramMatches, $route['callback']);
                    return;
                }
            }
            
        }

        //RENDER VIEW
        public static function renderView($file){
            http_response_code(200);
            include(Router::$viewsFolder . $file);
            exit();
        }

        public static function getSimpleRoute($route, $callback=""){

            if(!empty($_REQUEST['uri'])){
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $requestURI = preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
            }else{
                $requestURI = "/";
            }

            if($requestURI == $route){

                if(is_string($callback)){
                    self::renderView($callback);
                   
                }else{
                    //EXECUTE CALLBACK FUNCTION
                    call_user_func($callback);
                    exit();
                }
            }
        }

        public static function getRouteWithParams($route, $paramMatches ,$callback){
            $params = []; //STORE ALL PARAMETER VALUES
            $paramKey = []; //STORES ALL PARAMETER NAMES

            //SET PARAMETER NAMES 
            foreach($paramMatches[0] as $key){
                $paramKey[] = $key;
            }

            if(!empty($_REQUEST['uri'])){
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $requestURI = preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);

                echo $requestURI . "  ";
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
                    include(Router::$viewsFolder . $callback);
                    exit();
                }
            }else{
                //EXECUTE CALLBACK FUNCTION
                call_user_func($callback);
                exit();
            }
            
        }
    }

?>