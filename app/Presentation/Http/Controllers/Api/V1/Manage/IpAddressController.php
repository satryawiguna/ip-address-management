<?php

namespace App\Presentation\Http\Controllers\Api\V1\Manage;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\CreateLabelDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Application\Request\UpdateLabelDataRequest;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Presentation\Http\Controllers\Api\ApiBaseController;
use App\Service\Contract\IManageService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

        $labels = new Collection();

        if (is_array($request->input("label"))) {
            foreach ($request->input("label") as $label) {
                $createLabelDataRequest = new CreateLabelDataRequest();
                $createLabelDataRequest->setId($label["id"]);
                $createLabelDataRequest->setTitle((string) $label["title"]);

                $this->setRequestAuthor($createLabelDataRequest);

                $labels->push($createLabelDataRequest);
            }
        }

        $createIpAddressDataRequest->setLabel($labels);

        $this->setRequestAuthor($createIpAddressDataRequest);

        $storeIpAddressResponse = $this->manageService->storeIpAddress($createIpAddressDataRequest);

        if ($storeIpAddressResponse->isError()) {
            return $this->getErrorJsonResponse($storeIpAddressResponse);
        }

        return $this->getObjectJsonResponse($storeIpAddressResponse);
    }

    public function actionUpdate(Request $request, int $id) {
        $updateIpAddressDataRequest = new UpdateIpAddressDataRequest();
        $updateIpAddressDataRequest->setId($id);

        $labels = new Collection();

        if (is_array($request->input("label"))) {
            foreach ($request->input("label") as $label) {
                $updateLabelDataRequest = new UpdateLabelDataRequest();
                $updateLabelDataRequest->setId($label["id"]);
                $updateLabelDataRequest->setTitle((string) $label["title"]);

                $this->setRequestAuthor($updateLabelDataRequest);

                $labels->push($updateLabelDataRequest);
            }
        }

        $this->setRequestAuthor($updateIpAddressDataRequest);

        $updateIpAddressResponse = $this->manageService->updateIpAddress($updateIpAddressDataRequest);

        if ($updateIpAddressResponse->isError()) {
            return $this->getErrorJsonResponse($updateIpAddressResponse);
        }

        return $this->getObjectJsonResponse($updateIpAddressResponse);
    }

    public function actionGet(int $id) {
        $ipAddress = $this->manageService->getIpAddressById($id);

        if ($ipAddress->isError()) {
            return $this->getErrorJsonResponse($ipAddress);
        }

        return $this->getObjectJsonResponse($ipAddress);
    }
}
