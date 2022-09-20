<?php

namespace App\Domain;

use App\Core\Domain\BaseEntity;

class OAuthPersonalAccessClient extends BaseEntity
{
    const TABLE_NAME = 'oauth_personal_access_clients';
    const MORPH_NAME = 'oauth_personal_access_clients';

    protected $table = OAuthPersonalAccessClient::TABLE_NAME;
}
