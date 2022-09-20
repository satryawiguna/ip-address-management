<?php

namespace App\Service\Contract;

use App\Application\Request\Auth\LoginDataRequest;
use App\Application\Request\Auth\LogoutDataRequest;
use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericObjectResponse;

interface IAuthService
{
    public function login(LoginDataRequest $request): GenericObjectResponse;

    public function logout(LogoutDataRequest $request): BasicResponse;

    public function refreshToken(string $token): GenericObjectResponse;
}
