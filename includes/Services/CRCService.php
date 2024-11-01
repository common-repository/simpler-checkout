<?php

namespace Simpler\Services;

class CRCService
{
    public static function sign(string $value, string $secret)
    {
        return hash_hmac("sha1", $value, $secret);
    }
}