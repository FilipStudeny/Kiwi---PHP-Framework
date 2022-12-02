<?php

    include_once "./core/Router.php";

    class Request{

        //GET QUERY STRINGS
        public static function getQueryString(){

            $route = self::getPath();
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

        //GET ROUTER ROUTE
        public static function getRoute(){
            return Router::$routes[self::getHTTPMethod()][$_SERVER['REQUEST_URI']];
        }
 
        //GET PATH FROM URI
        public static function getPath(){
            $path = $_SERVER['REQUEST_URI'] ?? '/'; 
            $paramPosition = strpos($path, '?');
 
            if($paramPosition === false){
                return $path;
            }
 
            return $path = substr($path, 0, $paramPosition); //RETURNS ROUTE BEFORE ? PARAM
        }

        //GET HTTP METHOD
        public static function getHTTPMethod(){
            return strtolower($_SERVER['REQUEST_METHOD']); //HTTP METHOD TO lower case
        }
        
    }

?>