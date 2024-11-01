<h2>Shortcodes</h2>
<p style="font-size:1rem;">If you are an advanced user, you can use <a href="https://wordpress.com/support/shortcodes/">shortcodes</a>
    to control button placement in a more flexible way. Simpler provides two shortcodes :</p>
<ul style="padding: 32px 8px;">
    <li>
        <h5><code>[simpler-product-checkout]</code></h5>
        <p>
            This shortcode will render a checkout button that will open up Simpler Checkout for the product in the
            current context. If there is no product in the context (for example in a static page), you can use the
            product_id attribute to hardcode a product.
        </p>
        <p>
            If you are using a hardcoded product id for a variable product, you will need to supply the variation id.
            Feel free to reach out to <a href="mailto:support@simpler.so">support@simpler.so</a> for support.
        </p>
    </li>
    <li>
        <h5><code>[simpler-cart-checkout]</code></h5>
        <p>
            This shortcode will render a checkout button that will open up a Simpler Checkout for the <strong>current
                cart</strong>. It is useful for checkout pages, where customers may have added multiple products to
            their carts.
    </li>
</ul>
<p style="font-size:1rem;">
    If you decide to use shortcodes, we suggest you turn off the 'Auto-render' settings in the <a
            href="?page=simpler_management&tab=simpler_button">button settings tab</a>.
</p>