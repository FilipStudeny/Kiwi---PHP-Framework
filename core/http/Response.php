<?php

namespace core\http;

use core\views\View;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Router;
use ViewParameters;

require_once './core/views/View.php';

class Response
{
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;

    /**
     * RENDER 404 PAGE
     * @return void - Returns a 404 page
     */
    #[NoReturn] public static function notFound(): void
    {
        self::setStatusCode(self::HTTP_NOT_FOUND);
        self::render('404', (array)null, true);
        exit();
    }

    /**
     * RENDER MESSAGE IF WRONG HTTP METHOD WAS USED ON A ROUTE
     * @param string $method - HTTP Method
     * @return void - Returns a message if wrong HTTP method was used
     */
    #[NoReturn] public static function wrongMethod(string $method): void
    {
        self::setStatusCode(self::HTTP_METHOD_NOT_ALLOWED);
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
    #[NoReturn] public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * SEND JSON-ENCODED DATA AS RESPONSE
     * @param array $data - Data to send
     * @param int $statusCode - HTTP status code
     */
    #[NoReturn] public static function json(array $data, int $statusCode = self::HTTP_OK): void
    {
        self::setContentType('application/json');
        self::setStatusCode($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * SEND TEXT AS RESPONSE
     * @param string $text - Text to send
     * @param int $statusCode - HTTP status code
     */
    #[NoReturn] public static function text(string $text, int $statusCode = self::HTTP_OK): void
    {
        self::setContentType('text/plain');
        self::setStatusCode($statusCode);
        echo $text;
        exit();
    }

    /**
     * SEND FILE AS RESPONSE
     * @param string $filePath - Path to file to send
     * @param string $fileName - Name of file to send (optional)
     * @return void - Sends file as response
     */
    #[NoReturn] public static function file(string $filePath, string $fileName = ''): void
    {
        if (!file_exists($filePath)) {
            self::notFound();
        }

        header('Content-Type: ' . mime_content_type($filePath));
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: attachment; filename="' . ($fileName ?: basename($filePath)) . '"');

        readfile($filePath);
        exit();
    }

    /**
     * Set HTTP response status code
     * @param int $statusCode - HTTP status code
     */
    private static function setStatusCode(int $statusCode): void
    {
        http_response_code($statusCode);
    }

    /**
     * Set content type for the response
     * @param string $contentType - Content type for the response
     */
    private static function setContentType(string $contentType): void
    {
        header('Content-Type: ' . $contentType);
    }
}

?>