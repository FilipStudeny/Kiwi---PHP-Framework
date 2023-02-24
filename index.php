<?php
// Import the required classes
include_once './core/Router.php';
include_once './core/Request.php';
include_once './core/Response.php';
require_once './core/ViewParameters.php';

require_once './core/Router.php';

// Set the views folder
Router::setViewsFolder('./views/');

// Define some routes
Router::get('/', "home" );

// Define some routes
Router::get('/:username', function(Request $req, Response $res) {
    $name = $req->getParameter("username");

    $params = new ViewParameters();
    $params->addParameters('username', $name);

    Response::render("profile", $params->getParameters());
});

// Define some routes
Router::get('/:username/:id/:id', function(Request $req, Response $res) {
    $params = $req->getParams();

    print_r($params);
    echo "Welcome ! ";
});






// Resolve route and send response
Router::resolve();

?>