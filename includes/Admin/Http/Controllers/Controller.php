<?php

namespace Simpler\Admin\Http\Controllers;

use Simpler\Services\BladeService;

abstract class Controller
{

    /**
     * The blade template engine.
     *
     * @var BladeService
     */
    protected $blade;


    public function __construct(BladeService $blade)
    {
        $this->blade = $blade;
    }

    /**
     * Run the blade engine. It returns the result of the code.
     *
     * @param  string  $view  The name of the view. Ex: "folder.folder.view" ("/folder/folder/view.blade")
     * @param  array   $data  An associative arrays with the values to display.
     *
     */
    protected function render(string $view, array $data = [])
    {
        try {
            echo $this->blade->run($view, $data);
        } catch (\Exception $e) {
        }
    }
}