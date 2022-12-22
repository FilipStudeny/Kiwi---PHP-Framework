# KIVI Framework


## INCLUDES
-   Simple routing
-   Simple GET/POST Method handling
-   Rendering page with/without parameters
-   Simple Controller handling


## TODO
-   Tons of stuff


## HOW TO USE ?

### ROUTING

```PHP

    RENDER PAGE
    Router::get("/", "home.php");

    ROUTING WITH CONTROLLER
    Router::get("/user", [UserController::class, 'index'] );

    ROUTING WITH POST REQUEST
    Router::post("/user",  function(Request $Request, Response $Response){

        print_r($Request->getBody());
        echo $Request->getPostData('name');
        echo "FORM POST ";
    });

    ROUTING WITH CALLBACK FUNCTION AND CAPTURE REQUEST PARAMETERS
    Router::get("/user/:id/post/:id", function(Request $Request, Response $Response){

        $params = $Request->getParams();
        echo "User:" . $params['id'] . "<br>";
        echo "ID:" . $params['id_1'] . "<br>";

        $Response->echoMessage("Route reached");

    });

```