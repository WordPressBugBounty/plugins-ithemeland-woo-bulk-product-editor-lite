<?php

namespace wcbel\classes\services\background_process\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\Product;
use wcbel\classes\services\background_process\ProductBackgroundProcess;
use WC_Admin_Duplicate_Product;

class ProductDuplicateHandler implements HandlerInterface
{
    public function handle($item)
    {
        if (empty($item['count']) || empty($item['product_id'])) {
            return false;
        }

        if (!class_exists('WC_Admin_Duplicate_Product')) {
            return false;
        }

        $product_repository = Product::get_instance();
        $product = $product_repository->get_product(intval($item['product_id']));
        if (!($product instanceof \WC_Product) || $product instanceof \WC_Product_Variation) {
            return false;
        }

        for ($i = 1; $i <= intval($item['count']); $i++) {
            $new_product = new WC_Admin_Duplicate_Product();
            $new_product->product_duplicate($product);
        }

        $background_process = ProductBackgroundProcess::get_instance();
        $background_process->add_completed_task(intval($item['count']));
    }
}
