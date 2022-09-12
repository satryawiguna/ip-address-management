<?php

namespace App\Application\Request\Auth;

use App\Core\Application\Request\AuditableRequest;

class RegisterDataRequest extends AuditableRequest
{
    public ?string $full_name;

    public ?string $nick_name;

    public ?string $email;

    public ?string $password;

    public ?string $confirm_password;

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    /**
     * @param string|null $full_name
     */
    public function setFullName(?string $full_name): void
    {
        $this->full_name = $full_name;
    }

    /**
     * @return string|null
     */
    public function getNickName(): ?string
    {
        return $this->nick_name;
    }

    /**
     * @param string|null $nick_name
     */
    public function setNickName(?string $nick_name): void
    {
        $this->nick_name = $nick_name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    /**
     * @param string|null $confirm_password
     */
    public function setConfirmPassword(?string $confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }
}
