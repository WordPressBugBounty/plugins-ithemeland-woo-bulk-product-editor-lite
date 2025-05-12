<?php

namespace wcbel\classes\repositories;

use wcbel\classes\helpers\Operator;

defined('ABSPATH') || exit();


class EditFormItems
{
    public static function general_tab()
    {
        return [
            'title' => [
                'name' => 'title',
                'id' => 'title',
                'label' => esc_html__('Product title', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_text(),
                'field_type' => 'text',
                'placeholder' => esc_html__('Product title', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => true
            ],
            'slug' => [
                'name' => 'slug',
                'id' => 'slug',
                'label' => esc_html__('Product slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_text(),
                'field_type' => 'text',
                'placeholder' => esc_html__('Product slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => true
            ],
            'sku' => [
                'name' => 'sku',
                'id' => 'sku',
                'label' => esc_html__('Product SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_text(),
                'field_type' => 'text',
                'placeholder' => esc_html__('Product SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => true
            ],
            'description' => [
                'name' => 'description',
                'id' => 'description',
                'label' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_text(),
                'field_type' => 'text',
                'placeholder' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => true
            ],
            'short_description' => [
                'name' => 'short_description',
                'id' => 'short-description',
                'label' => esc_html__('Short description', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_text(),
                'field_type' => 'textarea',
                'placeholder' => esc_html__('Short description', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => true
            ],
            'purchase_note' => [
                'name' => 'purchase_note',
                'id' => 'purchase-note',
                'label' => esc_html__('Purchase note', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_text(),
                'field_type' => 'text',
                'placeholder' => esc_html__('Purchase note', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => true
            ],
            'menu_order' => [
                'name' => 'menu_order',
                'id' => 'menu-order',
                'label' => esc_html__('Menu order', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_number(),
                'field_type' => 'number',
                'placeholder' => esc_html__('Menu order', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'sold_individually' => [
                'name' => 'sold_individually',
                'id' => 'sold-individually',
                'label' => esc_html__('Sold individually', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ]
            ],
            'reviews_allowed' => [
                'name' => 'reviews_allowed',
                'id' => 'enable-reviews',
                'label' => esc_html__('Enable reviews', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'disabled' => true
            ],
            'status' => [
                'name' => 'status',
                'id' => 'status',
                'label' => esc_html__('Product status', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'no_default_option' => false
            ],
            'catalog_visibility' => [
                'name' => 'catalog_visibility',
                'id' => 'catalog-visibility',
                'label' => esc_html__('Catalog visibility', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'no_default_option' => false
            ],
            'date_created' => [
                'name' => 'date_created',
                'id' => 'date-created',
                'class' => 'wcbe-input-md wcbe-datepicker',
                'label' => esc_html__('Date', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'date',
                'data_to_id' => '',
                'placeholder' => esc_html__('Date', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'post_author' => [
                'name' => 'post_author',
                'id' => 'author',
                'label' => esc_html__('Author', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'wp_posts_field',
                'operators' => [],
                'field_type' => 'user_select',
                'select2' => true
            ],
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Image', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'image_upload',
                'upload_type' => 'single'
            ],
            'gallery_image_ids' => [
                'name' => 'gallery_image_ids',
                'label' => esc_html__('Gallery', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'image_upload',
                'upload_type' => 'multiple',
                'disabled' => true
            ]
        ];
    }

    public static function taxonomies_tab()
    {
        return [
            'taxonomy' => [
                'name' => 'taxonomy',
                'label' => esc_html__('Taxonomy', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'taxonomy',
                'operators' => Operator::edit_taxonomy(),
                'field_type' => 'taxonomy_group',
                'disabled' => true,
                'is_taxonomy_group' => true
            ],
            'attribute' => [
                'name' => 'attribute',
                'label' => esc_html__('Attribute', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'taxonomy',
                'operators' => Operator::edit_taxonomy(),
                'field_type' => 'attribute_group',
                'disabled' => true,
                'is_attribute_group' => true,
                'attribute_fields' => [
                    'is_visible' => [
                        'label' => esc_html__('Visible on Product Page', 'ithemeland-woo-bulk-product-editor-lite'),
                        'options' => [
                            '' => esc_html__('Current Value', 'ithemeland-woo-bulk-product-editor-lite'),
                            'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                            'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                        ]
                    ],
                    'used_for_variations' => [
                        'label' => esc_html__('Used for variations', 'ithemeland-woo-bulk-product-editor-lite'),
                        'options' => [
                            '' => esc_html__('Current Value', 'ithemeland-woo-bulk-product-editor-lite'),
                            'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                            'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                        ]
                    ]
                ]
            ]
        ];
    }
    public static function pricing_tab()
    {
        return [
            'regular_price' => [
                'name' => 'regular_price',
                'id' => 'regular-price',
                'label' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_regular_price(),
                'field_type' => 'price',
                'placeholder' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'has_rounding' => true,
                'formula_note' => true
            ],
            'sale_price' => [
                'name' => 'sale_price',
                'id' => 'sale-price',
                'label' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_sale_price(),
                'field_type' => 'price',
                'placeholder' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'has_rounding' => true,
                'formula_note' => true
            ],
            'date_on_sale_from' => [
                'name' => 'date_on_sale_from',
                'id' => 'sale-date-from',
                'class' => 'wcbe-input-md wcbe-datepicker wcbe-date-from',
                'label' => esc_html__('Sale date from', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'date',
                'placeholder' => esc_html__('Sale date from', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'data_to_id' => 'wcbe-bulk-edit-form-sale-date-to'

            ],
            'date_on_sale_to' => [
                'name' => 'date_on_sale_to',
                'id' => 'sale-date-to',
                'class' => 'wcbe-input-md wcbe-datepicker',
                'label' => esc_html__('Sale date to', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'date',
                'placeholder' => esc_html__('Sale date to', 'ithemeland-woo-bulk-product-editor-lite'),
                'data_to_id' => '',
                'disabled' => true
            ],
            'tax_status' => [
                'name' => 'tax_status',
                'id' => 'tax-status',
                'label' => esc_html__('Tax status', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'taxable' => esc_html__('Taxable', 'ithemeland-woo-bulk-product-editor-lite'),
                    'shipping' => esc_html__('Shipping Only', 'ithemeland-woo-bulk-product-editor-lite'),
                    'none' => esc_html__('None', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'disabled' => true
            ],
            'tax_class' => [
                'name' => 'tax_class',
                'id' => 'tax-class',
                'label' => esc_html__('Tax class', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [], // Will be filled from controller
                'disabled' => true
            ]
        ];
    }
    public static function shipping_tab()
    {
        return [];
    }
    public static function stock_tab()
    {
        return [
            'manage_stock' => [
                'name' => 'manage_stock',
                'id' => 'manage-stock',
                'label' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'no_default_option' => false,
                'variable' => false
            ],
            'stock_status' => [
                'name' => 'stock_status',
                'id' => 'stock-status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => wc_get_product_stock_status_options(),
                'no_default_option' => false,
                'disabled' => true,
                'variable' => false
            ],
            'stock_quantity' => [
                'name' => 'stock_quantity',
                'id' => 'stock-quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'placeholder' => esc_html__('Set Number', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_number(),
                'field_type' => 'number',
                'variable' => false
            ],
            'backorders' => [
                'name' => 'backorders',
                'id' => 'backorders',
                'label' => esc_html__('Allow backorders', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => wc_get_product_backorder_options(),
                'no_default_option' => false,
                'disabled' => true,
                'variable' => false
            ]
        ];
    }
    public static function type_tab()
    {
        return [
            'product_type' => [
                'name' => 'product_type',
                'id' => 'product-type',
                'label' => esc_html__('Product type', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => wc_get_product_types(),
                'no_default_option' => false,
                'disabled' => true,
                'variable' => false
            ],
            'featured' => [
                'name' => 'featured',
                'id' => 'featured',
                'label' => esc_html__('Featured', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'no_default_option' => false,
                'variable' => false
            ],
            'virtual' => [
                'name' => 'virtual',
                'id' => 'virtual',
                'label' => esc_html__('Virtual', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'no_default_option' => false,
                'disabled' => true,
                'variable' => false
            ],
            'downloadable' => [
                'name' => 'downloadable',
                'id' => 'downloadable',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => [],
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'no_default_option' => false,
                'disabled' => true,
                'variable' => false
            ],
            'download_limit' => [
                'name' => 'download_limit',
                'id' => 'download-limit',
                'label' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_number(),
                'field_type' => 'number',
                'placeholder' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => false
            ],
            'download_expiry' => [
                'name' => 'download_expiry',
                'id' => 'download-expiry',
                'label' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_number(),
                'field_type' => 'number',
                'placeholder' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => false
            ],
            '_product_url' => [
                'name' => '_product_url',
                'id' => 'product-url',
                'label' => esc_html__('Product url', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'meta_field',
                'operators' => [],
                'field_type' => 'text',
                'placeholder' => esc_html__('Product url', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'variable' => false
            ],
            '_button_text' => [
                'name' => '_button_text',
                'id' => 'button-text',
                'label' => esc_html__('Button text', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'meta_field',
                'operators' => [],
                'field_type' => 'text',
                'placeholder' => esc_html__('Button text', 'ithemeland-woo-bulk-product-editor-lite'),
                'variable' => false
            ],

            'upsell_ids' => [
                'name' => 'upsell_ids',
                'id' => 'wcbe-bulk-edit-form-upsells',
                'class' => 'wcbe-get-products-ajax wcbe-select2-item',
                'label' => esc_html__('Upsells', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_taxonomy(),
                'field_type' => 'upsell_crosssell',
                'select2' => true,
                'multiple' => true,
                'variable' => false,

            ],
            'cross_sell_ids' => [
                'name' => 'cross_sell_ids',
                'id' => 'wcbe-bulk-edit-form-cross-sells',
                'class' => 'wcbe-get-products-ajax wcbe-select2-item',
                'label' => esc_html__('Cross-Sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_taxonomy(),
                'field_type' => 'upsell_crosssell',
                'variable' => false,

            ]
        ];
    }
}
