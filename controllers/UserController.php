<?php


use core\http\Response;

class UserController{

        public static function index(){
            echo "USER CONTROLLER REACHED";

            $cp1 = ['component' => "{{content}}", 'file' => 'header.php'];
            $cp2 = ['component' => "{{header}}", 'file' => 'header.php'];

            Response::registerPageComponent('header', 'header.php');
            Response::registerPageComponent('footer', 'footer.php');


            Response::render('user.php');
            exit();
        }
    }

?>