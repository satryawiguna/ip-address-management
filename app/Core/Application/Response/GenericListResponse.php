<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class GenericListResponse extends BasicResponse
{
    private Collection $_dtoList;

    public Collection $dtoList;

    public function getDtoList(): Collection
    {
        return $this->dtoList ?? $this->_dtoList = new Collection();
    }
}
