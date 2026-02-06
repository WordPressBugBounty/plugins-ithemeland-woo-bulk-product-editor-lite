<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit();

use wcbel\classes\helpers\Operator;

class FilterFormItems
{
    public static function general_tab()
    {
        return [
            'product_ids' => [
                'name' => 'product_ids',
                'id' => 'ids',
                'label' => esc_html__('Product ID(s)', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_ids',
                'field_type' => 'text',
                'operators' => [
                    'exact' => esc_html__('Exact', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'placeholder' => esc_html__('for example: 1,2,3 or 1-10 or 1,2,3|10-20', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => false,
            ],
            'product_title' => [
                'name' => 'product_title',
                'id' => 'title',
                'label' => esc_html__('Product title', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_title',
                'field_type' => 'text',
                'operators' => Operator::filter_text(),
                'placeholder' => esc_html__('Product title', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => false,
            ],
            'product_content' => [
                'name' => 'product_content',
                'id' => 'content',
                'label' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_content',
                'field_type' => 'text',
                'operators' => Operator::filter_text(),
                'placeholder' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => false,
            ],
            'product_excerpt' => [
                'name' => 'product_excerpt',
                'id' => 'excerpt',
                'label' => esc_html__('Short description', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_excerpt',
                'field_type' => 'textarea',
                'operators' => Operator::filter_text(),
                'placeholder' => esc_html__('Short description', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => true,
            ],
            'product_slug' => [
                'name' => 'product_slug',
                'id' => 'slug',
                'label' => esc_html__('Product slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_slug',
                'field_type' => 'text',
                'operators' => Operator::filter_text(),
                'placeholder' => esc_html__('Product slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => true,
            ],
            'product_sku' => [
                'name' => 'product_sku',
                'id' => 'sku',
                'label' => esc_html__('Product SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_sku',
                'field_type' => 'text',
                'operators' => Operator::filter_text(),
                'placeholder' => esc_html__('Product SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => true,
            ],
            'product_url' => [
                'name' => 'product_url',
                'id' => 'url',
                'label' => esc_html__('Product url', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_url',
                'field_type' => 'text',
                'operators' => Operator::filter_text(),
                'placeholder' => esc_html__('Product url', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => true,
            ],
            'date_created' => [
                'name' => 'date_created',
                'id' => 'date-created',
                'label' => esc_html__('Product date', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'date_created',
                'field_type' => 'from_to_date',
                'operators' => [], // No operators for date range
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => true,
            ],
        ];
    }

    public static function taxonomies_tab()
    {
        return [
            'taxonomy_section' => [
                'name' => 'taxonomy_section',
                'label' => esc_html__('Taxonomy', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => '',
                'field_type' => '',
                'variable' => false,
                'disabled' => true,
            ],
            'attribute_section' => [
                'name' => 'attribute_section',
                'label' => esc_html__('Attribute', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => '',
                'field_type' => '',
                'variable' => false,
                'disabled' => true,
            ],
        ];
    }

    public static function pricing_tab()
    {
        return [
            'regular_price' => [
                'name' => 'regular-price',
                'id' => 'wcbe-filter-form-product-regular-price',
                'label' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'regular_price',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => false,
            ],
            'sale_price' => [
                'name' => 'sale-price',
                'id' => 'wcbe-filter-form-product-sale-price',
                'label' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'sale_price',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => false,
            ],
            'date_on_sale_from' => [
                'name' => 'date-on-sale-from',
                'id' => 'sale-price-date',
                'class' => 'wcbe-input-md wcbe-datepicker wcbe-date-from',
                'label' => esc_html__('Sale date from', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => '',
                'field_type' => 'date',
                'placeholder' => esc_html__('Sale date from', 'ithemeland-woo-bulk-product-editor-lite'),
                'date_range' => true,
                'variable' => false,
                'disabled' => true,
            ],
            'date_on_sale_to' => [
                'name' => 'date-on-sale-to',
                'id' => 'sale-price-date',
                'class' => 'wcbe-input-md wcbe-datepicker',
                'label' => esc_html__('Sale date to', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => '',
                'field_type' => 'date',
                'placeholder' => esc_html__('Sale date to', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false,
                'disabled' => true,
            ],
        ];
    }

    public static function shipping_tab()
    {
        return [
            'shipping_class' => [
                'name' => 'shipping_class',
                'id' => 'shipping-class',
                'label' => esc_html__('Shipping Class', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'shipping_class',
                'field_type' => 'select',
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'width' => [
                'name' => 'width',
                'id' => 'wcbe-filter-form-product-width',
                'label' => esc_html__('Width', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'width',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'height' => [
                'name' => 'height',
                'id' => 'wcbe-filter-form-product-height',
                'label' => esc_html__('Height', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'height',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'length' => [
                'name' => 'length',
                'id' => 'wcbe-filter-form-product-length',
                'label' => esc_html__('Length', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'length',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'weight' => [
                'name' => 'weight',
                'id' => 'wcbe-filter-form-product-width',
                'label' => esc_html__('Weight', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'weight',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite')
            ]
        ];
    }
    
    public static function stock_tab()
    {
        return [
            'manage_stock' => [
                'name' => 'manage_stock',
                'id' => 'manage-stock',
                'label' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'manage_stock',
                'field_type' => 'select',
                'options' => Operator::get_current_value(),
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'stock_quantity' => [
                'name' => 'stock_quantity',
                'id' => 'wcbe-filter-form-stock-quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'stock_quantity',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'stock_status' => [
                'name' => 'stock_status',
                'id' => 'stock-status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'stock_status',
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'backorders' => [
                'name' => 'backorders',
                'id' => 'backorders',
                'label' => esc_html__('Allow backorders', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'backorders',
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ]
        ];
    }

    public static function type_tab()
    {
        return [
            'product_type' => [
                'name' => 'product_type',
                'id' => 'type',
                'label' => esc_html__('Product type', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_type',
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'product_status' => [
                'name' => 'status',
                'id' => 'status',
                'label' => esc_html__('Product status', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'product_status',
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'featured' => [
                'name' => 'featured',
                'id' => 'featured',
                'label' => esc_html__('Featured', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'featured',
                'field_type' => 'select',
                'options' => [
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'downloadable' => [
                'name' => 'downloadable',
                'id' => 'downloadable',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'downloadable',
                'field_type' => 'select',
                'options' => Operator::get_current_value(),
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'sold_individually' => [
                'name' => 'sold_individually',
                'id' => 'downloadable',
                'label' => esc_html__('Sold individually', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'sold_individually',
                'field_type' => 'select',
                'options' => [
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'author' => [
                'name' => 'author',
                'id' => 'author',
                'label' => esc_html__('By author', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'author',
                'field_type' => 'user_select',
                'select2' => true
            ],
            'visibility' => [
                'name' => 'visibility',
                'id' => 'visibility',
                'label' => esc_html__('Catalog visibility', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'catalog_visibility',
                'field_type' => 'select',
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'menu_order' => [
                'name' => 'menu-order',
                'id' => 'wcbe-filter-form-product-menu-order',
                'label' => esc_html__('Menu order', 'ithemeland-woo-bulk-product-editor-lite'),
                'filter_type' => 'menu_order',
                'field_type' => 'from_to_number',
                'placeholder_from' => esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder_to' => esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ]
        ];
    }
}
