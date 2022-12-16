<?php

    include_once './core/Router.php';
   

    Router::get("/", "home.php");
    Router::get("/user/:id", function($val1) {
        $data = array(
            "Nicole",
            "Sarah",
            "Jinx",
            "Sarai"
        );

        echo $data[$val1] ?? "No data";
    });

    Router::get("/user/profile/:id", "admin.php");
    Router::post("/user/new/:username", function($val) {
        echo "User " . $val . " created <br>";
    });
    Router::resolve();

?>