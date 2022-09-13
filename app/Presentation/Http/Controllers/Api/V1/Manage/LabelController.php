<?php

namespace App\Presentation\Http\Controllers\Api\V1\Manage;

use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\IManageService;
use Illuminate\Http\Request;

class LabelController extends ApiBaseController
{
    public IManageService $manageService;

    public function __construct(IManageService $manageService)
    {
        $this->manageService = $manageService;
    }

    public function actionAll() {
        $labels = $this->manageService->getLabelAll();

        if ($labels->isError()) {
            return $this->getErrorJsonResponse($labels);
        }

        return $this->getListJsonResponse($labels);
    }
}
