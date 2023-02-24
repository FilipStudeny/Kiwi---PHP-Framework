<?php

    class Request{

        private $parameters; //ROUTE PARAMETERS

        function __construct($parameters)
        {
            $this->parameters = $parameters;
        }

        /**
         * GET ALL PARAMETERS FROM ROUTE AS AN ARRAY
         */
        public function getParams(){
            return $this->parameters;
        }

        /**
         * GET SINGLE PARAMETER FROM ROUTE
         */
        public function getParameter($index){
            return $this->parameters[$index];
        }

        /**
         * GET URI PATH
         */
        public static function getURIpath(){
            return $_SERVER['REQUEST_URI'] ?? '/';
        }

        /**
         * GET HTTP METHOD
         */
        public static function getHTTPmethod(){
            return $_SERVER['REQUEST_METHOD'];
        }

        /**
         * GET HTTP POST BODY
         */
        public function getBody(){
            return $_POST;
        }

        /**
         * GET HTTP POST BODY PART
         */
        public function getPostData($index){
            return $_POST[$index];
        }

    }
?>