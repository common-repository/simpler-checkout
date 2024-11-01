<?php

namespace Simpler\Exceptions;

use Throwable;

class InapplicableCouponException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'INAPPLICABLE_COUPON', $code, $previous);
    }
}
