<?php

namespace App\Core\Domain;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait BrokenRule
{
    private Collection $brokenRules;

    public function __construct()
    {
        $this->brokenRules = new Collection();
    }

    protected abstract function validate(): void;

    protected function addBrokenRule(BusinessRule $businessRule): void
    {
        $this->brokenRules->add($businessRule);
    }

    public function getBrokenRules(array $rules = null)
    {
        if ($rules) {
            return Validator::make((array)$this, $rules);
        }

        $this->brokenRules = new Collection();
        $this->validate();

        return $this->brokenRules;
    }
}
