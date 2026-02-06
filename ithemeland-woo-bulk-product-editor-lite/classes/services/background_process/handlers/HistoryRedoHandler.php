<?php

namespace wcbel\classes\services\background_process\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\History;
use wcbel\classes\services\product_update\Update_Service;

class HistoryRedoHandler implements HandlerInterface
{
    public function handle($item)
    {
        if (empty($item['product_data']) || empty($item['product_id'])) {
            return false;
        }

        $update_service = Update_Service::get_instance();
        $update_service->set_update_data([
            'update_type' => ($item['update_type'] == 'variation') ? 'variation' : 'product',
            'product_ids' => [intval($item['product_id'])],
            'product_data' => $item['product_data'],
            'save_history' => false,
        ]);
        return $update_service->perform();
    }

    public function complete($data)
    {
        if (empty($data['history_id'])) {
            return false;
        }

        if (isset($data['background_process_result']) && $data['background_process_result']) {
            $history_repository = History::get_instance();
            return $history_repository->update_history(intval($data['history_id']), ['reverted' => 0]);
        }
    }
}
