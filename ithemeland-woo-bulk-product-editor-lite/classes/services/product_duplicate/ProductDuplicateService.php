<?php

namespace wcbel\classes\services\product_duplicate;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\Product;
use wcbel\classes\services\background_process\ProductBackgroundProcess;

class ProductDuplicateService
{
    const MAX_PROCESS_COUNT = 2;
    const MAX_PROCESS_IDS = 10;

    private $is_processing;

    public function perform($data)
    {
        if (empty($data['product_ids']) || !is_array($data['product_ids']) || empty($data['count'])) {
            return false;
        }

        if ((count($data['product_ids']) > self::MAX_PROCESS_IDS || intval($data['count']) > self::MAX_PROCESS_COUNT) && ProductBackgroundProcess::is_enable()) {
            $this->push_to_queue($data);
        } else {
            $product_repository = Product::get_instance();
            foreach ($data['product_ids'] as $product_id) {
                $product = $product_repository->get_product(intval($product_id));
                if (!($product instanceof \WC_Product)) {
                    continue;
                }
                for ($i = 1; $i <= intval($data['count']); $i++) {
                    $new_product = new \WC_Admin_Duplicate_Product();
                    $new_product->product_duplicate($product);
                }
            }
        }
    }

    private function push_to_queue($data)
    {
        $background_process = ProductBackgroundProcess::get_instance();
        if ($background_process->is_not_queue_empty()) {
            return false;
        }

        foreach ($data['product_ids'] as $product_id) {
            if ($data['count'] > self::MAX_PROCESS_COUNT) {
                $round = ceil(intval($data['count']) / self::MAX_PROCESS_COUNT);
                for ($i = 1; $i <= intval($round); $i++) {
                    if ($i == $round) {
                        $count = intval($data['count']) - (($round - 1) * self::MAX_PROCESS_COUNT);  //phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
                    } else {
                        $count = self::MAX_PROCESS_COUNT;
                    }

                    $background_process->push_to_queue([
                        'handler' => 'product_duplicate',
                        'product_id' => intval($product_id),
                        'count' => intval($count),
                    ]);
                    $background_process->save();
                }
            } else {
                $background_process->push_to_queue([
                    'handler' => 'product_duplicate',
                    'product_id' => intval($product_id),
                    'count' => intval($data['count']),
                ]);
                $background_process->save();
            }
        }
        $background_process->set_total_tasks(count($data['product_ids']) * intval($data['count']));
        $background_process->start();
        $this->is_processing = true;
    }

    public function is_processing()
    {
        return $this->is_processing;
    }
}
