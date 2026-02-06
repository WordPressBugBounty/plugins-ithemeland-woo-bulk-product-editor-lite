<?php

namespace wcbel\classes\services\product_update;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\History;
use wcbel\classes\services\background_process\ProductBackgroundProcess;
use wcbel\classes\services\product_update\handlers\Meta_Field_Handler;
use wcbel\classes\services\product_update\handlers\Product_Action_Handler;
use wcbel\classes\services\product_update\handlers\Remove_Duplicate_Handler;
use wcbel\classes\services\product_update\handlers\Taxonomy_Handler;
use wcbel\classes\services\product_update\handlers\Woocommerce_Handler;
use wcbel\classes\services\product_update\handlers\WP_Posts_Handler;

class Product_Update implements Update_Interface
{
    private static $instance;
    private $product_ids;
    private $product_data;
    private $update_classes;
    private $save_history;
    private $operation_type;
    private $is_processing;
    private $max_process_count;
    private $history_id;
    private $complete_actions;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->update_classes = $this->get_update_classes();
        $this->max_process_count = 100;
    }

    public function is_processing()
    {
        return $this->is_processing;
    }

    public function get_history_id()
    {
        return $this->history_id;
    }

    public function set_update_data($data)
    {
        if (!isset($data['product_ids']) || empty($data['product_data']) || !is_array($data['product_data'])) {
            return false;
        }

        $this->product_ids = array_unique($data['product_ids']);
        $this->product_data = $data['product_data'];
        $this->complete_actions = (!empty($data['complete_actions'])) ? $data['complete_actions'] : null;
        $this->save_history = (!empty($data['save_history']));
        if (isset($data['operation_type'])) {
            $this->operation_type = sanitize_text_field($data['operation_type']);
        } else {
            $this->operation_type = (count($this->product_ids) > 1) ? History::BULK_OPERATION : History::INLINE_OPERATION;
        }
    }

    public function perform()
    {
        // save history
        if ($this->save_history) {
            $this->history_id = $this->save_history();
            if (empty($this->history_id)) {
                return false;
            }
        }

        $total_count = count($this->product_ids) * count($this->product_data);

        if ($total_count > $this->max_process_count && ProductBackgroundProcess::is_enable()) {
            $background_process = ProductBackgroundProcess::get_instance();

            if ($background_process->is_not_queue_empty()) {
                if (!empty($this->history_id)) {
                    $this->delete_history($this->history_id);
                }
                return false;
            }
        }

        foreach ($this->product_data as $update_item) {
            $update_result = false;

            if (!empty($this->history_id)) {
                $update_item['history_id'] = intval($this->history_id);
            }

            // check items
            if (!$this->is_valid_update_item($update_item)) {
                continue;
            }

            $class = $this->update_classes[$update_item['type']];
            if ($total_count > $this->max_process_count && ProductBackgroundProcess::is_enable()) {
                foreach ($this->product_ids as $product_id) {
                    $background_process->push_to_queue([
                        'handler' => 'product_update',
                        'update_class' => $class,
                        'product_id' => $product_id,
                        'update_item' => $update_item,
                    ]);
                    $background_process->save();
                }
                $this->is_processing = true;
            } else {
                $instance = new $class();
                $update_result = $instance->update($this->product_ids, $update_item);
            }
        }

        if ($this->is_processing === true) {
            $background_process->set_total_tasks($total_count);
            if (!empty($this->complete_actions)) {
                foreach ($this->complete_actions as $action) {
                    $background_process->add_complete_action($action);
                }
            }
            $background_process->start();
        } else {
            if (!empty($this->complete_actions)) {
                foreach ($this->complete_actions as $action) {
                    if (!empty($action['hook'])) {
                        if (!empty($action['data'])) {
                            $action['data']['result'] = $update_result;
                            do_action($action['hook'], $action['data']);
                        } else {
                            do_action($action['hook']);
                        }
                    }
                }
            }
        }

        return true;
    }

    private function is_valid_update_item($update_item)
    {
        // has require item ?
        if (
            empty($update_item['name'])
            || empty($update_item['type'])
            || (empty($update_item['value'])
                && (!empty($update_item['operator'])
                    && !in_array($update_item['operator'], ['text_remove_duplicate', 'text_replace', 'number_clear', 'text_clear'])
                    && $update_item['operation'] != 'inline_edit'
                    && empty($update_item['used_for_variations'])
                    && empty($update_item['attribute_is_visible'])))
        ) {
            return false;
        }

        // has update method ?
        if (!isset($this->update_classes[$update_item['type']]) || !class_exists($this->update_classes[$update_item['type']])) {
            return false;
        }

        return true;
    }

    private function get_update_classes()
    {
        return apply_filters('wcbel_product_update_handlers', [
            'woocommerce_field' => Woocommerce_Handler::class,
            'wp_posts_field' => WP_Posts_Handler::class,
            'meta_field' => Meta_Field_Handler::class,
            'taxonomy' => Taxonomy_Handler::class,
            'product_action' => Product_Action_Handler::class,
            'remove_duplicate' => Remove_Duplicate_Handler::class,
        ]);
    }

    private function save_history()
    {
        $history_repository = History::get_instance();
        $fields = array_column($this->product_data, 'name');

        $history_id = $history_repository->create_history([
            'user_id' => intval(get_current_user_id()),
            'fields' => serialize($fields),
            'operation_type' => $this->operation_type,
            'operation_date' => gmdate('Y-m-d H:i:s'),
        ]);

        return $history_id;
    }

    private function delete_history($history_id)
    {
        $history_repository = History::get_instance();
        return $history_repository->delete_history(intval($history_id));
    }
}
