<?php

namespace wcbel\classes\services\background_process\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\services\product_delete\ProductDeleteHandler as ProductDeleteServiceHandler;

class ProductDeleteHandler implements HandlerInterface
{
    public function handle($item)
    {
        if (empty($item['product_ids']) || empty($item['delete_type'])) {
            return;
        }

        $product_delete = new ProductDeleteServiceHandler();
        $product_delete->handle([
            'product_ids' => $item['product_ids'],
            'delete_type' => $item['delete_type'],
            'background_process' => true,
            'history_id' => isset($item['history_id']) ? $item['history_id'] : 0,
        ]);
    }
}
