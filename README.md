# KIVI Framework


## INCLUDES
-   Simple routing
-   GET/POST Method handling


## TODO
-   Tons of stuff


## HOW TO USE ?

### ROUTING

```PHP

    ROUTE DIRECTLY INTO A PAGE
    Router::get("/", "home.php");

    ROUTING WITH CONTROLLER
    Router::get("/user", [UserController::class, 'index'] );

    ROUTING WITH CALLBACK FUNCTION AND CAPTURE REQUEST PARAMETERS
    Router::get("/user/:id/post/:id", function(Request $Request, Response $Response){

        $params = $Request->getParams();
        echo "User:" . $params['id'] . "<br>";
        echo "ID:" . $params['id_1'] . "<br>";

        $Response->echoMessage("Route reached");

    });

```