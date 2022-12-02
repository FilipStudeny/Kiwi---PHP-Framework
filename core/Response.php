<?php

    class Response{

        public function renderView($viewsFolder, $view){
            ob_start(); //STARTS OUTPUT CACHING
            include_once($viewsFolder . $view . ".php");
            return ob_get_clean();
        }
    }

?>