<?php

namespace App\Service\Contract;

use App\Application\Log\LogActivity;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;

interface ILogService
{
    public function getAll(): GenericListResponse;

    public function getSearch(SearchRequest $searchRequest): GenericListSearchResponse;

    public function getSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse;
}
