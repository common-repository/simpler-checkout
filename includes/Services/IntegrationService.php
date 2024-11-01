<?php

namespace Simpler\Services;

use Simpler\Models\IntegrationCheck;
use WP_Http;

class IntegrationService
{
    const STATUS_UNKNOWN = 'unknown';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    /**
     * Checks the integration status with simpler.so.
     *
     * @return IntegrationCheck[]
     */
    public function status(): array
    {
        $response                       = $this->sendHttpStatusRequest();
        $checks                         = [];
        $check                          = $this->checkSimplerReachability($response);
        $checks[$check->renderingOrder] = $check;
        $check                          = $this->checkSimplerCredentials($response, $check);
        $checks[$check->renderingOrder] = $check;
        $check                          = $this->checkWooApiKey($response, $check);
        $checks[$check->renderingOrder] = $check;
        $check                          = $this->checkStoreReachability($response, $check);
        $checks[$check->renderingOrder] = $check;
        $check                          = $this->checkMerchantCanProcess($response, $check);
        $checks[$check->renderingOrder] = $check;

        ksort($checks);

        return $checks;
    }

    /**
     * Sends an HTTP request that validates integration status
     *
     * @return array|\WP_Error
     */
    private function sendHttpStatusRequest()
    {
        if (!get_option('simpler_api_key') || !get_option('simpler_api_secret')) {
            return false;
        }
        $signedKey = CRCService::sign($key = get_option('simpler_api_key'), get_option('simpler_api_secret'));
        return (new WP_Http())->post(simplerwc_get_integration_status_uri(), [
            'body'     => json_encode(['key' => $key, 'secret' => $signedKey]),
            'headers'  => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept'       => 'application/json; charset=utf-8',
            ],
            'blocking' => true,
        ]);
    }

    /**
     * Checks if HTTP request can reach simpler.so
     *
     * @param  array|\WP_Error  $response
     *
     * @return IntegrationCheck
     */
    private function checkSimplerReachability($response): IntegrationCheck
    {
        $check                 = new IntegrationCheck();
        $check->title          = 'Can access Simpler servers';
        $check->renderingOrder = 1;
        if ($response instanceof \WP_Error) {
            $check->status  = self::STATUS_FAIL;
            $check->message = implode(',', $response->get_error_messages());
        } else if ($response === false) {
            $check->status = self::STATUS_FAIL;
            $check->message = 'API key & secret not supplied. Contact us to get your api key to begin your integration.';
        } else {
            $check->status = self::STATUS_SUCCESS;
        }

        return $check;
    }

    /**
     * Checks if simpler api key and secret are valid.
     *
     * @param  array|\WP_Error   $response
     * @param  IntegrationCheck  $previousCheck
     *
     * @return IntegrationCheck
     */
    private function checkSimplerCredentials($response, IntegrationCheck $previousCheck): IntegrationCheck
    {
        $check                 = new IntegrationCheck();
        $check->title          = 'Credentials are valid';
        $check->renderingOrder = 2;

        if ($previousCheck->isNotSuccessful()) {
            $check->status = self::STATUS_UNKNOWN;
            return $check;
        }

        if ($response['response']['code'] === 422) {
            $check->status  = self::STATUS_FAIL;
            $check->message = 'Invalid Simpler API key';
        } elseif ($response['response']['code'] === 403) {
            $check->status  = self::STATUS_FAIL;
            $check->message = 'Invalid Simpler API Secret';
        } elseif ($response['response']['code'] === 404) {
            $check->status  = self::STATUS_FAIL;
            $check->message = 'Merchant does not exist';
        } elseif ($response['response']['code'] === 400) {
            $check->status = self::STATUS_FAIL;
            $check->message = 'Invalid API Key & Secret';
        } else {
            $check->status = self::STATUS_SUCCESS;
        }

        return $check;
    }

    /**
     * Checks if a WooCommerce API key exists and is active
     *
     * @param  array|\WP_Error   $response
     * @param  IntegrationCheck  $previousCheck
     *
     * @return IntegrationCheck
     */
    private function checkWooApiKey($response, IntegrationCheck $previousCheck): IntegrationCheck
    {
        $check                 = new IntegrationCheck();
        $check->title          = 'WooCommerce authorized';
        $check->renderingOrder = 3;

        if ($previousCheck->isNotSuccessful()) {
            $check->status = self::STATUS_UNKNOWN;

            return $check;
        }

        if ($response['response']['code'] === 202 || $response['response']['code'] === 401) {
            $check->status    = self::STATUS_FAIL;
            $check->actionUrl = $this->getWooCommerceAPIAuthorizationURL($response);
            if ($response['response']['code'] === 202) {
                $check->actionLabel = 'Authorize';
                $check->message     = 'WooCommerce API authorization required.';
            } else {
                $check->actionLabel = 'Reissue';
                $check->message     = 'WooCommerce API key reissuing required.';
            }
        } else {
            $check->status = self::STATUS_SUCCESS;
        }

        return $check;
    }

    private function getWooCommerceAPIAuthorizationURL($response)
    {
        $currentFullUrl = home_url($_SERVER['REQUEST_URI']);

        return json_decode($response['body'], true)['authorization_url'] . '?return_url=' . urlencode($currentFullUrl);
    }

    /**
     * Checks if simpler.so can reach the store
     *
     * @param  array|\WP_Error   $response
     * @param  IntegrationCheck  $previousCheck
     *
     * @return IntegrationCheck
     */
    private function checkStoreReachability($response, IntegrationCheck $previousCheck): IntegrationCheck
    {
        $check                 = new IntegrationCheck();
        $check->title          = sprintf('Simpler can access %s', get_bloginfo('name'));
        $check->renderingOrder = 4;

        if ($previousCheck->isNotSuccessful()) {
            $check->status = self::STATUS_UNKNOWN;

            return $check;
        }

        if ($response['response']['code'] === 424) {
            $check->status  = self::STATUS_FAIL;
            $check->message = 'Simpler can not reach your store';
        } else {
            $check->status = self::STATUS_SUCCESS;
        }

        return $check;
    }

    /**
     * Checks if merchant can process transactions
     *
     * @param  array|\WP_Error   $response
     * @param  IntegrationCheck  $previousCheck
     *
     * @return IntegrationCheck
     */
    private function checkMerchantCanProcess($response, IntegrationCheck $previousCheck): IntegrationCheck
    {
        $check                 = new IntegrationCheck();
        $check->title          = 'Can process payments';
        $check->renderingOrder = 5;

        if ($previousCheck->isNotSuccessful()) {
            $check->status = self::STATUS_UNKNOWN;

            return $check;
        }

        if ($response['response']['code'] === 412) {
            $check->status  = self::STATUS_FAIL;
            $check->message = 'Merchant can not process transactions';
        } else {
            $check->status = self::STATUS_SUCCESS;
        }

        return $check;
    }
}
