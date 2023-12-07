<?php

namespace core\views;
use core\http\Response;
use core\views\Template\TemplateEngine;
use Exception;
use Router;
use ViewParameters;

require_once './core/views/template/TemplateEngine.php';
readonly class View
{


    function __construct(private string $viewPath){}

    /**
     * @throws Exception
     */
    public function render(string $view, array $parameters = []): void
    {

        if (!file_exists($this->viewPath )) {
            Response::notFound();
        }

        // Load the view content
        $templateEngine = new TemplateEngine($this->viewPath);
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