<?php

const SIMPLERWC_VERSION = '1.0.3';

function simplerwc_get_sdk_uri()
{
    switch (get_option('simpler_environment')) {
        case 'development':
            return 'https://sdk.local.simpler.so/development/woo/simpler-checkout.js';
            // use with web-sdk `npm run preview` for live debugging
            // return 'http://localhost:4173/staging/woo/simpler-checkout.js';
        case 'sandbox':
            return 'https://cdn.simpler.so/sdk/staging/woo/simpler-checkout.js';
        default:
            return 'https://cdn.simpler.so/sdk/woo/simpler-checkout.js';
    }
}

function simplerwc_get_refund_uri()
{
    switch (get_option('simpler_environment')) {
        case 'development':
            return 'http://merchant-api.simpler.test/api/v1/refunds';
        case 'sandbox':
            return 'https://merchant.staging.simpler.so/api/v1/refunds';
        default:
            return 'https://merchant.simpler.so/api/v1/refunds';
    }
}

function simplerwc_get_integration_status_uri()
{
    switch (get_option('simpler_environment')) {
        case 'development':
            return 'http://merchant-api.simpler.test/api/v1/integrations/status';
        case 'sandbox':
            return 'https://merchant.staging.simpler.so/api/v1/integrations/status';
        default:
            return 'https://merchant.simpler.so/api/v1/integrations/status';
    }
}
