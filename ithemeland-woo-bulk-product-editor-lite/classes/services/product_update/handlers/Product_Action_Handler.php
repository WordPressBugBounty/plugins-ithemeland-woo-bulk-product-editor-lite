<?php

namespace wcbel\classes\services\product_update\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\services\product_update\Product_Update_Handler;

class Product_Action_Handler extends Product_Update_Handler
{
    private $product_id;

    public function update($product_ids, $update_data)
    {
        $methods = $this->get_methods();
        $method = (!empty($methods[$update_data['value']])) ? $methods[$update_data['value']] : '';
        if (empty($method) || !method_exists($this, $method)) {
            return false;
        }

        foreach ($product_ids as $product_id) {
            $this->product_id = intval($product_id);
            $this->{$method}();
            if (isset($update_data['background_process']) && $update_data['background_process'] === true) {
                $this->add_completed_task(1);
            }
        }

        return true;
    }

    private function get_methods()
    {
        return [
            'trash' => 'delete_product',
            'untrash' => 'restore_product'
        ];
    }

    private function delete_product()
    {
        return wp_trash_post($this->product_id);
    }

    private function restore_product()
    {
        return wp_untrash_post($this->product_id);
    }
}
