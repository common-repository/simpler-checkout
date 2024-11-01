
<div class="wrap">
    <h1 style="font-weight: 600; margin: 2rem 0;">Simpler. 1-Click Checkout</h1>
    <p>
        The Simpler 1-Click Checkout button lets your customers complete their purchases in seconds.
        Customers using Simpler for the first time will fill in a simple form once.
        For all their next purchases, they can complete their orders with 1 click, regardless of device or browser, and without a password.
    </p>
    <p>
        You can configure your Simpler integration here.
        <span style="font-size:.8rem;">Questions? Contact us at <a href="mailto:support@simpler.so">support@simpler.so</a></span>
    </p>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab {{ $tab == 'simpler_management' ? 'nav-tab-active' : '' }}" href="?page=simpler_management&tab=simpler_management">Basic Configuration</a>
        <a class="nav-tab {{ $tab == 'simpler_button' ? 'nav-tab-active' : '' }}" href="?page=simpler_management&tab=simpler_button">Button</a>
        <a class="nav-tab {{ $tab == 'simpler_styling' ? 'nav-tab-active' : '' }}" href="?page=simpler_management&tab=simpler_styling">Advanced Styling</a>
        <a class="nav-tab {{ $tab == 'simpler_shortcodes' ? 'nav-tab-active' : '' }}" href="?page=simpler_management&tab=simpler_shortcodes">Shortcodes</a>
    </h2>
    <form action="{{ $formUrl }}" method="POST" style="padding-top:20px;">
        {!! wp_nonce_field('update-options'); !!}
        @php
            settings_fields($tab);
            do_settings_sections($tab);
            submit_button();
        @endphp
    </form>
</div>
