<?php

namespace Simpler\Traits;

trait WebhookValidation
{
    public static function validate_crc($request, $secret)
    {
        $hash = hash_hmac("sha1", $request->get_body(), $secret);

        return $hash === $request->get_header('X-Simpler-CRC');
    }
}
