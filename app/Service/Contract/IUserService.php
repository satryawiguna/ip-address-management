<?php

namespace App\Service\Contract;

use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;

interface IUserService
{
    public function getAll();

    public function getAllSearch(SearchRequest $searchRequest);

    public function getAllSearchPage(SearchPageRequest $searchPageRequest);
}
