<?php

namespace Simpler\Exceptions;

use Throwable;

class OutOfStockProductException extends BaseException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 'OUT_OF_STOCK_PRODUCT', $code, $previous);
    }
}
