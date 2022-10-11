<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class GenericListSearchResponse extends BasicResponse
{
    private Collection $_dtoListSearch;

    public Collection $dtoListSearch;

    public int $totalCount;

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getDtoListSearch(): Collection
    {
        return $this->dtoListSearch ?? $this->_dtoListSearch = new Collection();
    }
}
