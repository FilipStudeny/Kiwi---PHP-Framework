<?php

namespace core\http;
use Exception;
use Router;
use ViewParameters;

class Response
{

    /**
     * RENDER 404 PAGE
     * @return void - Returns a 404 page
     */
    public static function notFound(): void
    {
        // If no matching route was found, show a 404 page
        http_response_code(404);
        self::render('404', (array)null, true);
        exit();
    }

    /**
     * RENDER MESSAGE IF WRONG HTTP METHOD WAS USED ON A ROUTE
     * @param string $method - HTTPM Method
     * @return void - Returns a message if wrong HTTP method was used
     */
    public static function wrongMethod(string $method): void
    {
        // If no matching route was found, show a 404 page
        http_response_code(405);
        header("Allow: GET, POST, PUT, DELETE");
        $params = new ViewParameters();
        $params->addParameters('method', $method);
        self::render('405', $params->getParameters(), true);
        exit();

    }

    /**
     * Render a view with components
     * @param string $view - The view to render
     * @param array $parameters - URL parameters to be used inside the view
     * @param bool $isErrorRoute - Flag for error route
     * @return void - Returns the rendered view
     */
    public static function render(string $view, array $parameters = [], bool $isErrorRoute = false): void
    {
        $viewPath = $isErrorRoute ? Router::getErrorPageRoutes() . "/$view.php" : self::getViewsFolder() . "/$view.php";

        if (!file_exists($viewPath)) {
            self::notFound();
        }

        // Load the view content
        ob_start();
        extract($parameters);
        include $viewPath;
        $viewContent = ob_get_clean();

        // Handle the components with parameters
        $viewContent = preg_replace_callback('/@component\("(\w+)"(?:\s*,\s*\[(.*)\])?\)/', function ($matches) use ($parameters) {
            $componentPath = self::getViewsFolder() . "/components/{$matches[1]}.php";
            $componentParams = [];
            if (!empty($matches[2])) {
                $paramsString = $matches[2];
                preg_match_all('/\'(\w+)\'\s*=>\s*(\w+)/', $paramsString, $paramMatches, PREG_SET_ORDER);
                foreach ($paramMatches as $match) {
                    $componentParams[$match[1]] = $parameters[$match[2]] ?? $match[2];
                }
            }
            if (file_exists($componentPath)) {
                ob_start();
                extract(array_merge($parameters, $componentParams));
                include $componentPath;
                $componentContent = ob_get_clean();
                return $componentContent;
            }
            return '';
        }, $viewContent);

        // Replace parameters enclosed in @ with their values
        $viewContent = preg_replace_callback('/@(\w+)/', function ($match) use ($parameters) {
            return $parameters[$match[1]] ?? $match[0];
        }, $viewContent);

        // Output the rendered content
        echo $viewContent;
    }



    /**
     * GET VIEWS DIRECTORY FROM ROUTER
     * @return string $viewsFolder - Path to Views directory, where views are stored
     */
    private static function getViewsFolder(): string
    {
        return Router::$viewsFolder;
    }

    /**
     * REDIRECT RESPONSE
     * @param string $url - URL target to redirect to
     * @param int $statusCode
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * SEND JSON-ENCODED DATA AS REPONSE
     * @param array $data - Data to send
     * @param int $statucCode - HTTP status code
     */
    public static function json(array $data, int $statusCode = 200): void
    {
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
    public static function text(string $text, int $statusCode = 200): void
    {
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
    public static function file(string $filePath, string $fileName = ''): void
    {
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