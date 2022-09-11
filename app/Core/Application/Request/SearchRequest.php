<?php

namespace App\Core\Application\Request;

class SearchRequest
{
    public string $search;

    public string $orderBy;

    public string $sort = 'ASC';

    public function getSearch(): string
    {
        return $this->search;
    }

    public function setSearch(string $search): void
    {
        $this->search = $search;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }
}
