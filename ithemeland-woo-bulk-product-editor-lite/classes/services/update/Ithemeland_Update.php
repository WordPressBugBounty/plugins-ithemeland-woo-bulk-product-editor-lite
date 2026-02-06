<?php

namespace wcbel\classes\services\Update;

defined('ABSPATH') || exit();

class Ithemeland_Update
{
    public function get_plugins_data()
    {
        try {
            $data = [
                'service' => 'update_data',
                'product_id' => 'wcbe-pro'
            ];
            $response = wp_remote_post("https://license.ithemelandco.com/index.php", [
                'sslverify' => false,
                'method' => 'POST',
                'timeout' => 45,
                'httpversion' => '1.0',
                'body' => $data
            ]);

            return (!is_object($response) && !empty($response['body'])) ? json_decode($response['body'], true) : null;
        } catch (\Exception $e) {
            return false;
        }
    }
}
