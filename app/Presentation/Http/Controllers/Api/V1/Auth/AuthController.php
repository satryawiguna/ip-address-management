<?php

namespace App\Presentation\Http\Controllers\Api\V1\Auth;

use App\Application\Request\Auth\LoginDataRequest;
use App\Application\Request\Auth\LogoutDataRequest;
use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\IAuthService;
use App\Service\Contract\IUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends ApiBaseController
{
    public IAuthService $authService;
    public IUserService $userService;

    public function __construct(IAuthService $authService,
        IUserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function actionLogin(Request $request) {
        $loginDataRequest = new LoginDataRequest();
        $loginDataRequest->setEmail($request->input("email"));
        $loginDataRequest->setPassword($request->input("password"));

        $loginResponse = $this->authService->login($loginDataRequest);

        if ($loginResponse->isError()) {
            return $this->getErrorJsonResponse($loginResponse);
        }

        Cookie::queue('refresh_token', $loginResponse->dto->token["refresh_token"], 60*24);

        return $this->getObjectJsonResponse($loginResponse);
    }

    public function actionLogout(Request $request) {
        $logoutDataRequest = new LogoutDataRequest();
        $logoutDataRequest->setEmail($request->input("email"));

        $logoutResponse = $this->authService->logout($logoutDataRequest);

        if ($logoutResponse->isError()) {
            return $this->getErrorJsonResponse($logoutResponse);
        }

        $request->user()->token()->revoke();
        Cookie::forget('refresh_token');

        return $this->getSuccessLatestJsonResponse($logoutResponse);
    }
}
