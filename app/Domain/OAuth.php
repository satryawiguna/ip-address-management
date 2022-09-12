<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;

class OAuth extends BaseEntity
{
    const TABLE_NAME = 'oauth_access_tokens';
    const MORPH_NAME = 'oauth_access_tokens';

    protected $table = OAuth::TABLE_NAME;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
