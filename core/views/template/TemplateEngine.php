<?php

namespace core\views\template;

use Exception;

class TemplateEngine
{
    protected string $templateDir;
    protected array $templateData;

    /**
     * @throws Exception
     */
    public function __construct(string $templateDir)
    {
        if (!is_dir($templateDir)) {
            throw new Exception('Invalid template directory: ' . $templateDir);
        }
        $this->templateDir = $templateDir;
        $this->templateData = [];
    }

    public function set(string $name, $value): void
    {
        if (is_array($value)) {
            $this->templateData[$name] = $value;
        } else {
            $this->templateData[$name] = $value;
        }
    }


    /**
     * @throws Exception
     */
    public function render(string $templateName): void
    {
        $templatePath = $this->templateDir . '/' . $templateName;
        $templateContent = file_get_contents($templatePath);

        if ($templateContent === false) {
            throw new Exception('Error reading template file: ' . $templatePath);
        }

        $templateContent = $this->loadComponents($templateContent);
        $templateContent = $this->handleLoop($templateContent);

        foreach ($this->templateData as $key => $value) {
            $value = is_array($value) ? implode(', ', $value) : (string) $value;
            $templateContent = str_replace("{{" . $key . "}}", $value, $templateContent);
        }

        echo $templateContent;
    }

    /**
     * @throws Exception
     */
    protected function loadComponents(string $templateContent): string
    {
        $pattern = '/@component\((.*?),\s*({[\s\S]*?})\)/';
        preg_match_all($pattern, $templateContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $componentName = trim($match[1], " '");
            $attributes = json_decode(trim($match[2]), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in component attributes: ' . $match[2]);
            }

            // Check for missing attributes in the component registration
            if (empty($attributes)) {
                throw new Exception('No attributes specified for component ' . $componentName);
            }

            $componentPath = $this->templateDir . '/components/' . $componentName . '.php';
            if (file_exists($componentPath)) {
                ob_start();
                include $componentPath;
                $componentContent = ob_get_clean();

                // Extract placeholder keys in the component content
                $placeholderKeys = [];
                preg_match_all('/{{(.*?)}}/', $componentContent, $placeholderKeys);

                // Check for missing attributes in the component content
                $missingAttributes = array_diff($placeholderKeys[1], array_keys($attributes));
                if (!empty($missingAttributes)) {
                    throw new Exception('Missing attributes in component content ' . $componentName . ': ' . implode(', ', $missingAttributes));
                }

                // Replace the placeholders with the actual values
                foreach ($attributes as $key => $value) {
                    $componentContent = str_replace("{{" . $key . "}}", $value, $componentContent);
                }

                $templateContent = str_replace($match[0], $componentContent, $templateContent);
            } else {
                throw new Exception('Component ' . $componentName . ' not found!');
            }
        }
        $templateContent = $this->handleLoop($templateContent);
        return $templateContent;
    }
    /**
     * Loop through a range and execute the given template content for each iteration.
     *
     * @throws Exception
     */

    protected function handleLoop(string $templateContent): string
    {
        $pattern = '/@loop\((\w+)\s+as\s+(\$\w+)\)(.*?)@endloop/s';
        preg_match_all($pattern, $templateContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $loopArrayName = trim($match[1]);
            $loopVariableName = trim($match[2], '$');
            $loopContent = '';

            if (!array_key_exists($loopArrayName, $this->templateData) || !is_array($this->templateData[$loopArrayName])) {
                throw new Exception('Invalid or missing array for loop: ' . $loopArrayName);
            }

            foreach ($this->templateData[$loopArrayName] as $loopItem) {
                $iteratedContent = str_replace('{{' . $loopVariableName . '}}', $loopItem, $match[3]);
                $loopContent .= $this->loadComponents($iteratedContent); // Process components inside the loop
            }

            $templateContent = str_replace($match[0], $loopContent, $templateContent);
        }

        return $templateContent;
    }
}