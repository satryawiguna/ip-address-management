<?php

namespace App\Core\Application\Response;

use Illuminate\Support\Collection;

class GenericListResponse extends BasicResponse
{
    public Collection $dtoList;

    public Collection $_dtoList;

    public function getDtoList(): Collection
    {
        return $this->_dtoList ?? $this->_dtoList = new Collection();
    }
}
