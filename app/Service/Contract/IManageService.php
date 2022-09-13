<?php

namespace App\Service\Contract;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\CreateLabelDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Application\Request\UpdateLabelDataRequest;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;

interface IManageService
{
    public function getIpAddressAll(): GenericListResponse;

    public function getIpAddressSearch(SearchRequest $searchRequest): GenericListSearchResponse;

    public function getIpAddressSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse;

    public function getIpAddressById(int $id): GenericObjectResponse;

    public function storeIpAddress(CreateIpAddressDataRequest $request): GenericObjectResponse;

    public function updateIpAddress(UpdateIpAddressDataRequest $request): GenericObjectResponse;

    public function destroyIpAddress(int $id): BasicResponse;


    public function getLabelAll(): GenericListResponse;

    public function getLabelSearch(SearchRequest $searchRequest): GenericListSearchPageResponse;

    public function getLabelSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse;

    public function getLabelById(int $id): GenericObjectResponse;

    public function storeLabel(CreateLabelDataRequest $request): GenericObjectResponse;

    public function updateLabel(UpdateLabelDataRequest $request): GenericObjectResponse;

    public function destroyLabel(int $id): BasicResponse;
}
