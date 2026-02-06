<?php

namespace wcbel\classes\services\product_update\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\services\product_update\Product_Update_Handler;

class WP_Posts_Handler extends Product_Update_Handler
{
    private $post;
    private $update_data;
    private $current_field_value;

    public function update($product_ids, $update_data)
    {
        if (empty($product_ids) && !is_array($product_ids)) {
            return false;
        }

        $this->update_data = $update_data;

        foreach ($product_ids as $product_id) {
            if (!isset($this->update_data['value'])) {
                $this->update_data['value'] = '';
            }

            $post = get_post(intval($product_id));
            if (!($post instanceof \WP_Post)) {
                return false;
            }

            $this->post = $post;
            $this->current_field_value = (!empty($this->post->{$this->update_data['name']})) ? $this->post->{$this->update_data['name']} : '';

            $update_result = wp_update_post([
                'ID' => intval($product_id),
                sanitize_text_field($this->update_data['name']) => sanitize_text_field($this->update_data['value'])
            ]);

            if (!$update_result) {
                return false;
            }

            if (!empty($this->update_data['background_process'])) {
                $this->add_completed_task(1);
            }

            // save history item
            if (!empty($this->update_data['history_id'])) {
                $this->save_history_item([
                    'history_id' => $this->update_data['history_id'],
                    'historiable_id' => $this->post->ID,
                    'name' => $this->update_data['name'],
                    'sub_name' => (!empty($this->update_data['sub_name'])) ? $this->update_data['sub_name'] : '',
                    'type' => $this->update_data['type'],
                    'prev_value' => $this->current_field_value,
                    'new_value' => $this->update_data['value'],
                    'prev_total_count' => 1,
                    'new_total_count' => 1,
                ]);
            }
        }

        return true;
    }
}
