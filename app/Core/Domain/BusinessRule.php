<?php

namespace App\Core\Domain;

class BusinessRule
{
    public string $property;

    public string $rule;

    public function __construct(string $property, string $rule)
    {
        $this->property = $property;
        $this->rule = $rule;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function setRule(string $rule): void
    {
        $this->rule = $rule;
    }
}
