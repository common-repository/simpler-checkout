=== Simpler Checkout ===
Contributors: simplercheckout
Tags: simpler, login, checkout, authentication, woocommerce, woocommerce payment, woocommerce checkout, one click checkout, 1click checkout, simpler checkout
Requires at least: 5.1
Tested up to: 6.5
Requires PHP: 7.0
Stable tag: 1.0.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Let your customers checkout in seconds. The simplest way to increase your sales.

== Description ==

The Simpler Checkout button lets your customers complete their purchases in seconds. Customers using Simpler for the first time will fill in a simple form once. For all the next purchases, they can complete their orders with one click, regardless of device or browser, and without a password.

Simpler checkout is designed based on conversion best practices, reducing friction and increasing sales.

== Installation ==

You can find a complete installation guide in our [developer documentation page](https://simpler.readme.io/).

== Frequently Asked Questions ==

= How much does Simpler cost? =
Simpler integrates with WooCommerce and works in addition with your current payment gateway. You can find our pricing information at [pricing page](https://www.simpler.so/pricing)

= Do you share the shopper's email address with the merchant?
When the shopper completes a checkout, all contact & shipping info is shared with the merchant.

= I need help installing Simpler Checkout to my website =
Please email us at integrations@simpler.so and we'll guide you through the process as soon as possible.

== Screenshots ==

== Changelog ==

== 1.0.3
Feat: handle bundle discounted products

== 1.0.2
Feat: `simplerwc_customer_properties` filter
Compat: [WC Pickup Store](https://wordpress.com/plugins/wc-pickup-store)

== 1.0.1
Hotfix: default to production environment when no explicit setting

== 1.0.0
Feat: new `simplerwc_order_created` action on successful submission
Feat: run `woocommerce_checkout_order_created` action on successful submission
Feat: remove sandbox option as Simpler production environment now supports Test Stores

== 0.7.11
Compat: Wordpress 6.5

== 0.7.10
Feat: Experimental support for [WooCommerce Order Attribution Tracking](https://woo.com/document/order-attribution-tracking/)

== 0.7.9
Compat: [Pay for Payment for WooCommerce](https://wordpress.org/plugins/woocommerce-pay-for-payment/)

== 0.7.8
Fix: omit coupon from button payload if empty

== 0.7.7
Feat: introduce `simplerwc_should_render_product_button` and `simplerwc_should_render_cart_button` filters for granular control of rendering logic
Feat: introduce `simplerwc_button_get_product_attibutes` and `simplerwc_get_cart_item_data` filters for managing cart item attributes
Compat: [iThemeland Free Gifts](https://ithemelandco.com/plugins/free-gifts-for-woocommerce/)

== 0.7.6
Fix: COD filters invocation

== 0.7.5
Feat: Parse company invoicing details in order request
Fix: do not set payment method title when payment method is not simpler

== 0.7.4
Feat: Support COD payment method
Compat: Smart COD plugin support

== 0.7.3
Compat: BoxNow v2 plugin support

== 0.7.2
Feat: Allow switching to sandbox environment from single distributable

== 0.7.1
Fix: Correct auth cookie argument in login before order confirmation redirect

== 0.7.0
Feat: Login user before redirecting to order confirmation

== 0.6.2
Feat: Add Minicart placement setting
Fix: remove excessive free gifts returned in products response when FGF plugin is active

== 0.6.1
Compat: [BoxNow](https://boxnow.gr/) support

== 0.6.0
Compat: [WooCommerce Local Pickup Plus](https://woocommerce.com/products/local-pickup-plus) support
Fix: Apply coupons before collecting shipping rates to account for free shipping coupons
Fix: Set chosen shipping method on retrieved package keys instead of defaulting to 0

== 0.5.8
Fix: WooCommerce Product Bundles correct quantity payload when bundle is in cart

== 0.5.7
Fix: Free Gifts for Woocommerce support for cart checkout with gift

== 0.5.6
* [Free Gifts for Woocommerce](https://woocommerce.com/products/free-gifts-for-woocommerce/) support

== 0.5.5
* Support Product Bundles created with WooCommerce Product Bundles plugin

== 0.5.4
* Calculate discounted tax when simpler discount present
* Fix : check data validity when invoking simplerwc_should_render_button function

== 0.5.3
* Optionally include customer email during quotation to handle coupon usage limits
* Fix : Refactor submission flow to ensure shipping tax calculation works as intended

== 0.5.2
* Fix: Cost reporting in order confirmation email
* Remove deprecated order submission functionality
* Remove deprecated Offers tab from Settings

== 0.5.1
* Hotfix: Include customer name & phone in order shipping address

== 0.5.0
* Support custom fees during cart fees calculation
* Breakdown products cost during quotation
* Excluded specific user roles from viewing the button
* Fix: Price rounding
* Fix: Include tax in coupons

== 0.4.0
* Breaking : Drop support for legacy SDK
* Add configuration option to hide the product page button if cart contains at least one item
* Include amount in refund request to account for partial refunds
* Introduce programmatic filter to modify shipping rates during quotation
* Fix : Include tax amount in shipping costs during quotation
* Fix : Respect prices when input excluding tax

== 0.3.3
* Introduce products route to speed up product details retrieval
* Introduce /v2/order route to include cart calculation hooks in order submission flow

== 0.3.2
* Fix critical issue preventing the plugin from running on PHP 7.2
* Render translated asset texts based on store locale

== 0.3.1
* Add product attributes to simpler integration for variable products.

== 0.3.0

* Extract browser assets to external file, hosted at https://cdn.simpler.so. This behavior can be toggled off by the "use legacy SDK" checkbox in the plugin settings, but is not encouraged.
* Use a Web Component to render the simpler-checkout button
* Add separate option to control if checkout button gets rendered in the cart view
* Accepted cards notice is now controlled by a single option for all positions to enhance consistency
