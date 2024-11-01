<?php

namespace Simpler\Exceptions;

use Throwable;

class IndividuallySoldProductException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'INDIVIDUALLY_SOLD_PRODUCT', $code, $previous);
    }
}
