<?php

namespace App\Application\Request;

use App\Core\Application\Request\AuditableRequest;

class CreateLabeldataRequest extends AuditableRequest
{
    public string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    protected function validate(): void {}
}
