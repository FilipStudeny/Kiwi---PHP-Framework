<?php

    class Response{

        /**
         * RENDER USER SPECIFIED PAGE WITH PARAMETERS
         */
        public static function render($file, $params="" ,$viewsFolder='./views/'){
            include($viewsFolder . $file);
        }

        /**
         * RENDER RESPONSE WHEN WRONG HTTP METHOD IS USED ON ROUTE
         */
        public static function wrongMethod(){
            header("HTTP/1.0 405 Wrong request method");
            http_response_code(405);
            echo "Method not allowed";
            exit();
        }

        /**
         * RENDER 404 PAGE
         */
        public static function notFound(){
            header("HTTP/1.0 404 Page not found");
            http_response_code(400);
            include('./views/404.php');
            
            exit();
        }
    }

?>