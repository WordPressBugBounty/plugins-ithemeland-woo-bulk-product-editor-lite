<?php

namespace wcbel\classes\services\deactivation;

defined('ABSPATH') || exit(); // Exit if accessed directly

class WCBEL_Deactivation_Service
{
    private static $instance;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function deactivate()
    {
        $el_data = get_option('wcbe-pro-el-data');
        if (empty($el_data)) {
            return false;
        }

        $response = wp_remote_post("https://license.ithemelandco.com/index.php", [
            'sslverify' => false,
            'method' => 'POST',
            'timeout' => 45,
            'httpversion' => '1.0',
            'body' => [
                'service' => 'license_deactivation',
                'license_data' => $el_data,
            ],
        ]);

        if (!is_object($response) && !empty($response['body'])) {
            $data = json_decode($response['body'], true);
            return (isset($data['success']) && $data['success'] === true);
        }

        return false;
    }
}
