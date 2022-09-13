<?php

namespace App\Repository\Contract;

use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Core\Domain\BaseEntity;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface IIpAddressRepository
{
    public function allSearch(string $keyword, string $order = "id", string $sort = "asc", array $args = []): Collection;

    public function allSearchPage(string $keyword, int $perPage, int $page, string $order = "id", string $sort = "asc", array $args = []): Paginator;

    public function findById(int|string $id): BaseEntity|null;

    public function save(CreateIpAddressDataRequest $request): BaseEntity;

    public function update(UpdateIpAddressDataRequest $request): BaseEntity|null;

    public function delete(int $id): int;
}
