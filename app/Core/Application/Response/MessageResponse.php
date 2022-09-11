<?php

namespace App\Core\Application\Response;

class MessageResponse
{
    public MessageType $messageType;

    public string $text;

    public function __construct(MessageType $messageType, string $text)
    {
        $this->messageType = $messageType;
        $this->text = $text;
    }
}
