<?php

namespace App\Presentation\Http\Controllers\Api\V1\Manage;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\IManageService;
use Illuminate\Http\Request;

class IpAddressController extends ApiBaseController
{
    public IManageService $manageService;

    public function __construct(IManageService $manageService)
    {
        $this->manageService = $manageService;
    }

    public function actionAll() {
        $ipAddresses = $this->manageService->getIpAddressAll();

        if ($ipAddresses->isError()) {
            return $this->getErrorJsonResponse($ipAddresses);
        }

        return $this->getListJsonResponse($ipAddresses);
    }

    public function actionSearch(Request $request) {
        $searchRequest = new SearchRequest();
        $searchRequest->setSearch((string) $request->input("search"));

        $ipAddresses = $this->manageService->getIpAddressSearch($searchRequest);

        if ($ipAddresses->isError()) {
            return $this->getErrorJsonResponse($ipAddresses);
        }

        return $this->getListSearchJsonResponse($ipAddresses);
    }

    public function actionSearchPage(Request $request, $perPage, $page) {
        $searchPageRequest = new SearchPageRequest();
        $searchPageRequest->setSearch((string) $request->input("search"));
        $searchPageRequest->setPerPage($perPage);
        $searchPageRequest->setPage($page);

        $ipAddresses = $this->manageService->getIpAddressSearchPage($searchPageRequest);

        if ($ipAddresses->isError()) {
            return $this->getErrorJsonResponse($ipAddresses);
        }

        return $this->getListSearchPageJsonResponse($ipAddresses);
    }

    public function actionStore(Request $request) {
        $createIpAddressDataRequest = new CreateIpAddressDataRequest();
        $createIpAddressDataRequest->setIpv4((string) $request->input("ipv4"));
        $createIpAddressDataRequest->setLabel((string) $request->input("label"));

        $this->setRequestAuthor($createIpAddressDataRequest);

        $storeIpAddressResponse = $this->manageService->storeIpAddress($createIpAddressDataRequest);

        if ($storeIpAddressResponse->isError()) {
            return $this->getErrorJsonResponse($storeIpAddressResponse);
        }

        return $this->getObjectJsonResponse($storeIpAddressResponse);
    }
}
