<?php

namespace Simpler\Exceptions;

use Throwable;

class UnshippableLocationException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'UNSHIPPABLE_LOCATION', $code, $previous);
    }
}
