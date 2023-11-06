<?php

class ViewParameters{
    private $parameters = [];

    public function addParameters(string $name, string $value): void{
        $this->parameters[$name] = $value;
    }

    public function getParameters(): array{
        return $this->parameters;
    }
}