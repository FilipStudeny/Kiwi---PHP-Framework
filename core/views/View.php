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
    function __construct(private readonly string $view, private array $parameters = []){}

    /**
     * @throws Exception
     */
    public function render(string $viewPath): void
    {

        // Load the view content
        $templateEngine = new TemplateEngine($viewPath);
        foreach ($this->parameters as $key => $value) {
            $templateEngine->set($key, $value);
        }

        try {
            http_response_code(200);
            $templateEngine->render("/$this->view.php");
            exit();
        } catch (Exception $e) {
            echo "Error rendering view: " . $e->getMessage();
            exit();
        }
    }

    public function add(string $name, $value): void{
        $this->parameters[$name] = $value;
    }
    public function getParameters(): array{
        return $this->parameters;
    }

}