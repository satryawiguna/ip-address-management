<?php

namespace App\Application\Request;

use App\Core\Application\Request\IdentityableRequest;

class UpdateLabelDataRequest extends IdentityableRequest
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
}
