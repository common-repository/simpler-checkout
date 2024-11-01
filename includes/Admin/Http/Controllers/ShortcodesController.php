<?php

namespace Simpler\Admin\Http\Controllers;

class ShortcodesController extends Controller
{
    public function shortcodes()
    {
        $this->render('settings._shortcodes');
    }
}
