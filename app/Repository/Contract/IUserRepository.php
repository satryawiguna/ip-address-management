<?php

namespace App\Repository\Contract;


use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Domain\BaseEntity;
use Illuminate\Support\Collection;

interface IUserRepository
{
    public function allSearch(string $keyword): Collection;

    public function allSearchPage(string $keyword, int $perPage, int $page): Collection;

    public function register(RegisterDataRequest $request): BaseEntity;

    public function revokeToken(string $email): BaseEntity|null;
}
