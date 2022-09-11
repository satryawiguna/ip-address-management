<?php

namespace App\Core\Domain\Contract;

use App\Core\Domain\BaseEntity;
use Illuminate\Database\Eloquent\Collection;

interface IRepository
{
    public function all(): Collection;

    public function read(int|string $id): BaseEntity;
}
