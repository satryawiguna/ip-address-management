<?php

namespace App\Service\Contract;

use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;

interface IUserService
{
    public function getAll(): GenericListResponse;

    public function getAllSearch(SearchRequest $searchRequest): GenericListSearchResponse;

    public function getAllSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse;

    public function register(RegisterDataRequest $request): GenericObjectResponse;
}
