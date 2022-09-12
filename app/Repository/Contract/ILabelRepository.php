<?php

namespace App\Repository\Contract;

use App\Application\Request\CreateLabeldataRequest;
use App\Core\Domain\BaseEntity;

interface ILabelRepository
{
    public function findByTitle(string $title): BaseEntity|null;

    public function save(CreateLabeldataRequest $request): BaseEntity;
}
