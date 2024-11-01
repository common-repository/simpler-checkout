<?php

namespace Simpler\Admin\Http\Controllers;

class HomeController extends Controller
{

    public function home()
    {
        add_submenu_page(
            'options-general.php',
            'SimplerCheckout',
            'Simpler Checkout',
            'manage_options',
            'simpler_management',
            [$this, 'show'],
            100,
            100
        );
    }

    public function show()
    {
        $this->render('_home', [
            'tab'     => $_GET['tab'] ?? 'simpler_management',
            'formUrl' => esc_url(admin_url('options.php')),
        ]);
    }
}
