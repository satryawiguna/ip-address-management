<?php

namespace App\Core\Domain;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class BaseEntity extends Model
{
    protected $primaryKey = 'id';

    protected string $created_by;

    protected DateTime $created_at;

    protected string $updated_by;

    protected DateTime $updated_at;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getPrimaryKey(): int|string
    {
        return $this->primaryKey;
    }

    protected function setPrimaryKey(int|string $id): void
    {
        $this->primaryKey = $id;
    }

    public function getCreatedBy(): string
    {
        return $this->created_by;
    }

    protected function setCreatedBy(string $created_by): void
    {
        $this->created_by = $created_by;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt($value)
    {
        $this->created_at = $value;
    }

    public function getUpdatedBy(): string
    {
        return $this->updated_by;
    }

    protected function setUpdatedBy(string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($value)
    {
        $this->updated_at = $value;
    }

    public function setCreatedInfo(string $created_by): void
    {
        $this->created_by = $created_by;
        $this->created_at = Carbon::now()->toDateString();
    }

    public function setUpdatedInfo(string $updated_by): void
    {
        $this->updated_by = $updated_by;
        $this->updated_at = Carbon::now()->toDateString();
    }
}
