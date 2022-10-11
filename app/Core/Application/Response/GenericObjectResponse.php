<?php

namespace App\Core\Application\Response;

class GenericObjectResponse extends BasicResponse
{
    private $_dto;

    public $dto;

    public function getDto()
    {
        return $this->dto ?? $this->_dto = new \stdClass();
    }
}
