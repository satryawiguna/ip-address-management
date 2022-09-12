<?php

namespace App\Repository;

use App\Core\Application\Request\AuditableRequest;
use App\Core\Domain\BaseEntity;
use App\Core\Domain\Contract\IRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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

    public function all(string $order = "id", string $sort = "asc"): Collection {
        return $this->model
            ->orderBy($order, $sort)
            ->get();
    }

    public function findById(int|string $id): BaseEntity|null {
        return $this->model->find($id);
    }

    protected function setAuditableInformationFromRequest(BaseEntity|array $entity, AuditableRequest $request)
    {
        if ($entity instanceof BaseEntity) {
            if (!$entity->getKey()) {
                $entity->setCreatedInfo($request->request_by);
            } else {
                $entity->setUpdatedInfo($request->request_by);
            }
        }

        if (is_array($entity)) {
            if (!array_key_exists('id', $entity) || $entity['id'] == 0) {
                $entity['created_by'] = $request->request_by;
                $entity['created_at'] = Carbon::now()->toDateTimeString();
            } else {
                $entity['updated_by'] = $request->request_by;
                $entity['updated_at'] = Carbon::now()->toDateTimeString();
            }

            return $entity;
        }
    }
}
