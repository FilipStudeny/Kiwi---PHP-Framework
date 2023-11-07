<?php

namespace core\views;
use core\http\Response;
use core\views\Template\TemplateEngine;
use Exception;
use Router;
use ViewParameters;

require_once './core/views/template/TemplateEngine.php';
class View
{
    private string $viewsFolder;

    function __construct(string $viewsFolder)
    {
        $this->viewsFolder = $viewsFolder;
    }

    public function renderStatic(string $view, array $parameters = [], bool $isErrorRoute = false): void
    {
        $viewPath = $isErrorRoute ? Router::getErrorPageRoutes() . "/$view.php" : $this->viewsFolder . "/$view.php";

        if (!file_exists($viewPath)) {
            Response::notFound();
        }

        // Load the view content
        ob_start();
        extract($parameters);
        include $viewPath;
        $viewContent = ob_get_clean();

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
     * @throws Exception
     */
    public function render(string $view, array $parameters = [], bool $isErrorRoute = false): void
    {
        $viewPath = $isErrorRoute ? Router::getErrorPageRoutes() . "/$view.php" : $this->viewsFolder . "/$view.php";

        if (!file_exists($viewPath)) {
            Response::notFound();
        }

        // Load the view content
        $templateEngine = new TemplateEngine($this->viewsFolder); // Assuming $this->viewsFolder is the directory path for your views
        foreach ($parameters as $key => $value) {
            $templateEngine->set($key, $value);
        }


        try {
            http_response_code(200);
            $templateEngine->render("/$view.php");
            exit();
        } catch (Exception $e) {
            echo "Error rendering view: " . $e->getMessage();
            exit();
        }
    }

}