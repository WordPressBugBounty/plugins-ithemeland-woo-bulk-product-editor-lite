<?php

namespace wcbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

class WCBEL_Bulk_Edit_Prepare
{
    private static $instance;

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    private function __construct()
    {
        $epbulkit = get_option('wcbel_epbulkit');
        if (!empty($epbulkit) && $epbulkit < time()) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 100);
        }
    }

    public function enqueue_scripts()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'wcbe') { //phpcs:ignore
            wp_enqueue_script('wcbel-epbulkit', WCBEL_JS_URL . 'epbulkit.js', ['jquery'], WCBEL_VERSION, true); //phpcs:ignore
        }
    }
}
