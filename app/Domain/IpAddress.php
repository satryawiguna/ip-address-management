<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpAddress extends BaseEntity
{
    use HasFactory, SoftDeletes;

    const TABLE_NAME = 'ip_addresses';
    const MORPH_NAME = 'ip_addresses';

    protected $table = IpAddress::TABLE_NAME;

    protected $fillable = [
        'ipv4',
        'created_by',
        'updated_by'
    ];

    public function labels() {
        return $this->belongsToMany(Label::class, "ip_address_labels");
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'audit_logable');
    }
}
