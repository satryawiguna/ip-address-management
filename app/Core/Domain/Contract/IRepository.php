<?php

namespace App\Core\Domain\Contract;

use App\Core\Domain\BaseEntity;
use Illuminate\Support\Collection;

interface IRepository
{
    public function all(string $order = "id", string $sort = "asc"): Collection;

    public function findById(int|string $id): BaseEntity|null;
}
