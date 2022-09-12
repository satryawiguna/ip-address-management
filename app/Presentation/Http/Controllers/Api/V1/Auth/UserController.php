<?php

namespace App\Presentation\Http\Controllers\Api\V1\Auth;

use App\Application\Request\Auth\RegisterDataRequest;
use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\IUserService;
use Illuminate\Http\Request;

class UserController extends ApiBaseController
{
    public IUserService $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    public function actionRegister(Request $request) {
        $registerDataRequest = new RegisterDataRequest();
        $registerDataRequest->setFullName($request->input("full_name"));
        $registerDataRequest->setNickName($request->input("nick_name"));
        $registerDataRequest->setEmail($request->input("email"));
        $registerDataRequest->setPassword($request->input("password"));
        $registerDataRequest->setConfirmPassword($request->input("confirm_password"));

        $this->setRequestAuthor($registerDataRequest);

        $registerResponse = $this->userService->register($registerDataRequest);

        if ($registerResponse->isError()) {
            return $this->getErrorJsonResponse($registerResponse);
        }

        return $this->getObjectJsonResponse($registerResponse);
    }
}
