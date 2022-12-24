<?php

    class Response{

        public static $pageComponents = [];

        /**
         * RENDER USER SPECIFIED PAGE WITH PARAMETERS OR WITHOUT
         */
        public static function render($file, $params="" , $viewsFolder='./views/'){

            $view = self::view($file, $params, $viewsFolder);

            

            if(count(self::$pageComponents) != 0){
                $templates = [];
                $files = [];
                foreach(self::$pageComponents as $component){
                    array_push($templates, $component['component']);
                    array_push($files, file_get_contents('./views/components/' . $component['file']));
                }

                echo str_replace($templates, $files, $view);
                exit();
            }
    
            $layoutContent = self::renderPageComponent("header.php");
            echo str_replace("{{header}}",$layoutContent, $view); 
            exit();
        }


        public static function view($file, $params="" , $viewsFolder='./views/'){
            ob_start();
            include_once($viewsFolder . $file);
            return ob_get_clean();
        }

        public static function renderPageComponent($layout){
            ob_start();
            $componentPath = __DIR__ . Router::$viewFolder . Router::$componentFolder . "/";
            include_once($componentPath . $layout);
            return ob_get_clean();

        }

        public static function registerPageComponent($name, $file){
            array_push(self::$pageComponents, [
                'component' => "{{" . $name . "}}",
                'file' => $file
            ]);
        }


        public function echoMessage($message){
            echo $message;
        }


        public function setStatusCode($code){
            http_response_code($code);
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
            header('Location: 404');
            http_response_code(404);
            include_once('./views/404.php');
            
            exit();
        }
    }

?>