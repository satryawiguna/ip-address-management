<?php

namespace App\Core\Application\Request;

class IdentityableRequest extends AuditableRequest
{
    public int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
