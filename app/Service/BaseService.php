<?php

namespace App\Service;

use App\Core\Application\Request\AuditableRequest;
use App\Core\Domain\BaseEntity;
use Carbon\Carbon;

abstract class BaseService
{
    protected function setAuditableInformationFromRequest(BaseEntity|array $entity, AuditableRequest $request)
    {
        if ($entity instanceof BaseEntity) {
            if (!$entity->getKey()) {
                $entity->setCreatedInfo($request->request_by);
            } else {
                $entity->setUpdatedInfo($request->request_by);
            }
        }

        // Usage for relation
        if (is_array($entity)) {
            if (!array_key_exists('id', $entity) || $entity['id'] == 0) {
                $entity['created_by'] = $request->request_by;
                $entity['created_at'] = Carbon::now()->toDateString();
            } else {
                $entity['updated_by'] = $request->request_by;
                $entity['updated_at'] = Carbon::now()->toDateString();
            }

            return $entity;
        }
    }
}
