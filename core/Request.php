<?php

class Request{

    /**
     * GET URI PATH
     */
    public static function getURIpath(){
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * GET HTTP METHOD
     */
    public static function getHTTPmethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * GET ALL PARAMETERS FROM ROUTE AS AN ARRAY
     */
    public static function getParams($route){
        $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['route'])) . "$@D";
        $matches = Array();

        echo preg_quote($route['route']);

        preg_match($pattern, self::getURIpath(), $matches);
        array_shift($matches); //REMOVES FIRST ELEMENT

        return $matches;
    }

    /**
     * GET SINGLE PARAMETER FROM ROUTE
     */
    public static function getParam($route, $index){
        return self::getParams($route)[$index];
    }


}

?>