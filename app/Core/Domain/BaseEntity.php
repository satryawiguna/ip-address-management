<?php

namespace App\Core\Domain;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseEntity extends Model
{
    protected $primaryKey = 'id';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getPrimaryKey(): int|string
    {
        return $this->primaryKey;
    }

    public function getCreatedBy(): string
    {
        return $this->getAttribute("created_by");
    }

    public function getCreatedAt(): DateTime
    {
        return $this->getAttribute("created_at");
    }

    public function getUpdatedBy(): string
    {
        return $this->getAttribute("updated_by");
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->getAttribute("updated_by");
    }

    public function setCreatedInfo(string $created_by): void
    {
        $this->setAttribute("created_by", $created_by);
        $this->setAttribute("created_at", Carbon::now()->toDateTimeString());
    }

    public function setUpdatedInfo(string $updated_by): void
    {
        $this->setAttribute("updated_by", $updated_by);
        $this->setAttribute("updated_at", Carbon::now()->toDateTimeString());
    }
}
