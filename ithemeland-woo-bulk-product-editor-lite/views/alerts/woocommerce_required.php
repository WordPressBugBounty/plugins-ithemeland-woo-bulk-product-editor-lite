<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

function wcbe_woocommerce_required_error()
{
    $class = 'notice notice-error';
    $message = esc_html__('"iThemeland WooCommerce Bulk Product Editing" Plugin needs "WooCommerce" Plugin, Please Install/Activate that.', 'ithemeland-woo-bulk-product-editor-lite');
    echo wp_kses(sprintf('<div class="%1$s"><p>%2$s</p></div>', $class, $message), Sanitizer::allowed_html());
}

add_action('admin_notices', 'wcbe_woocommerce_required_error');
