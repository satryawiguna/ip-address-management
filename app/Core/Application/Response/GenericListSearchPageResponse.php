<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class GenericListSearchPageResponse extends BasicResponse
{
    public int $totalCount;

    public array $meta;

    public Collection $dtoListSearchPage;

    public Collection $_dtoListSearchPage;

    public function getDtoListSearchPage(): Collection
    {
        return $this->_dtoListSearchPage ?? $this->_dtoListSearchPage = new Collection();
    }
}
