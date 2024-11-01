<?php

namespace Simpler\Exceptions;

use Throwable;

class InvalidProductException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'INVALID_PRODUCT', $code, $previous);
    }
}
