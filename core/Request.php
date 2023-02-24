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
            return $this->parameters[$index] ?? null;
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
            return $this->validateAndSanitize($_POST);
        }

        /**
         * GET HTTP POST BODY PART
         */
        public function getPostData(string $index)
        {
            $postData = $this->getBody();
            return $postData[$index] ?? null;
        }

        private function validateAndSanitize(array $data): array
        {
            $validated = [];
    
            foreach ($data as $key => $value) {
                // Validate and sanitize $value here
                // e.g. using filter_input() and htmlspecialchars()
                $validated[$key] = htmlspecialchars(filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS));
            }
    
            return $validated;
        }

    }
?>