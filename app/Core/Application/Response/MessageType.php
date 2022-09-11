<?php

namespace App\Core\Application\Response;

enum MessageType
{
    case SUCCESS;
    case INFO;
    case WARNING;
    case ERROR;
}
