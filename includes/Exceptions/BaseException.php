<?php

namespace Simpler\Exceptions;

use Throwable;

class BaseException extends \Exception
{
    /**
     * Simpler's internal code to identify these errors.
     *
     * @var string
     */
    private $errorCode;

    public function __construct($message = "", string $errorCode = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function get_error_code(): string
    {
        return $this->errorCode;
    }
}