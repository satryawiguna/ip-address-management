<?php

namespace App\Core\Application\Response;

enum HttpResponseType: int
{
    case SUCCESS = 200;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case UNSUPPORT_MEDIA = 415;
    case INTERNAL_SERVER_ERROR = 500;
}
