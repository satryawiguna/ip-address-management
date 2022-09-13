<?php

namespace App\Presentation\Http\Controllers\Api\V1\Manage;

use App\Application\Request\CreateLabelDataRequest;
use App\Application\Request\UpdateLabelDataRequest;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
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

    public function actionSearch(Request $request) {
        $searchRequest = new SearchRequest();
        $searchRequest->setSearch((string) $request->input("search"));

        $labels = $this->manageService->getLabelSearch($searchRequest);

        if ($labels->isError()) {
            return $this->getErrorJsonResponse($labels);
        }

        return $this->getListSearchJsonResponse($labels);
    }

    public function actionSearchPage(Request $request, $perPage, $page) {
        $searchPageRequest = new SearchPageRequest();
        $searchPageRequest->setSearch((string) $request->input("search"));
        $searchPageRequest->setPerPage($perPage);
        $searchPageRequest->setPage($page);

        $labels = $this->manageService->getLabelSearchPage($searchPageRequest);

        if ($labels->isError()) {
            return $this->getErrorJsonResponse($labels);
        }

        return $this->getListSearchPageJsonResponse($labels);
    }

    public function actionGet(int $id) {
        $label = $this->manageService->getLabelById($id);

        if ($label->isError()) {
            return $this->getErrorJsonResponse($label);
        }

        return $this->getObjectJsonResponse($label);
    }

    public function actionStore(Request $request) {
        $createLabelDataRequest = new CreateLabelDataRequest();
        $createLabelDataRequest->setTitle((string) $request->input("title"));

        $this->setRequestAuthor($createLabelDataRequest);

        $storeLabelResponse = $this->manageService->storeLabel($createLabelDataRequest);

        if ($storeLabelResponse->isError()) {
            return $this->getErrorJsonResponse($storeLabelResponse);
        }

        return $this->getObjectJsonResponse($storeLabelResponse);
    }

    public function actionUpdate(Request $request, int $id) {
        $updateLabelDataRequest = new UpdateLabelDataRequest();
        $updateLabelDataRequest->setId($id);
        $updateLabelDataRequest->setTitle($request->input("title"));

        $this->setRequestAuthor($updateLabelDataRequest);

        $updateLabelResponse = $this->manageService->updateLabel($updateLabelDataRequest);

        if ($updateLabelResponse->isError()) {
            return $this->getErrorJsonResponse($updateLabelResponse);
        }

        return $this->getObjectJsonResponse($updateLabelResponse);
    }

    public function actionDestroy(int $id) {
        $destroyLabelResponse = $this->manageService->destroyLabel($id);

        if ($destroyLabelResponse->isError()) {
            return $this->getErrorJsonResponse($destroyLabelResponse);
        }

        return $this->getSuccessLatestJsonResponse($destroyLabelResponse);
    }
}
