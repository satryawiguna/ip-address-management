<?php

namespace App\Presentation\Http\Controllers\Api\V1\Manage;

use App\Core\Application\Request\SearchRequest;
use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\ILogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class AuditLogController extends ApiBaseController
{
    public ILogService $logService;

    public function __construct(ILogService $logService)
    {
        $this->logService = $logService;
    }

    public function actionSearch(Request $request) {
        $searchRequest = new SearchRequest();
        $searchRequest->setSearch((string) $request->input("search"));

        $logs = $this->logService->getSearch($searchRequest);

        if ($logs->isError()) {
            return $this->getErrorJsonResponse($logs);
        }

        return $this->getListSearchJsonResponse($logs);
    }
}
