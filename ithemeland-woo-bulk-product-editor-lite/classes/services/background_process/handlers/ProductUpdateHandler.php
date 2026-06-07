<?php

namespace wcbel\classes\services\background_process\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class ProductUpdateHandler implements HandlerInterface
{
    public function handle($item)
    {
        if (!isset($item['product_id']) || !isset($item['update_item']) || !isset($item['update_class'])) {
            return;
        }

        $item['update_item']['background_process'] = true;
        $instance = new $item['update_class']();
        return $instance->update([intval($item['product_id'])], $item['update_item']);
    }
}
