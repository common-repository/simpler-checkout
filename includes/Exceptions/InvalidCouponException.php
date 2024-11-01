<?php

namespace Simpler\Exceptions;

use Throwable;

class InvalidCouponException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'INVALID_COUPON', $code, $previous);
    }
}
