<?php

class Response {

    public static function notFound(): void{
        // If no matching route was found, show a 404 page
        http_response_code(404);
        include_once Router::$viewsFolder . '/404.php';
        exit();
    }

    public static function wrongMethod(string $method): void{
        // If no matching route was found, show a 404 page
        http_response_code(405);
        header("Allow: GET, POST, PUT, DELETE");
        echo "Route doesn't support $method request !";
        exit();

    }
    
    public static function render(string $view, array $parameters = []): void {
        $viewPath = self::getViewsFolder() . "/$view.php";

        if(!file_exists($viewPath)){
            self::notFound();
        }

        try {
            http_response_code(200);
            foreach ($parameters as $key => $value) {
                $$key = $value;
            }
            include $viewPath;
            exit();
        } catch (Exception $e) {
            echo "Error rendering view: " . $e->getMessage();
            exit();
        }
    }

    private static function getViewsFolder(): string {
        return Router::$viewsFolder;
    }

}

?>