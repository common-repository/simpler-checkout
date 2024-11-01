<h2>Single Product Checkout Button</h2>
<hr class="divider" />

<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Automatically render button in product page</th>
            <td>
                <div><input type="checkbox" id="simpler_auto_render_product_button" name="simpler_auto_render_product_button" value="1" {{ $autoRenderProductButton }}><label for="simpler_auto_render_product_button">Automatically render checkout button in product
                        pages</label></div>
                <div><small>Disable this if you are using the [simpler-product-checkout] shortcode to render the checkout
                        button.</small></div>
            </td>
        </tr>
        <tr>
            <th scope="row">Product button placement</th>
            <td><select name="simplerwc_product_button_placement">
                    <option value="woocommerce_before_add_to_cart_form" {{ selected($productButtonPlacement, 'woocommerce_before_add_to_cart_form') }}>
                        Before Add To Cart Form
                    </option>
                    <option value="woocommerce_after_add_to_cart_form" {{ selected($productButtonPlacement, 'woocommerce_after_add_to_cart_form') }}>
                        After Add To Cart Form
                    </option>
                    <option value="woocommerce_before_add_to_cart_quantity" {{ selected($productButtonPlacement, 'woocommerce_before_add_to_cart_quantity') }}>
                        Before Quantity
                    </option>
                    <option value="woocommerce_after_add_to_cart_quantity" {{ selected($productButtonPlacement, 'woocommerce_after_add_to_cart_quantity') }}>
                        After Quantity
                    </option>
                    <option value="woocommerce_after_add_to_cart_button" {{ selected($productButtonPlacement, 'woocommerce_after_add_to_cart_button') }}>
                        After Add To Cart Button
                    </option>
                    <option value="woocommerce_after_single_product_summary" {{ selected($productButtonPlacement, 'woocommerce_after_single_product_summary') }}>
                        After Product Summary
                    </option>
                </select>
            </td>
        </tr>
    </tbody>
</table>

<h2>Cart Button</h2>
<hr class="divider">
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Automatically render button in cart page</th>
            <td>
                <div>
                    <input type="checkbox" id="simpler_auto_render_cart_button" name="simpler_auto_render_cart_button" value="1" {{ $autoRenderCartButton }}>
                    <label for="simpler_auto_render_cart_button">Automatically render checkout button in cart page</label>
                </div>
                <div><small>Disable this if you are using the [simpler-cart-checkout] shortcode to render the checkout button.</small></div>
            </td>
        </tr>
        <tr>
            <th scope="row">Cart page button placement</th>
            <td><select name="simplerwc_cart_page_button_placement">
                    <option value="woocommerce_proceed_to_checkout" {{ selected($cartPageButtonPlacement, 'woocommerce_proceed_to_checkout') }}>
                        Before Checkout
                    </option>
                    <option value="woocommerce_after_cart_table" {{ selected($cartPageButtonPlacement, 'woocommerce_after_cart_table') }}>
                        After Cart Review
                    </option>
                    <option value="woocommerce_cart_totals_after_order_total" {{ selected($cartPageButtonPlacement, 'woocommerce_cart_totals_after_order_total') }}>
                        After Order Totals
                    </option>
                </select>
            </td>
        </tr>
    </tbody>
</table>

<h2>Checkout Page</h2>
<hr class="divider">
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Automatically render button in checkout page</th>
            <td>
                <div>
                    <input type="checkbox" id="simplerwc_auto_render_checkout_page_button" name="simplerwc_auto_render_checkout_page_button" value="1" {{ $autoRenderCheckoutPageButton }}>
                    <label for="simplerwc_auto_render_checkout_page_button">Automatically render button in checkout page</label>
                </div>
                <div><small>Disable this if you are using the [simpler-cart-checkout] shortcode to render the checkout button.</small></div>
            </td>
        </tr>
        <tr>
            <th scope="row">Checkout page button placement</th>
            <td><select name="simplerwc_checkout_page_button_placement">
                    <option value="woocommerce_checkout_before_customer_details" {{ selected($checkoutPageButtonPlacement, 'woocommerce_checkout_before_customer_details') }}>
                        Before Customer Details
                    </option>
                    <option value="woocommerce_checkout_before_order_review" {{ selected($checkoutPageButtonPlacement, 'woocommerce_checkout_before_order_review') }}>
                        Before Order Review
                    </option>
                    <option value="woocommerce_review_order_before_payment" {{ selected($checkoutPageButtonPlacement, 'woocommerce_review_order_before_payment') }}>
                        Before Payment
                    </option>
                    <option value="woocommerce_review_order_before_submit" {{ selected($checkoutPageButtonPlacement, 'woocommerce_review_order_before_submit') }}>
                        Before Submit
                    </option>
                </select>
            </td>
        </tr>
    </tbody>
</table>

<h2>Minicart</h2>
<hr class="divider">
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Render quick checkout in minicart widget</th>
            <td>
                <div><input type="checkbox" id="simplerwc_auto_render_minicart_button" name="simplerwc_auto_render_minicart_button" value="1" {{ $autoRenderMinicartButton }}><label for="simplerwc_auto_render_minicart_button">Render quick checkout in minicart widget</label>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">Minicart button placement</th>
            <td><select name="simplerwc_minicart_button_placement">
                    <option value="woocommerce_widget_shopping_cart_before_buttons" {{ selected($minicartButtonPlacement, 'woocommerce_widget_shopping_cart_before_buttons') }}>
                        Before Buttons
                    </option>
                    <option value="woocommerce_widget_shopping_cart_after_buttons" {{ selected($minicartButtonPlacement, 'woocommerce_widget_shopping_cart_after_buttons') }}>
                        After Buttons
                    </option>
                </select>
            </td>
        </tr>
    </tbody>
</table>

<h2>General Settings</h2>
<hr class="divider">
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Show accepted cards below button</th>
            <td>
                <div><input type="checkbox" id="simplerwc_show_cards_notice" name="simplerwc_show_cards_notice" value="1" {{ $showCardsUnderButton }}><label for="simplerwc_show_cards_notice">Show accepted cards below button</label></div>
            </td>
        </tr>
        <tr>
            <th scope="row">Do not render the button for logged-in user with these roles</th>
            <td>
                <div>
                    <select name="simplerwc_excluded_user_roles[]" id="simplerwc_excluded_user_roles" multiple="true" style="min-width: 320px;">
                        @foreach ($excludedRoles as $role)
                        <option value="{{ $role['name'] }}" {{ $role['selected'] }}>{{ $role['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div><small>Exclude multiple roles by control-clicking</small></div>
            </td>
        </tr>
    </tbody>
</table>