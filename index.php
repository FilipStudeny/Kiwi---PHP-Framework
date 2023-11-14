<?php
// Import the required classes
use core\http\Next;
use core\http\Request;
use core\http\Response;

require_once './core/ViewParameters.php';
require_once './core/Router.php';


// Set the views folder
Router::setViewsFolder('./views/');
Router::setErrorPageRoutes('./views/Errors/');
Router::setComponentRenderDepth(2);

function logEcho(Request $request, Next $next): Next{
    $username = $request->getParameter("username");
    $modifiedUsername = strtoupper($username);
    $next->setModifiedData(['username' => $modifiedUsername]);
    return $next;
}


Router::use('logEcho');

// Define some routes
Router::get('/', "home", 'logEcho' );
Router::post('/postsend', function(Request $req, Response $res) {
    echo "Site reached";
});

Router::get('/:username', function(Request $req, Response $res) {
    $name = $req->getParameter("username");
    $users = ['admin', 'pepa', 'bogo'];
    $users2 = [['admin', 0], ['pepa', 1], ['bogo', 2]];

    $params = new ViewParameters();
    $params->addParameters('username', $name);
    $params->addParameters('page', 1);
    $params->addParameters('users', $users);
   // $params->addParameters('users2', $users2);

    Response::renderTemplate("profile", $params->getParameters());
} );

Router::get('/user/:username', function(Request $req, Response $res){
    $username = $req->getParameter("username");

    echo "Hello: $username !";
}, 'logEcho' );

Router::get('/:username/:id/:id', function(Request $req, Response $res) {
    $params = $req->getParams();

    print_r($params);
    echo "Welcome ! ";
}, 'logEcho');

Router::get('/:username/:id/post/:id', function(Request $req, Response $res) {
    $params = $req->getParams();
    $username = $params["username"];
    $id = $params["id"];
    $post = $params["id_2"];

    echo "Welcome $username: $id = $post! ";
});



// Resolve route and send response
Router::resolve();

?>