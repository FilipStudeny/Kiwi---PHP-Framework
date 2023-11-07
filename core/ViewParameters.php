<?php

class ViewParameters{
    private $parameters = [];

    public function addParameters(string $name, $value): void{
        $this->parameters[$name] = $value;
    }


    public function getParameters(): array{
        return $this->parameters;
    }
}