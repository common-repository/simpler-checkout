<?php

namespace Simpler\Exceptions;

use Throwable;

class InvalidOrderException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'INVALID_ORDER', $code, $previous);
    }
}
