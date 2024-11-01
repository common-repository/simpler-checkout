<?php

namespace Simpler\Admin\Http\Controllers;

class OffersController extends Controller
{
    public static function register()
    {
        register_setting('simpler_offers', 'simplerwc_should_render_sale_ribbon', [
            'type' => 'boolean',
            'default' => false
        ]);

        register_setting('simpler_offers', 'simplerwc_sale_ribbon_text', [
            'type' => 'string'
        ]);
    }

    public function settings()
    {
        $this->render('settings.offers._form', [
            'should_render_sale_ribbon_checked' => checked(1, get_option('simplerwc_should_render_sale_ribbon'), false),
            'sale_ribbon_text' => esc_html(get_option('simplerwc_sale_ribbon_text'))
        ]);
    }
}
