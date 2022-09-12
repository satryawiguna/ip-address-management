<?php

namespace App\Repository;

use App\Application\Log\LogActivity;
use App\Core\Domain\BaseEntity;
use App\Domain\AuditLog;
use App\Repository\Contract\IAuditLogRepository;
use Illuminate\Support\Collection;

class AuditLogRepository extends BaseRepository implements IAuditLogRepository
{
    public AuditLog $auditLog;

    public function __construct(AuditLog $auditLog)
    {
        parent::__construct($auditLog);
        $this->auditLog = $auditLog;
    }

    public function allSearch(string $keyword, string $order = "id", string $sort = "asc"): Collection
    {
        $parameter = $this->getParameter($keyword);

        return $this->auditLog->whereRaw("(tag LIKE ? OR context LIKE ?)", $parameter)
            ->orderBy($order, $sort)
            ->get();
    }

    public function allSearchPage(string $keyword, int $perPage, int $page, string $order = "id", string $sort = "asc"): Collection
    {
        $parameter = $this->getParameter($keyword);

        return $this->auditLog->whereRaw("(tag LIKE ? OR context LIKE ?)", $parameter)
            ->orderBy($order, $sort)
            ->simplePaginate(perPage: $perPage, page: $page);
    }

    public function writeLogActivity(LogActivity $request): BaseEntity
    {
        $auditLog = new $this->auditLog([
            "audit_logable_id" => $request->getAuditLogableId(),
            "audit_logable_type" => $request->getAuditLogableType(),
            "level" => $request->getLevel(),
            "logged_at" => $request->getLoggedAt(),
            "message" => $request->getMessage(),
            "context" => json_encode($request->getContext())
        ]);

        $auditLog->save();

        return $auditLog->fresh();
    }

    private function getParameter(string $keyword) {
        return [
            'tag' => '%' . $keyword . '%',
            'context' => '%' . $keyword . '%'
        ];
    }
}
