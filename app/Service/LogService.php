<?php

namespace App\Service;

use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Repository\Contract\IAuditLogRepository;
use App\Service\Contract\ILogService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class LogService implements ILogService
{
    public IAuditLogRepository $auditLogRepository;


    public function __construct(IAuditLogRepository $auditLogRepository)
    {
        $this->auditLogRepository = $auditLogRepository;
    }


    public function getAll(): GenericListResponse
    {
        $response = new GenericListResponse();

        try {
            $auditLogs = $this->auditLogRepository->all();

            $response->dtoList = $auditLogs;
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getSearch(SearchRequest $searchRequest): GenericListSearchResponse
    {
        $response = new GenericListSearchResponse();

        try {
            $cached = Redis::get('log:search:' . $searchRequest->getSearch());

            if ($cached) {
                $auditLogs = new Collection(json_decode($cached, FALSE));
            } else {
                $auditLogs = $this->auditLogRepository->allSearch($searchRequest->getSearch());

                Redis::set('log:search:' . $searchRequest->getSearch(), json_encode($auditLogs->toArray()));
            }

            $response->dtoListSearch = $auditLogs;
            $response->totalCount = $auditLogs->count();
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse
    {
        $response = new GenericListSearchPageResponse();

        try {
            $auditLogs = $this->auditLogRepository->allSearchPage($searchPageRequest->getSearch(),
                $searchPageRequest->getPerPage(),
                $searchPageRequest->getPage());

            $response->dtoListSearchPage = $auditLogs;
            $response->totalCount = $auditLogs->count();
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
        }

        return $response;
    }

}
