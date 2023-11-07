<?php

namespace core\http;
use core\views\View;
use Exception;
use Router;
use ViewParameters;
use core\views;
require_once './core/views/View.php';

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
     * Render a view without components
     * @param string $view - The view to render
     * @param array $parameters - URL parameters to be used inside the view
     * @param bool $isErrorRoute - Flag for error route
     * @return void - Returns the rendered view
     */
    public static function render(string $view, array $parameters = [], bool $isErrorRoute = false): void
    {
        $newView = new View(self::getViewsFolder());
        $newView->renderStatic($view, $parameters, $isErrorRoute);
    }

    /**
     * Render a view with components
     * @param string $view - The view to render
     * @param array $parameters - URL parameters to be used inside the view
     * @param bool $isErrorRoute - Flag for error route
     * @return void - Returns the rendered view
     * @throws Exception
     */
    public static function renderTemplate(string $view, array $parameters = [], bool $isErrorRoute = false): void
    {
        $newView = new View(self::getViewsFolder());
        $newView->render($view, $parameters, $isErrorRoute);
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