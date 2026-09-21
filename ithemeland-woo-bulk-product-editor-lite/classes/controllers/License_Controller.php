<?php

namespace wcbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class License_Controller
{
    public function index()
    {
        include WCBEL_VIEWS_DIR . 'license/main.php';
    }
}
