<?php

namespace wcbel\classes\services\product_delete;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\History;
use wcbel\classes\services\background_process\ProductBackgroundProcess;

class ProductDeleteHandler
{
    public function handle($data)
    {
        if (empty($data['product_ids']) || !is_array($data['product_ids']) || empty($data['delete_type'])) {
            return false;
        }

        $trashed = [];
        if (count($data['product_ids']) > ProductDeleteService::MAX_PROCESS_COUNT && ProductBackgroundProcess::is_enable()) {
            $this->push_to_queue($data['product_ids'], $data['delete_type'], $data['history_id']);
        } else {
            foreach ($data['product_ids'] as $product_id) {
                if ($data['delete_type'] == 'permanently') {
                    wp_delete_post(intval($product_id), true);
                } else {
                    $trashed[] = intval($product_id);
                    wp_trash_post(intval($product_id));
                }
                if (isset($data['background_process']) && $data['background_process'] === true) {
                    $background_process = ProductBackgroundProcess::get_instance();
                    $background_process->add_completed_task(1);
                }
            }
        }

        if (!empty($trashed) && !empty($data['history_id'])) {
            $this->save_history_items($data['history_id'], $trashed);
        }
    }

    private function push_to_queue($product_ids, $delete_type, $history_id)
    {
        $background_process = ProductBackgroundProcess::get_instance();
        if ($background_process->is_not_queue_empty()) {
            return false;
        }

        $ids = array_chunk($product_ids, ProductDeleteService::MAX_PROCESS_COUNT);
        foreach ($ids as $items) {
            if (empty($items) || !is_array($items)) {
                continue;
            }
            $background_process->push_to_queue([
                'handler' => 'product_delete',
                'product_ids' => array_map('intval', $items),
                'delete_type' => $delete_type,
                'history_id' => $history_id
            ]);
            $background_process->save();
        }
        $background_process->set_total_tasks(count($product_ids));
        $background_process->start();
    }

    private function save_history_items($history_id, $product_ids)
    {
        if (empty($history_id) || empty($product_ids) || !is_array($product_ids)) {
            return false;
        }

        $history_repository = History::get_instance();
        foreach ($product_ids as $product_id) {
            $history_repository->save_history_item([
                'history_id' => intval($history_id),
                'historiable_id' => intval($product_id),
                'name' => 'product_delete',
                'type' => 'product_action',
                'prev_value' => 'untrash',
                'new_value' => 'trash',
                'prev_total_count' => 1,
                'new_total_count' => 1,
            ]);
        }

        return true;
    }
}
