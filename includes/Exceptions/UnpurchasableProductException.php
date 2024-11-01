<?php

namespace Simpler\Exceptions;

use Throwable;

class UnpurchasableProductException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'UNPURCHASABLE_PRODUCT', $code, $previous);
    }
}
