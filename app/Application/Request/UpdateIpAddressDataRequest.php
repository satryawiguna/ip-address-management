<?php

namespace App\Application\Request;

use App\Core\Application\Request\IdentityableRequest;

class UpdateIpAddressDataRequest extends IdentityableRequest
{
    public string $ipv4;

    public string $label;

    public function getIpv4(): string
    {
        return $this->ipv4;
    }

    public function setIpv4(string $ipv4): void
    {
        $this->ipv4 = $ipv4;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    protected function validate(): void {}
}
