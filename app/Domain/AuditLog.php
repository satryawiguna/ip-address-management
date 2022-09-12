<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;

class AuditLog extends BaseEntity
{
    const TABLE_NAME = 'audit_logs';
    const MORPH_NAME = 'audit_logs';

    protected $table = AuditLog::TABLE_NAME;

    protected $fillable = [
        'audi_logable_id',
        'audi_logable_type',
        'level',
        'tag',
        'context',
        'created_by',
        'updated_by'
    ];

    public $timestamps = false;

    protected $dates = [
        'deleted_at'
    ];
}
