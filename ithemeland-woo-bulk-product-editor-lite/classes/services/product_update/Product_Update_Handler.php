<?php

namespace wcbel\classes\services\product_update;

use wcbel\classes\repositories\History;
use wcbel\classes\services\background_process\ProductBackgroundProcess;

defined('ABSPATH') || exit(); // Exit if accessed directly

abstract class Product_Update_Handler
{
    abstract function update($product_ids, $update_data);

    protected function save_history_item($data)
    {
        $history_repository = History::get_instance();
        return $history_repository->save_history_item($data);
    }

    protected function add_completed_task($number)
    {
        $background_process = ProductBackgroundProcess::get_instance();
        $background_process->add_completed_task(intval($number));
    }
}
