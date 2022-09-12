<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends BaseEntity
{
    use HasFactory, SoftDeletes;

    const TABLE_NAME = 'labels';
    const MORPH_NAME = 'labels';

    protected $table = Label::TABLE_NAME;

    protected $fillable = [
        'title',
        'created_by',
        'updated_by'
    ];

    public function ipAddresses() {
        return $this->belongsToMany(IpAddress::class);
    }
}
