<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\setting\Setting_Main;

class Setting extends Setting_Main
{
    private static $instance;

    private $settings;

    const MAX_COUNT_PER_PAGE = 50;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->settings_option_name = "wcbe_settings";
        $this->current_settings_option_name = "wcbe_current_settings";
    }

    public function update($data = [])
    {
        $this->settings = $this->get_settings();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->settings[sanitize_text_field($key)] = sanitize_text_field($value);
            }
        }

        update_option($this->settings_option_name, $this->settings);
        return $this->get_settings();
    }

    public function get_settings()
    {
        if (empty($this->settings)) {
            $this->set_settings();
        }

        return $this->settings;
    }

    private function set_settings()
    {
        $this->settings = get_option($this->settings_option_name, []);

        if (empty($this->settings)) {
            $this->settings = $this->set_default_settings();
        } else {
            $required_fields = $this->get_required_fields();
            if (count($required_fields) > count($this->settings)) {
                foreach ($required_fields as $setting => $value) {
                    if (!isset($this->settings[$setting])) {
                        $this->settings[$setting] = $value;
                    }
                }
            }
        }
    }

    public function set_default_settings()
    {
        update_option($this->settings_option_name, $this->get_required_fields());
        return $this->get_required_fields();
    }

    public function set_current_settings($settings)
    {
        $this->update_current_settings([
            'count_per_page' => ($settings['count_per_page'] > self::MAX_COUNT_PER_PAGE) ? self::MAX_COUNT_PER_PAGE : intval($settings['count_per_page']),
        ]);
    }

    private function get_required_fields()
    {
        return [
            'count_per_page' => 10,
            'default_sort_by' => 'ID',
            'default_sort' => "DESC",
            'close_popup_after_applying' => 'no',
            'sticky_first_columns' => 'yes',
            'display_full_columns_title' => 'yes',
            'enable_thumbnail_popup' => 'yes',
            'keep_filled_data_in_bulk_edit_form' => 'no',
            'show_only_filtered_variations' => 'no',
            'fetch_data_in_bulk' => 'no',
            'enable_background_processing' => 'yes',
            'display_cell_content' => 'long',
            'enable_load_more_variations' => 'yes',
        ];
    }

    public function get_count_per_page_items()
    {
        return [
            '10',
            '25',
            '50',
        ];
    }
}
