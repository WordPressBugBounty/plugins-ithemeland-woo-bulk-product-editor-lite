<?php
/*
Plugin Name: PBULKiT - Bulk Edit Products for WooCommerce
Plugin URI: https://ithemelandco.com/plugins/woocommerce-bulk-product-editing
Description: Editing Date in WordPress is very painful. Be professionals with managing data in the reliable and flexible way by WooCommerce Bulk Product Editor.
Author: iThemelandco
Tested up to: 7.1
Requires PHP: 8.0.3
Tags: woocommerce,woocommerce bulk edit,bulk edit,bulk,products bulk editor
Text Domain: ithemeland-woo-bulk-product-editor-lite
Domain Path: /languages
Requires Plugins: woocommerce
WC requires at least: 3.9
WC tested up to: 9.8.2
Requires at least: 5.3
Version: 4.1.2
License: GPLv3
Author URI: https://www.ithemelandco.com
*/

use wcbel\classes\bootstrap\WCBEL;
use wcbel\classes\bootstrap\WCBEL_Bulk_Edit_Prepare;
use wcbel\framework\renew_license_alert\RenewLicenseAlert;

defined('ABSPATH') || exit();

if (defined('WCBEL_NAME') || defined('WCBE_WOO_VERSION')) {
    return false;
}

require_once __DIR__ . '/vendor/autoload.php';

define('WCBEL_NAME', 'ithemeland-woo-bulk-product-editor-lite');
define('WCBEL_LABEL', 'PBULKiT - Bulk Edit Products for WooCommerce');
define('WCBEL_DESCRIPTION', 'Be professionals with managing data in the reliable and flexible way!');
define('WCBEL_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WCBEL_PLUGIN_MAIN_PAGE', admin_url('admin.php?page=wcbe'));
define('WCBEL_ADD_ONS_URL', admin_url('admin.php?page=wcbe-add-ons'));
define('WCBEL_ACTIVATION_PAGE', admin_url('admin.php?page=ithemeland-activation'));
define('WCBEL_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('WCBEL_LIB_DIR', trailingslashit(WCBEL_DIR . 'classes/lib'));
define('WCBEL_VIEWS_DIR', trailingslashit(WCBEL_DIR . 'views'));
define('WCBEL_LANGUAGES_DIR', dirname(plugin_basename(__FILE__)) . '/languages/');
define('WCBEL_ASSETS_DIR', trailingslashit(WCBEL_DIR . 'assets'));
define('WCBEL_ASSETS_URL', trailingslashit(WCBEL_URL . 'assets'));
define('WCBEL_FW_DIR', trailingslashit(WCBEL_DIR . 'framework'));
define('WCBEL_FW_URL', trailingslashit(WCBEL_URL . 'framework'));
define('WCBEL_CSS_URL', trailingslashit(WCBEL_ASSETS_URL . 'css'));
define('WCBEL_IMAGES_URL', trailingslashit(WCBEL_ASSETS_URL . 'images'));
define('WCBEL_JS_URL', trailingslashit(WCBEL_ASSETS_URL . 'js'));
define('WCBEL_VERSION', '4.1.2');
define('WCBEL_PRO_LINK', 'https://ithemelandco.com/plugins/woocommerce-bulk-product-editing?utm_source=free_plugins&amp;utm_medium=plugin_links&amp;utm_campaign=user-lite-buy#pricing');

register_activation_hook(__FILE__, ['wcbel\classes\bootstrap\WCBEL', 'activate']);
register_deactivation_hook(__FILE__, ['wcbel\classes\bootstrap\WCBEL', 'deactivate']);

add_action('init', ['wcbel\classes\bootstrap\WCBEL', 'wcbe_wp_init']);

// compatible with woocommerce custom order tables
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

add_action('plugins_loaded', function () {
    if (WCBEL::is_initable()) {
        WCBEL::init();
    } else {
        if (isset($_GET['page']) && $_GET['page'] == 'wcbe' && !defined('WCBE_NAME')) { //phpcs:ignore
            header("Location: " . admin_url('index.php'));
            die();
        }
    }
}, PHP_INT_MAX - 10);

add_action('plugins_loaded', function () {
    if (wcbel_pro_is_active() && !defined('WBEBL_NAME')) {
        $el_data = get_option('wcbe-pro-el-data');
        if (!empty($el_data) && !empty($el_data['expire_date'])) {
            if (time() > strtotime($el_data['expire_date'])) {
                RenewLicenseAlert::init();
            } else {
                RenewLicenseAlert::remove();
            }
        }

        WCBEL_Bulk_Edit_Prepare::init();
    }
}, PHP_INT_MAX);

if (!function_exists('wcbel_pro_is_active')) {
    function wcbel_pro_is_active()
    {
        $wcbel_active_plugins = apply_filters('active_plugins', get_option('active_plugins', array())); //phpcs:ignore
        if (is_multisite()) {
            $wcbel_active_plugins = array_merge($wcbel_active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array('ithemeland-woocommerce-bulk-product-editing-pro/ithemeland-woocommerce-bulk-product-editing-pro.php', $wcbel_active_plugins, false) || array_key_exists('ithemeland-woocommerce-bulk-product-editing-pro/ithemeland-woocommerce-bulk-product-editing-pro.php', $wcbel_active_plugins);
    }
}
