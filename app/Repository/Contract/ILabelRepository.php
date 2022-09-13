<?php

namespace App\Repository\Contract;

use App\Application\Request\CreateLabelDataRequest;
use App\Application\Request\UpdateLabelDataRequest;
use App\Core\Domain\BaseEntity;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface ILabelRepository
{
    public function allSearch(string $keyword, string $order = "id", string $sort = "asc", array $args = []): Collection;

    public function allSearchPage(string $keyword, int $perPage, int $page, string $order = "id", string $sort = "asc", array $args = []): Paginator;

    public function findByTitle(string $title): BaseEntity|null;

    public function save(CreateLabelDataRequest $request): BaseEntity;

    public function update(UpdateLabelDataRequest $request): BaseEntity|null;

    public function delete(int $id): int;
}
