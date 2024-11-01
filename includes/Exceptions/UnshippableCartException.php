<?php

namespace Simpler\Exceptions;

use Throwable;

class UnshippableCartException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'UNSHIPPABLE_CART', $code, $previous);
    }
}
