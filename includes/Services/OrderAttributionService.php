<?php

namespace Simpler\Services;

class OrderAttributionService
{
    public static function save_attribution_data($order, $cookies)
    {
        $attribution_data = array_merge(
            [],
            self::parse_cookie_values($cookies['sbjs_current'] ?? ''),
            self::parse_cookie_values($cookies['sbjs_current_add'] ?? ''),
            self::parse_cookie_values($cookies['sbjs_session'] ?? ''),
            self::parse_cookie_values($cookies['sbjs_udata'] ?? '')
        );

        $attribution_params = [
            'source_type' => $attribution_data['typ'] ?? '',
            'referrer' => $attribution_data['rf'] ?? '',
            'utm_campaign' => $attribution_data['cmp'] ?? '',
            'utm_medium' => $attribution_data['mdm'] ?? '',
            'utm_content' => $attribution_data['cnt'] ?? '',
            'utm_id' => $attribution_data['id'] ?? '',
            'utm_term' => $attribution_data['trm'] ?? '',
            'utm_source' => $attribution_data['src'] ?? '',
            'session_entry' => $attribution_data['ep'] ?? '',
            'session_start_time' => $attribution_data['fd'] ?? '',
            'session_pages' => $attribution_data['pgs'] ?? '',
            'session_count' => $attribution_data['vst'] ?? '',
            'user_agent' => $attribution_data['uag'] ?? ''
        ];

        do_action('woocommerce_order_save_attribution_data', $order, $attribution_params);
    }

    private static function parse_cookie_values($cookie)
    {
        $values = [];
        foreach (explode("|||", $cookie) as $val) {
            $kv = explode("=", $val);
            if (count($kv) == 2) {
              $values[$kv[0]] = $kv[1];
            }
        }
        return $values;
    }
}
