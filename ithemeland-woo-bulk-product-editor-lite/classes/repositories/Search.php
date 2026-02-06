<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\search\Search_Main;

class Search extends Search_Main
{
    private static $instance;

    protected $filter_profile_option_name = "wcbe_filter_profile";
    protected $use_always_table = "wcbe_filter_profile_use_always";
    protected $current_data_option_name = "wcbe_filter_profile_current_data";
    protected $option_values_option_name = "wcbe_filter_option_values";

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set_default_item()
    {
        $default_item['default'] = [
            'name' => esc_html__('All Products', 'ithemeland-woo-bulk-product-editor-lite'),
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'default',
            'filter_data' => []
        ];
        $this->update_use_always('default');
        return update_option($this->filter_profile_option_name, $default_item);
    }

    public function get_option_values()
    {
        return get_option($this->option_values_option_name, []);
    }

    public function update_option_values($data)
    {
        return update_option($this->option_values_option_name, Sanitizer::array($data));
    }
}
