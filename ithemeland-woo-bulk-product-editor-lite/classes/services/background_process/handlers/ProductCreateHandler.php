<?php

namespace wcbel\classes\services\background_process\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\Product;
use wcbel\classes\services\background_process\ProductBackgroundProcess;

class ProductCreateHandler implements HandlerInterface
{
    public function handle($item)
    {
        if (empty($item['count'])) {
            return;
        }

        $product_repository = Product::get_instance();
        for ($i = 1; $i <= intval($item['count']); $i++) {
            $product_repository->create($item['product_data']);
        }

        $background_process = ProductBackgroundProcess::get_instance();
        $background_process->add_completed_task(intval($item['count']));
    }
}
