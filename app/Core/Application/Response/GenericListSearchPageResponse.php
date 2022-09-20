<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class GenericListSearchPageResponse extends BasicResponse
{
    private Collection $_dtoListSearchPage;

    public Collection $dtoListSearchPage;

    public int $totalCount;

    public array $meta;

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getDtoListSearchPage(): Collection
    {
        return $this->_dtoListSearchPage ?? $this->_dtoListSearchPage = new Collection();
    }
}
