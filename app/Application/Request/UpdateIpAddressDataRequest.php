<?php

namespace App\Application\Request;

use App\Core\Application\Request\IdentityableRequest;
use Illuminate\Support\Collection;

class UpdateIpAddressDataRequest extends IdentityableRequest
{
    public Collection $label;

    public function getLabel(): Collection
    {
        return $this->label;
    }

    public function setLabel(Collection $label): void
    {
        $this->label = $label;
    }
}
