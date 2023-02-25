<?php

class Response {

    /**
     * RENDER 404 PAGE
     * @return void - Returns a 404 page
     */  
    public static function notFound(): void{
        // If no matching route was found, show a 404 page
        http_response_code(404);
        include_once Router::$viewsFolder . '/404.php';
        exit();
    }

    /**
     * RENDER MESSAGE IF WRONG HTTP METHOD WAS USED ON A ROUTE
     * @param string $method - HTTPM Method
     * @return void - Returns a message if wrong HTTP method was used
     */  
    public static function wrongMethod(string $method): void{
        // If no matching route was found, show a 404 page
        http_response_code(405);
        header("Allow: GET, POST, PUT, DELETE");
        echo "Route doesn't support $method request !";
        exit();

    }
    
     /**
     * RENDER VIEW
     * @param string $view - Which view to liad
     * @param array $parameters - URL parameters to be used inside the view
     * @return void - Returns a view if one is found
     */  
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

    /**
     * GET VIEWS DIRECTORY FROM ROUTER 
     * @return string $viewsFolder - Path to Views directory, where views are stored
     */  
    private static function getViewsFolder(): string {
        return Router::$viewsFolder;
    }

    /**
     * REDIRECT RESPONSE
     * @param string $url - URL target to redirect to
     * @param int $statucCode - HTTP status code
     */  
    public static function redirect(string $url, int $statusCode = 302): void {
        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * SEND JSON-ENCODED DATA AS REPONSE 
     * @param array $data - Data to send
     * @param int $statucCode - HTTP status code
     */  
    public static function json(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * SEND TEXT AS REPONSE 
     * @param array $data - Data to send
     * @param int $statucCode - HTTP status code
     */  
    public static function text(string $text, int $statusCode = 200): void {
        header('Content-Type: text/plain');
        http_response_code($statusCode);
        echo $text;
        exit();
    }

        /**
     * SEND FILE AS RESPONSE
     * @param string $filePath - Path to file to send
     * @param string $fileName - Name of file to send (optional)
     * @return void - Sends file as response
     */  
    public static function file(string $filePath, string $fileName = ''): void {
        if (!file_exists($filePath)) {
            self::notFound();
        }

        // Set appropriate headers
        header('Content-Type: ' . mime_content_type($filePath));
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: attachment; filename="' . ($fileName ?: basename($filePath)) . '"');

        // Send file contents
        readfile($filePath);
        exit();
    }
    
    

}

?>