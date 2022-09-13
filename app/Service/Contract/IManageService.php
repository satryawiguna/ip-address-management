<?php

namespace App\Service\Contract;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
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

    public function getIpAddressById(int $id): GenericObjectResponse;

    public function getIpAddressSearch(SearchRequest $searchRequest): GenericListSearchResponse;

    public function getIpAddressSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse;

    public function storeIpAddress(CreateIpAddressDataRequest $request): GenericObjectResponse;

    public function updateIpAddress(UpdateIpAddressDataRequest $request): GenericObjectResponse;

    public function destroyIpAddress(int $id): BasicResponse;


    public function getLabelAll(): GenericListResponse;
}
