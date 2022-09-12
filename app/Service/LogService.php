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
use Illuminate\Support\Facades\Log;

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
            $allResponse = $this->auditLogRepository->all();

            $response->dtoList = $allResponse;
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

    public function getAllSearch(SearchRequest $searchRequest): GenericListSearchResponse
    {
        $response = new GenericListSearchResponse();

        try {
            $allResponse = $this->auditLogRepository->allSearch($searchRequest->getSearch());

            $response->dtoListSearch = $allResponse;
            $response->totalCount = $allResponse->count();
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

    public function getAllSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse
    {
        $response = new GenericListSearchPageResponse();

        try {
            $allResponse = $this->auditLogRepository->allSearchPage($searchPageRequest->getSearch(),
                $searchPageRequest->getPerPage(),
                $searchPageRequest->getPage());

            $response->dtoListSearch = $allResponse;
            $response->totalCount = $allResponse->count();
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

}
