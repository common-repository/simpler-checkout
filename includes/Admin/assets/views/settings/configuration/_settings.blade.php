<h2>Credentials</h2>
<hr class="divider">


<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Simpler API Key</th>
            <td>
                <input name="simpler_api_key" id="simpler_api_key" type="text" style="min-width: 35%;" value="{{ $apiKey }}" />
            </td>
        </tr>
        <tr>
            <th scope="row">Simpler API Secret</th>
            <td>
                <input name="simpler_api_secret" id="simpler_api_key" type="text" style="min-width: 35%;" value="{{ $apiSecret }}" />
            </td>
        </tr>
    </tbody>
</table>

<h2>Advanced Settings</h2>
<hr class="divider">

<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Show button only to Administrators</th>
            <td>
                <input type="checkbox" id="simpler_checkout_test_mode" name="simpler_checkout_test_mode" value="1" {{ $testModeChecked }} />
                <label for="simpler_checkout_test_mode">Show button only to administrators</label>
                <div><small>Enable this to ensure only logged in administrators will be able to see the Simpler Checkout
                        buttons. Useful when debugging so as to not affect your users.</small></div>
            </td>
        </tr>
        <tr>
            <th scope="row">Experimental Support for WooCommerce Order Attribution</th>
            <td>
                <input type="checkbox" id="simplerwc_support_woo_order_attribution" name="simplerwc_support_woo_order_attribution" value="1" {{ $supportWooAttribution }} />
                <label for="simplerwc_support_woo_order_attribution">Experimental Support for WooCommerce Order Attribution</label>
                <div><small>If you're on woo 8.5+ you can try our experimental integration with the woocommerce order attribution module.</small></div>
            </td>
        </tr>
    </tbody>
</table>