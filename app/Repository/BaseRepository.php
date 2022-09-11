<?php

namespace App\Repository;

use App\Core\Domain\BaseEntity;
use App\Core\Domain\Contract\IRepository;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements IRepository
{
    public BaseEntity $model;

    /**
     * @param BaseEntity $model
     */
    public function __construct(BaseEntity $model)
    {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function read(int|string $id): BaseEntity {
        return $this->model->find($id);
    }
}
