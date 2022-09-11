<?php

namespace App\Presentation\Http\Controllers\Api\V1\Auth;

use App\Application\Request\Auth\RegisterDataRequest;
use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\IUserService;

class UserController extends ApiBaseController
{
    public IUserService $userManagementService;

    public function __construct(IUserService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function actionRegister(RegisterDataRequest $request) {
        dd($request);
    }
}
