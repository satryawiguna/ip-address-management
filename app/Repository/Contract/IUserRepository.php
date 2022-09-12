<?php

namespace App\Repository\Contract;


use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Domain\BaseEntity;

interface IUserRepository
{
    public function register(RegisterDataRequest $request): BaseEntity;

    public function revokeToken(string $email): BaseEntity|null;
}
