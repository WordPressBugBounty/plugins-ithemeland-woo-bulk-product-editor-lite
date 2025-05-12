<?php

namespace wcbel\classes\services\background_process\handlers;

use wcbel\classes\services\background_process\ProductBackgroundProcess;

defined('ABSPATH') || exit(); // Exit if accessed directly

class ProductRestoreHandler implements HandlerInterface
{
    public function handle($item)
    {
        if (empty($item['product_ids'])) {
            return;
        }

        if (!empty($item['product_ids']) && is_array($item['product_ids'])) {
            foreach ($item['product_ids'] as $product_id) {
                wp_untrash_post(intval($product_id));
            }

            $background_process = ProductBackgroundProcess::get_instance();
            $background_process->add_completed_task(count($item['product_ids']));
        }
    }
}
