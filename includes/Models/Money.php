<?php

namespace Simpler\Models;

class Money
{
    public static function to_cents($amount): int
    {
        return intval(strval(\wc_cart_round_discount($amount, 2) * 100));
    }

    public static function from_cents($amount): float
    {
        return \wc_cart_round_discount($amount / 100.0, 2);
    }
}
