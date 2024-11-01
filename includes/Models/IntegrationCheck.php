<?php

namespace Simpler\Models;

use Simpler\Services\IntegrationService;

class IntegrationCheck
{
    public $title = '';
    public $status = '';
    public $message = '';
    public $actionUrl = '';
    public $actionLabel = '';
    public $renderingOrder = 0;

    /**
     * Indicates whether the check has succeeded.
     *
     * @return bool
     */
    public function isNotSuccessful(): bool
    {
        return $this->status == IntegrationService::STATUS_FAIL || $this->status == IntegrationService::STATUS_UNKNOWN;
    }
}