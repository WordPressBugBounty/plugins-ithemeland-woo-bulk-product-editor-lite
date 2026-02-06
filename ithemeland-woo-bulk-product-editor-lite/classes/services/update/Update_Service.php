<?php

namespace wcbel\classes\services\update;

defined('ABSPATH') || exit();

use wcbel\classes\repositories\update_data\Update_Data;

class Update_Service
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
        $plugins_data = $this->get_plugins_data();
        $available_updates = [];
        if (!empty($plugins_data) && is_array($plugins_data)) {
            foreach ($plugins_data as $plugin_data) {
                if (isset($plugin_data['key'])) {
                    $current_version = $this->get_plugin_version($plugin_data['key']);
                    if (!empty($current_version) && $this->has_update($plugin_data, $current_version)) {
                        $available_updates[$plugin_data['key']] = $plugin_data;
                    }
                }
            }

            $this->set_log();
            $this->save($available_updates);
        }
    }

    private function has_update($plugin_data, $current_version)
    {
        $new_version = (isset($plugin_data['new_version'])) ? $plugin_data['new_version'] : '';
        return ((version_compare($current_version, $new_version) === -1));
    }

    private function set_log()
    {
        update_option('wcbe_pro_last_check_for_update', gmdate('Y-m-d H:i:s', time()));
    }

    private function get_plugins_data()
    {
        return (new Ithemeland_Update())->get_plugins_data();
    }

    private function save($plugins_data)
    {
        try {
            return (new Update_Data())->update($plugins_data);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function get_plugin_version($plugin_key)
    {
        $version = get_option($plugin_key . '-version');
        return ($version) ? $version  : null;
    }
}
