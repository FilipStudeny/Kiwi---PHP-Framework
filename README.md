# KIVI Framework


## INCLUDES
-   Simple routing
-   Simple GET/POST Method handling
-   Rendering page with/without parameters
-   Simple Controller handling


## TODO
-   Tons of stuff


## HOW TO USE ?

### Register middleware

```PHP
Router::use('logEcho');
```
Create middleware that returns Next, for middleware that handles returning username into Uppercase on the /:username route:
```PHP
function middleware_function(Request $request, Next $next): Next{
    $username = $request->getParameter("username");
    $modifiedUsername = strtoupper($username);
    $next->setModifiedData(['username' => $modifiedUsername]);
    return $next;
}
```

### Register view directories
```PHP
Router::setViewsFolder('./views/');
Router::setErrorPageRoutes('./views/Errors/');
```

### ROUTING
```PHP
    use core\http\Next;
    use core\http\Request;
    use core\http\Response;
    
    require_once './core/ViewParameters.php';
    require_once './core/Router.php';

    RENDER PAGE
    Router::get("/", "home.php");
    
    RENDER PAGE WITH COMPONENTS
    Router::get('/:username', function(Request $req, Response $res) {
        $name = $req->getParameter("username");
    
        $params = new ViewParameters();
        $params->addParameters('username', $name);
        $params->addParameters('page', 1);
    
        Response::render("profile", $params->getParameters());
    });
    
    ROUTING WITH CONTROLLER - WILL BE REIMPLEMENTED AND IMPROVED
    Router::get("/user", [UserController::class, 'index'] );

    ROUTING WITH POST REQUEST
    Router::post('/post_test', function(Request $req, Response $res) {
        echo "Site reached";
    });

    ROUTING WITH CALLBACK FUNCTION AND CAPTURE REQUEST PARAMETERS
    Router::get('/:username/:id/post/:id', function(Request $req, Response $res) {
        $params = $req->getParams();
        $username = $params["username"];
        $id = $params["id"];
        $post = $params["id_2"];
    
        echo "Welcome $username: $id = $post! ";
    });

```

### RENDING VIEW WITH COMPONENTS:

1. Create components inside the /components directory

body.php:
```PHP
<div>
    <p>Username: @username </p>
    <p>Page number: @page</p>
</div>
```
header.php:
```PHP
<header>
    <h1>Header Content</h1>
</header>
```

2. Create a view inside the /views directory an register component with parameters:
```PHP
<h1>Hello @username </h1>

@component("header")
@component("body", ['username' => @username, 'page' => @1 ])
```
3. Create route and register parameters:
```PHP
Router::get('/:username', function(Request $req, Response $res) {
    $name = $req->getParameter("username");

    $params = new ViewParameters();
    $params->addParameters('username', $name);
    $params->addParameters('page', 1);

    Response::render("profile", $params->getParameters());
});
```

4. Will be rendered as:
```HTML
Hello admin
Header Content

Username: ADMIN
Page number: 1
```