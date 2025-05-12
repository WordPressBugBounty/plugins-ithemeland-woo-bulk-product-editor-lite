<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit();

use wcbel\classes\helpers\Operator;

class NewFormItems
{
    public static function general_tab()
    {
        return [
            'title' => [
                'name' => 'title',
                'label' => esc_html__('Product title', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Product title', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
            ],
            'slug' => [
                'name' => 'slug',
                'label' => esc_html__('Product slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Product slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'sku' => [
                'name' => 'sku',
                'label' => esc_html__('Product SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Product SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'description' => [
                'name' => 'description',
                'label' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'short_description' => [
                'name' => 'short-description',
                'label' => esc_html__('Short description', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'textarea',
                'placeholder' => esc_html__('Short description', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'purchase_note' => [
                'name' => 'purchase-note',
                'label' => esc_html__('Purchase note', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Purchase note', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'menu_order' => [
                'name' => 'menu-order',
                'label' => esc_html__('Menu order', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Menu order', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'sold_individually' => [
                'name' => 'sold-individually',
                'label' => esc_html__('Sold individually', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => Operator::get_current_value(),
                'disabled' => true
            ],
            'reviews_allowed' => [
                'name' => 'reviews-allowed',
                'label' => esc_html__('Enable reviews', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => Operator::get_current_value(),
                'disabled' => true
            ],
            'status' => [
                'name' => 'product-status',
                'label' => esc_html__('Product status', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [],
                'disabled' => true
            ],
            'visibility' => [
                'name' => 'catalog-visibility',
                'label' => esc_html__('Catalog visibility', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [],
                'disabled' => true
            ],
            'date_created' => [
                'name' => 'date-created',
                'label' => esc_html__('Date', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'date',
                'placeholder' => esc_html__('Date', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'author' => [
                'name' => 'author',
                'label' => esc_html__('Author', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => '',
                'field_type' => 'user_select',
                'select2' => true,
                'disabled' => true
            ],
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Image', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'image_upload',
                'upload_type' => 'single',
                'disabled' => true
            ],
            'gallery_image_ids' => [
                'name' => 'gallery_image_ids',
                'label' => esc_html__('Gallery', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
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
                'label' => esc_html__('Taxonomy', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'taxonomy',
                'operators' => Operator::edit_taxonomy(),
                'disabled' => true
            ],
            'attribute' => [
                'label' => esc_html__('Attribute', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'taxonomy',
                'operators' => Operator::edit_taxonomy(),
                'disabled' => false,
                'attribute_fields' => [
                    'is_visible' => [
                        'label' => esc_html__('Visible on Product Page', 'ithemeland-woo-bulk-product-editor-lite'),
                        'options' => Operator::edit_taxonomy(),
                    ],
                    'used_for_variations' => [
                        'label' => esc_html__('Used for variations', 'ithemeland-woo-bulk-product-editor-lite'),
                        'options' => Operator::get_current_value(),
                    ]
                ]
            ]
        ];
    }

    public static function pricing_tab()
    {
        return [
            'price_section' => [
                'name' => 'price-section',
                'label' => esc_html__('Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'regular_price' => [
                'name' => 'regular-price',
                'label' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'price_with_rounding',
                'placeholder' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'round_items' => ['' => 'Round item'] + Operator::round_items()
            ],
            'sale_price' => [
                'name' => 'sale-price',
                'label' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'price_with_rounding',
                'placeholder' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'round_items' => ['' => 'Round item'] + Operator::round_items()
            ],
            'date_on_sale_from' => [
                'name' => 'date-on-sale-from',
                'label' => esc_html__('Sale date from', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'date',
                'placeholder' => esc_html__('Sale date from', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'datepicker_options' => [
                    'data_to_id' => 'wcbe-bulk-new-form-sale-date-to'
                ]
            ],
            'date_on_sale_to' => [
                'name' => 'date-on-sale-to',
                'label' => esc_html__('Sale date to', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'date',
                'placeholder' => esc_html__('Sale date to', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'tax_section' => [
                'name' => 'tax-section',
                'label' => esc_html__('Tax', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'tax_status' => [
                'name' => 'tax-status',
                'label' => esc_html__('Tax status', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [
                    '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                ],
                'disabled' => true
            ],
            'tax_class' => [
                'name' => 'tax-class',
                'label' => esc_html__('Tax class', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true,
                'select2' => false
            ]
        ];
    }

    public static function shipping_tab()
    {
        return [
            'shipping_section' => [
                'name' => 'shipping_section',
                'label' => esc_html__('Shipping', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'shipping_class' => [
                'name' => 'shipping-class',
                'label' => esc_html__('Shipping class', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'select2' => false,
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'dimensions_section' => [
                'name' => 'dimensions_section',
                'label' => '',
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'width' => [
                'name' => 'width',
                'label' => esc_html__('Width', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Width', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'height' => [
                'name' => 'height',
                'label' => esc_html__('Height', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Height', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'length' => [
                'name' => 'length',
                'label' => esc_html__('Length', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Length', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'weight' => [
                'name' => 'weight',
                'label' => esc_html__('Weight', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Weight', 'ithemeland-woo-bulk-product-editor-lite')
            ]
        ];
    }
    public static function stock_tab()
    {
        return [
            'stock_section' => [
                'name' => 'stock_section',
                'label' => esc_html__('Stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'manage_stock' => [
                'name' => 'manage-stock',
                'label' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'stock_status' => [
                'name' => 'stock-status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'stock_quantity' => [
                'name' => 'stock-quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number'
            ],
            'backorders' => [
                'name' => 'backorders',
                'label' => esc_html__('Allow backorders', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ]
        ];
    }

    public static function type_tab()
    {
        return [
            'type_section' => [
                'name' => 'typesection',
                'label' => esc_html__('Type', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'product_type' => [
                'name' => 'product-type',
                'label' => esc_html__('Product type', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'featured' => [
                'name' => 'featured',
                'label' => esc_html__('Featured', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')
            ],
            'virtual' => [
                'name' => 'virtual',
                'label' => esc_html__('Virtual', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'downloadable_section' => [
                'name' => 'downloadable-section',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'downloadable' => [
                'name' => 'downloadable',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'select',
                'options' => [
                    'yes' => esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite'),
                    'no' => esc_html__('No', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'first_option' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'download_limit' => [
                'name' => 'download-limit',
                'label' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'download_expiry' => [
                'name' => 'download-expiry',
                'label' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'field_type' => 'number',
                'placeholder' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'external_section' => [
                'name' => 'external-section',
                'label' => esc_html__('External/Affiliate product', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'product_url' => [
                'name' => 'product-url',
                'label' => esc_html__('Product url', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'meta_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Product url', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'button_text' => [
                'name' => 'button-text',
                'label' => esc_html__('Button text', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'meta_field',
                'field_type' => 'text',
                'placeholder' => esc_html__('Button text', 'ithemeland-woo-bulk-product-editor-lite'),
                'disabled' => true
            ],
            'upsell_section' => [
                'name' => 'upsell-section',
                'label' => '',
                'update_type' => 'section_header',
                'field_type' => 'section_header'
            ],
            'upsell_ids' => [
                'name' => 'upsell-ids',
                'class' => 'wcbe-select2',
                'id' => 'wcbe-bulk-new-form-upsells',
                'label' => esc_html__('Upsells', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_taxonomy(),
                'field_type' => 'upsell_crosssell',
                'select2' => true,
                'multiple' => true,
                'variable' => false,
            ],
            'cross_sell_ids' => [
                'name' => 'cross-sell-ids',
                'class' => 'wcbe-select2',
                'id' => 'wcbe-bulk-new-form-cross-sells',
                'label' => esc_html__('Cross-Sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'update_type' => 'woocommerce_field',
                'operators' => Operator::edit_taxonomy(),
                'field_type' => 'upsell_crosssell',
                'select2' => true,
                'multiple' => true,
                'variable' => false,

            ]
        ];
    }
}
