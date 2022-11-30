<?php

/*
class HTTPrequest{

  public function get($route){
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
      foreach($_GET as $key => $vl) {
        echo($key .  $vl . "\n");
      };
    };
  }

  public function post($route){
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
      foreach($_POST as $key => $vl) {
        echo($key .  $vl . "\n");
      };
    };
  }
}

class Router{

  private $viewsSource;

  function __construct($path)
  {
    $this->viewsSource = $path;
  }

  public function get($route, $file){
      $uri = $_SERVER['REQUEST_URI'];
      $uri = trim($uri, '/');
      $uri = explode('/', $uri);

      if($uri[0] == trim($route, '/')){
        array_shift($uri);
        $args = $uri;
      }
      
      print_r($uri);
  }


}


 // $url = $_SERVER['REQUEST_URI'];

  //$expl = explode('/', $url);

  /*

  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    foreach($_POST as $key => $vl) {
      echo($key);
      echo($vl);
    };
  }*/

include_once('./core/Router.php');

Router::get('/{as}', function () {
  include('./views/home.php');
  exit();
});

Router::get('/user/([0-9]*)', function( Request $request, Response $response) {
    $response->toJSON([
      'user' => ['id' => $request->paramtrs[0]],
      'status' => 'ok'
    ]);

});

Router::get('/user/admin/([aA0-zZ9]*)', function( Request $request, Response $response) {
  $response->toJSON([
    'user' => ['id' => $request->paramtrs[0]],
  ]);
});
/*
  $htpprequest = new HTTPrequest();
  $htpprequest->get("/home");
  $htpprequest->post("/");

    include_once './core/Request.php';
    include_once './core/Router.php';
    include_once './core/Route.php';


    //NEW ROUTE
    $route = new Route("./views/");

    $route->get("/", "home.php");
    $route->get("/user/{id}", "user.php");
    $route->notFound("404.php")

    /*
    $router = new Router(new Request);

    $router->get('/', function() {
      return <<<HTML
      <h1>Hello world</h1>
    HTML;
    });
    
    
    $router->get('/profile', function($request) {
      return <<<HTML
      <h1>Profile</h1>
    HTML;
    });
    
    $router->post('/data', function($request) {
    
      return json_encode($request->getBody());
    });
    */
?>