<?php

namespace App\Application\Request;

use App\Core\Application\Request\AuditableRequest;
use Illuminate\Support\Collection;

class CreateIpAddressDataRequest extends AuditableRequest
{
    public string $ipv4;

    public Collection $label;

    public function getIpv4(): string
    {
        return $this->ipv4;
    }

    public function setIpv4(string $ipv4): void
    {
        $this->ipv4 = $ipv4;
    }

    public function getLabel(): Collection
    {
        return $this->label;
    }

    public function setLabel(Collection $label): void
    {
        $this->label = $label;
    }
}
