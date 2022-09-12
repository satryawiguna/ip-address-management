<?php

namespace App\Application\Request\Auth;

use App\Core\Domain\BrokenRule;

class LoginDataRequest
{
    use BrokenRule;

    public ?string $email;

    public ?string $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    protected function validate(): void {}
}
