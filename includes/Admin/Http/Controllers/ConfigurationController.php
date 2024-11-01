<?php

namespace Simpler\Admin\Http\Controllers;

use Simpler\Services\BladeService;
use Simpler\Services\IntegrationService;

class ConfigurationController extends Controller
{
    /**
     * @var IntegrationService
     */
    private $integrationService;

    public function __construct(BladeService $blade, IntegrationService $service)
    {
        parent::__construct($blade);
        $this->integrationService = $service;
    }

    /**
     * Registers settings fields
     */
    public static function register()
    {
        register_setting('simpler_management', 'simpler_environment', [
            'type' => 'string',
            'default' => 'production'
        ]);
        register_setting('simpler_management', 'simpler_api_key', [
            'type' => 'string',
            'default' => '',
        ]);

        register_setting('simpler_management', 'simpler_api_secret', [
            'type' => 'string',
            'default' => '',
        ]);

        register_setting('simpler_management', 'simpler_checkout_test_mode', [
            'type'    => 'number',
            'default' => 1,
        ]);

        register_setting('simpler_management', 'simplerwc_support_woo_order_attribution', [
            'type' => 'number',
            'default' => 0,
        ]);
    }

    /**
     * Renders settings fields
     */
    public function settings()
    {
        $this->render(
            'settings.configuration._settings',
            [
                'apiKey'          => esc_html(get_option('simpler_api_key')),
                'apiSecret'       => esc_html(get_option('simpler_api_secret')),
                'testModeChecked' => checked(1, get_option('simpler_checkout_test_mode'), false),
                'supportWooAttribution' => checked(1, get_option('simplerwc_support_woo_order_attribution'), false)
            ]
        );
    }

    /**
     * Renders integration status
     */
    public function integrationStatus()
    {
        $this->render('settings.configuration._status', ['checks' => $this->integrationService->status()]);
    }
}
