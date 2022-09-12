<?php

namespace App\Application\Request\Auth;

use App\Core\Domain\BrokenRule;

class LogoutDataRequest
{
    use BrokenRule;

    public ?string $email;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    protected function validate(): void {}
}
