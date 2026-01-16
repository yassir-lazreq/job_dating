<?php

namespace App\Core;

abstract class BaseController
{
    /**
     * Render a view file with optional data.
     * 
     * @param string $view The view file name (without .php extension).
     * @param array $data Optional associative array of data to pass to the view.
     */
    protected function render(string $view, array $data = []): void
    {
        // Extract data array to variables
        extract($data);

        // Include the view file
        require_once(dirname(__DIR__, 1) . '/views/' . $view . '.php');
    }
}