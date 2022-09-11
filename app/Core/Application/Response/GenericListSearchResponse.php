<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class GenericListSearchResponse extends BasicResponse
{
    public int $totalCount;

    public Collection $dtoListSearch;

    public Collection $_dtoListSearch;

    public function getDtoListSearch(): Collection
    {
        return $this->_dtoListSearch ?? $this->_dtoListSearch = new Collection();
    }
}
