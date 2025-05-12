<?php

namespace wcbel\classes\services\product_delete;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\History;

class ProductDeleteService
{
    const MAX_PROCESS_COUNT = 50;

    private $is_processing;

    public function __construct()
    {
        $this->is_processing = false;
    }

    public function perform($product_ids, $delete_type)
    {
        if (empty($product_ids) || empty($delete_type)) {
            return false;
        }

        if (count($product_ids) > self::MAX_PROCESS_COUNT) {
            $this->is_processing = true;
        }

        $history_id = 0;
        if ($delete_type != 'permanently') {
            $history_id = $this->save_history();
        }

        $product_delete = new ProductDeleteHandler();
        return $product_delete->handle([
            'history_id' => $history_id,
            'product_ids' => $product_ids,
            'delete_type' => $delete_type
        ]);
    }

    private function save_history()
    {
        $history_repository = History::get_instance();
        return $history_repository->create_history([
            'user_id' => intval(get_current_user_id()),
            'fields' => serialize(['product_delete']),
            'operation_type' => History::BULK_OPERATION,
            'operation_date' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function is_processing()
    {
        return $this->is_processing;
    }
}
