<?php

namespace App\Repository\Contract;

interface IUserRepository
{
    public function allSearch(string $keyword);

    public function allSearchPage(string $keyword, int $perPage, int $page);
}
