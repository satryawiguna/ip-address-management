<?php

namespace App\Application\Exceptions;

use Exception;

class ResponseBadRequestException extends Exception
{
    public array $messages;

    public function __construct(array $messages)
    {
        parent::__construct();
        $this->messages = $messages;
    }

    public function getMessages() {
        return $this->messages;
    }
}
