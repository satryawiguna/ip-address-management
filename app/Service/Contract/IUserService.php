<?php

namespace App\Service\Contract;

use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Application\Response\GenericObjectResponse;

interface IUserService
{
    public function register(RegisterDataRequest $request): GenericObjectResponse;
}
