<?php

namespace core\http;

class Request
{

    private $parameters; //ROUTE PARAMETERS

    function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * GET ALL PARAMETERS FROM ROUTE AS AN ARRAY
     */
    public function getParams()
    {
        return $this->parameters;
    }

    /**
     * GET SINGLE PARAMETER FROM ROUTE
     */
    public function getParameter($index)
    {
        if (is_array($this->parameters)) {
            return $this->parameters[$index] ?? null;
        } else if ($this->parameters instanceof Next) {
            return $this->parameters->passToRoute()[$index] ?? null;
        }
        return null;
    }

    /**
     * GET URI PATH
     */
    public static function getURIpath()
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * GET HTTP METHOD
     */
    public static function getHTTPmethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * GET HTTP POST BODY
     */
    public function getBody()
    {
        return $this->validateAndSanitize($_POST);
    }

    /**
     * GET HTTP POST BODY PART
     */
    public function getPostData(string $index)
    {
        $postData = $this->getBody();
        return $postData[$index] ?? null;
    }

    /**
     * Validate and sanitize POST data
     */
    private function validateAndSanitize(array $data): array
    {
        $validated = [];

        foreach ($data as $key => $value) {
            // Validate and sanitize $value here
            // e.g. using filter_input() and htmlspecialchars()
            $filteredValue = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            if (empty($filteredValue)) {
                throw new Exception("Value for '$key' is required");
            }

            $validated[$key] = htmlspecialchars($filteredValue);
        }

        return $validated;
    }

}

?>