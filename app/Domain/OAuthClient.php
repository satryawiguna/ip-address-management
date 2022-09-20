<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;

class OAuthClient extends BaseEntity
{
    const TABLE_NAME = 'oauth_clients';
    const MORPH_NAME = 'oauth_clients';

    protected $table = OAuthClient::TABLE_NAME;

    protected $keyType = 'string';
}
