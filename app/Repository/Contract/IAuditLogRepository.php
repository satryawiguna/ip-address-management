<?php

namespace App\Repository\Contract;

use App\Application\Log\LogActivity;
use App\Core\Domain\BaseEntity;
use Illuminate\Support\Collection;

interface IAuditLogRepository
{
    public function allSearch(string $keyword, string $order = "id", string $sort = "asc", array $args = []): Collection;

    public function allSearchPage(string $keyword, int $perPage, int $page, string $order = "id", string $sort = "asc", array $args = []): Collection;

    public function writeLogActivity(LogActivity $request): BaseEntity;
}
