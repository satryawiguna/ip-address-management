<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;

class AuditLog extends BaseEntity
{
    const TABLE_NAME = 'audit_logs';
    const MORPH_NAME = 'audit_logs';

    protected $table = AuditLog::TABLE_NAME;

    protected $fillable = [
        'audit_logable_id',
        'audit_logable_type',
        'level',
        'logged_at',
        'message',
        'context'
    ];

    public $timestamps = false;

    public function audit_logable() {
        return $this->morphTo();
    }
}
