<?php

namespace wcbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Operator
{
    public static function edit_text($extra = [])
    {
        $operators =  [
            'text_new' => esc_html__('New', 'ithemeland-woo-bulk-product-editor-lite'),
            'text_append' => esc_html__('Append', 'ithemeland-woo-bulk-product-editor-lite'),
            'text_prepend' => esc_html__('Prepend', 'ithemeland-woo-bulk-product-editor-lite'),
            'text_delete' => esc_html__('Delete', 'ithemeland-woo-bulk-product-editor-lite'),
            'text_replace' => esc_html__('Replace', 'ithemeland-woo-bulk-product-editor-lite'),
        ];

        if (!empty($extra) && is_array($extra)) {
            foreach ($extra as $key => $label) {
                $operators[sanitize_text_field($key)] = sanitize_text_field($label);
            }
        }

        return $operators;
    }

    public static function edit_taxonomy()
    {
        return [
            'taxonomy_append' => esc_html__('Append', 'ithemeland-woo-bulk-product-editor-lite'),
            'taxonomy_replace' => esc_html__('Replace', 'ithemeland-woo-bulk-product-editor-lite'),
            'taxonomy_delete' => esc_html__('Delete', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function edit_number()
    {
        return [
            'number_new' => esc_html__('Set New', 'ithemeland-woo-bulk-product-editor-lite'),
            'number_clear' => esc_html__('Clear Value', 'ithemeland-woo-bulk-product-editor-lite'),
            'number_formula' => esc_html__('Formula', 'ithemeland-woo-bulk-product-editor-lite'),
            'increase_by_value' => esc_html__('Increase by value', 'ithemeland-woo-bulk-product-editor-lite'),
            'decrease_by_value' => esc_html__('Decrease by value', 'ithemeland-woo-bulk-product-editor-lite'),
            'increase_by_percent' => esc_html__('Increase by %', 'ithemeland-woo-bulk-product-editor-lite'),
            'decrease_by_percent' => esc_html__('Decrease by %', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function edit_regular_price()
    {
        return [
            'increase_by_value_from_sale' => esc_html__('Increase by value (From sale)', 'ithemeland-woo-bulk-product-editor-lite'),
            'increase_by_percent_from_sale' => esc_html__('Increase by % (From sale)', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function edit_sale_price()
    {
        return [
            'decrease_by_value_from_regular' => esc_html__('Decrease by value (From regular)', 'ithemeland-woo-bulk-product-editor-lite'),
            'decrease_by_percent_from_regular' => esc_html__('Decrease by % (From regular)', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }
    public static function filter_text()
    {
        return [
            'like' => esc_html__('Like', 'ithemeland-woo-bulk-product-editor-lite'),
            'exact' => esc_html__('Exact', 'ithemeland-woo-bulk-product-editor-lite'),
            'not' => esc_html__('Not', 'ithemeland-woo-bulk-product-editor-lite'),
            'begin' => esc_html__('Begin', 'ithemeland-woo-bulk-product-editor-lite'),
            'end' => esc_html__('End', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function filter_multi_select()
    {
        return [
            'or' => 'OR',
            'and' => 'And',
            'not_in' => 'Not IN',
        ];
    }

    public static function filter_type_select()
    {
        return [
            'like' => esc_html__('Like', 'ithemeland-woo-bulk-product-editor-lite'),
            'exact' => esc_html__('Exact', 'ithemeland-woo-bulk-product-editor-lite'),
            'not' => esc_html__('Not', 'ithemeland-woo-bulk-product-editor-lite'),
            'begin' => esc_html__('Begin', 'ithemeland-woo-bulk-product-editor-lite'),
            'end' => esc_html__('End', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }
    public static function round_items()
    {
        return [
            5 => 5,
            10 => 10,
            19 => 19,
            29 => 29,
            39 => 39,
            49 => 49,
            59 => 59,
            69 => 69,
            79 => 79,
            89 => 89,
            99 => 99
        ];
    }

    public static function get_current_value()
    {
        return [
            // '' => esc_html__('Current Value', 'ithemeland-woo-bulk-product-editor-lite'),
            'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
            'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }
}
