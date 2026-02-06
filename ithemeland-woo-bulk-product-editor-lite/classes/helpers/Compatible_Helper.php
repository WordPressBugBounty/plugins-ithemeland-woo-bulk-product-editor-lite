<?php

namespace wcbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Compatible_Helper
{
    public static function has_compatible_fields()
    {
        $compatible_fields_status = self::get_compatible_fields_status();
        return (in_array(true, $compatible_fields_status));
    }

    public static function get_compatible_tabs_label()
    {
        return [
            'min_max_quantities' => esc_html__('Min/Max quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'pricing' => esc_html__('Dynamic Price Role', 'ithemeland-woo-bulk-product-editor-lite'),
            'multi_vendor' => esc_html__('Multi vendor', 'ithemeland-woo-bulk-product-editor-lite'),
            'multi_currency' => esc_html__('Multi currency', 'ithemeland-woo-bulk-product-editor-lite'),
            'cost_of_goods' => esc_html__('Cost of goods', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function get_compatible_fields_status()
    {
        return [
            'min_max_quantities' => defined("WC_MIN_MAX_QUANTITIES") || defined("YWMMQ_INIT"),
            'pricing' => class_exists("it_WC_Dynamic_Pricing"),
            'multi_vendor' => defined("YITH_WPV_INIT") || class_exists("WC_Product_Vendors"),
            'multi_currency' => class_exists("WOOMULTI_CURRENCY_F"),
            'cost_of_goods' => defined("YITH_COG_INIT") || class_exists("WC_COG"),
        ];
    }

    public static function get_compatibles()
    {
        return [
            'min_max_quantities' => [
                'woocommerce' => [
                    'status' => defined("WC_MIN_MAX_QUANTITIES"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/min_max_quantities/woocommerce.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/min_max_quantities/woocommerce.php',
                ],
                'yith' => [
                    'status' => defined("YWMMQ_INIT"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/min_max_quantities/yith.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/min_max_quantities/yith.php',
                ]
            ],
            'pricing' => [
                'ithemeland' => [
                    'status' => class_exists("it_WC_Dynamic_Pricing"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/pricing/ithemeland.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/pricing/ithemeland.php',
                ]
            ],
            'multi_vendor' => [
                'woocommerce' => [
                    'status' => class_exists("WC_Product_Vendors"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/multi_vendor/woocommerce.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/multi_vendor/woocommerce.php',
                ],
                'yith' => [
                    'status' => defined("YITH_WPV_INIT"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/multi_vendor/yith.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/multi_vendor/yith.php',
                ]
            ],
            'multi_currency' => [
                'villa_theme' => [
                    'status' => class_exists("WOOMULTI_CURRENCY_F"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/multi_currency/villa_theme.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/multi_currency/villa_theme.php',
                ]
            ],
            'cost_of_goods' => [
                'woocommerce' => [
                    'status' => class_exists("WC_COG"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/cost_of_goods/woocommerce.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/cost_of_goods/woocommerce.php',
                ],
                'yith' => [
                    'status' => defined("YITH_COG_INIT"),
                    'edit_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles/cost_of_goods/yith.php',
                    'filter_fields' => WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles/cost_of_goods/yith.php',
                ],
            ],
        ];
    }
}
