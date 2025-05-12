<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\column\Column_Main;
use wcbel\classes\helpers\Meta_Fields;

class Column extends Column_Main
{
    const SHOW_ID_COLUMN = true;
    const DEFAULT_PROFILE_NAME = 'default';

    private static $instance;

    private $deactivated_columns;
    private $main_columns;
    private $columns;
    private $column_keys;
    private $main_column_keys;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private  function __construct()
    {
        $this->deactivated_columns = [];
        $this->columns_option_name = "wcbe_column_fields";
        $this->active_columns_option_name = 'wcbe_active_columns';

        // compatible with "woocommerce min/max quantities"
        if (defined("WC_MIN_MAX_QUANTITIES")) {
            add_filter('wcbe_column_fields', [$this, 'set_wc_min_max_quantities_fields']);
        } else {
            $this->set_deactivated_columns(array_keys($this->get_wc_min_max_quantities_fields()));
        }

        // compatible with "yith min/max quantities"
        if (defined("YWMMQ_INIT")) {
            add_filter('wcbe_column_fields', [$this, 'set_yith_min_max_quantities_fields']);
        } else {
            $this->set_deactivated_columns(array_keys($this->get_yith_min_max_quantities_fields()));
        }

        // compatible with "iThemeland WooCommerce Dynamic Prices By User Role Plugin"
        if (class_exists("it_WC_Dynamic_Pricing")) {
            add_filter('wcbe_column_fields', [$this, 'set_it_wc_dynamic_pricing_fields']);
        } else {
            $this->set_deactivated_columns(array_keys($this->get_it_wc_dynamic_pricing_fields()));
        }

        // compatible with "yith vendors"
        if (defined("YITH_WPV_INIT")) {
            add_filter('wcbe_column_fields', [$this, 'set_yith_vendors_fields']);
        } else {
            $this->set_deactivated_columns(array_keys($this->get_yith_vendors_fields()));
        }

        // compatible with "woocommerce product vendors"
        if (class_exists("WC_Product_Vendors")) {
            add_filter('wcbe_column_fields', [$this, 'set_wc_vendors_fields']);
        } else {
            $this->set_deactivated_columns(array_keys($this->get_wc_vendors_fields()));
        }

        // compatible with "yith cost of goods"
        if (defined("YITH_COG_INIT")) {
            add_filter('wcbe_column_fields', [$this, 'set_yith_cost_of_goods_fields']);
        } else {
            $this->set_deactivated_columns(array_keys($this->get_yith_cost_of_goods_fields()));
        }

        // compatible with "woo multi currency"
        if (class_exists("WOOMULTI_CURRENCY_F")) {
            add_filter('wcbe_column_fields', [$this, 'set_woo_multi_currency_fields']);
        } else {
            $this->set_deactivated_columns([
                '_regular_price_wmcp',
                '_sale_price_wmcp',
            ]);
        }
    }

    public static function get_static_columns()
    {
        return [
            'title' => [
                'name' => 'title',
                'label' => esc_attr__('Product Title', 'ithemeland-woo-bulk-product-editor-lite'),
                'fetch_type' => 'woocommerce'
            ],
        ];
    }

    public function get_deactivated_columns()
    {
        return $this->deactivated_columns;
    }

    private function set_deactivated_columns($columns)
    {
        if (!empty($columns) && is_array($columns)) {
            foreach ($columns as $column) {
                $this->deactivated_columns[] = sanitize_text_field($column);
            }
        }
    }

    public static function get_columns_title()
    {
        return [
            'stock_quantity' => "Set Stock quantity. If this is a variable product this <br> value will be used to control stock for all variations, unless you define stock <br>at variation level. <br> Note: if to set count of products in Stock quantity, Manage stock option automatically set as TRUE!",
            'stock_status' => 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend. Note: Does Not work if the product Manage stock option is not activated!',
            'date_on_sale_from' => 'The sale will start at 00:00:00 of "From" date and end at 23:59:59 of "To" date.',
            'tax_status' => 'Define whether or not the entire product <br> is taxable, or just the cost of shipping it.',
            'tax_class' => 'Choose a tax class for this product. Tax <br> classes are used to apply different tax rates specific <br> to certain types of product.',
            'sku' => "SKU refers to a Stock-keeping unit, a unique <br> identifier  for each distinct product and <br> service that can be purchased.",
            'backorders' => 'If managing stock, this controls whether or not <br> backorders are allowed. If enabled, stock quantity can go below 0.',
            'shipping_class' => 'Shipping classes are used by certain shipping <br> methods to group similar products.',
            'upsell_ids' => 'Upsells are products which you recommend <br> instead of the currently viewed product, for example <br>, products that are more profitable or better quality or more expensive.',
            'cross_sell_ids' => 'Cross-sells are products which you promote <br> in the cart, based on the current product.',
            'purchase_note' => 'Enter an optional note to send the customer <br> after purchase.',
            'download_limit' => 'Leave blank for unlimited re-downloads.',
            'download_expiry' => 'Enter the number of days before a download <br> link expires, or leave blank.',
            'sold_individually' => 'Enable this to only allow one of this <br> item to be bought in a single order',
            'product_url' => 'Enter the external URL to the product.',
            'button_text' => 'This text will be shown on the button <br> linking to the external product.',
            'catalog_visibility' => 'This setting determines which shop <br> pages products will be listed on',
            'virtual' => 'Virtual products are intangible and are not shipped.',
            'downloadable' => 'Downloadable products give access to a file upon purchase.',
        ];
    }

    public function update_meta_field_items()
    {
        $presets = $this->get_presets();
        $meta_field_repository = Meta_Field::get_instance();
        $meta_fields = $meta_field_repository->get();
        if (!empty($presets)) {
            foreach ($presets as $preset) {
                if (!empty($preset['fields'])) {
                    foreach ($preset['fields'] as $field) {
                        if (isset($field['field_type'])) {
                            if (isset($meta_fields[$field['name']])) {
                                $preset['fields'][$field['name']]['content_type'] = Meta_Fields::get_meta_field_type($meta_fields[$field['name']]['main_type'], $meta_fields[$field['name']]['sub_type']);
                                $this->update($preset);
                            }
                        }
                    }
                }
            }
        }
    }

    public function sync_active_columns()
    {
        $active_columns = $this->get_active_columns();

        if (!empty($active_columns['fields'])) {
            $columns = $this->get_columns();
            foreach ($active_columns['fields'] as $column_key => $column) {
                if (!isset($columns[$column_key])) {
                    unset($active_columns['fields'][$column_key]);
                    continue;
                }

                $active_columns['fields'][$column_key]['name'] = $columns[$column_key]['name'];
                if (isset($columns[$column_key]['content_type'])) {
                    $active_columns['fields'][$column_key]['content_type'] = $columns[$column_key]['content_type'];
                }
                if (isset($columns[$column_key]['update_type'])) {
                    $active_columns['fields'][$column_key]['update_type'] = $columns[$column_key]['update_type'];
                }
                if (isset($columns[$column_key]['fetch_type'])) {
                    $active_columns['fields'][$column_key]['fetch_type'] = $columns[$column_key]['fetch_type'];
                }
            }

            $this->set_active_columns($active_columns['name'], $active_columns['fields']);
        }
    }

    public function set_default_columns()
    {
        $fields['default'] = [
            'name' => 'Default',
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'default',
            'fields' => $this->get_default_columns_default(),
            'checked' => array_keys($this->get_default_columns_default()),
        ];
        $fields['variations'] = [
            'name' => 'For variations fields only',
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'variations',
            'fields' => $this->get_default_columns_variations(),
            'checked' => array_keys($this->get_default_columns_variations()),
        ];
        $fields['stock'] = [
            'name' => 'Stock',
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'stock',
            'fields' => $this->get_default_columns_stock(),
            'checked' => array_keys($this->get_default_columns_stock()),
        ];
        $fields['prices'] = [
            'name' => 'Prices',
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'prices',
            'fields' => $this->get_default_columns_prices(),
            'checked' => array_keys($this->get_default_columns_prices()),
        ];
        $fields['attachments'] = [
            'name' => 'Downloads, Cross-sells, Up-sells, Grouped',
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'attachments',
            'fields' => $this->get_default_columns_attachments(),
            'checked' => array_keys($this->get_default_columns_attachments()),
        ];
        return update_option('wcbe_column_fields', $fields);
    }

    public function get_grouped_fields()
    {
        $compatible_groups = apply_filters('wcbe_column_profile_compatible_groups', []);
        $grouped_fields = [];
        $fields = $this->get_columns();
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!empty($field['group'])) {
                    if (!empty($compatible_groups[$field['group']])) {
                        if (!isset($grouped_fields['compatibles'])) {
                            $grouped_fields['compatibles'] = [];
                        }

                        $grouped_fields['compatibles'][sanitize_text_field($compatible_groups[$field['group']])][$key] = $field;
                    }
                } else {
                    if (isset($field['field_type'])) {
                        switch ($field['field_type']) {
                            case 'general':
                                $grouped_fields['General'][$key] = $field;
                                break;
                            case 'advanced':
                                $grouped_fields['Advanced'][$key] = $field;
                                break;
                            case 'linked_products':
                                $grouped_fields['Linked Products'][$key] = $field;
                                break;
                            case 'shipping':
                                $grouped_fields['Shipping'][$key] = $field;
                                break;
                            case 'inventory':
                                $grouped_fields['Inventory'][$key] = $field;
                                break;
                            case 'taxonomy':
                                $grouped_fields['Taxonomies'][$key] = $field;
                                break;
                            case 'attribute':
                                $grouped_fields['Attributes'][$key] = $field;
                                break;
                            case 'custom_field':
                                $grouped_fields['Custom Fields'][$key] = $field;
                                break;
                            case 'woocommerce_min_max_quantities':
                                $grouped_fields['compatibles']['WooCommerce Min/Max quantities'][$key] = $field;
                                break;
                            case 'yith_min_max_quantities':
                                $grouped_fields['compatibles']['Yith Min/Max quantities'][$key] = $field;
                                break;
                            case 'woocommerce_vendors':
                                $grouped_fields['compatibles']['WooCommerce vendors'][$key] = $field;
                                break;
                            case 'yith_vendors':
                                $grouped_fields['compatibles']['Yith vendors'][$key] = $field;
                                break;
                            case 'yith_cost_of_goods':
                                $grouped_fields['compatibles']['Yith Cost of goods'][$key] = $field;
                                break;
                            case 'woocommerce_cost_of_goods':
                                $grouped_fields['compatibles']['Woocommerce Cost of goods'][$key] = $field;
                                break;
                            case 'woo_multi_currency':
                                $grouped_fields['compatibles']['Woo multi currency'][$key] = $field;
                                break;
                            case 'yith_badge_management':
                                $grouped_fields['compatibles']['Yith badge management'][$key] = $field;
                                break;
                            case 'ithemeland_badge':
                                $grouped_fields['compatibles']['iThemeland badge'][$key] = $field;
                                break;
                            case 'yikes_custom_product_tabs':
                                $grouped_fields['compatibles']['Yikes Custom product tabs'][$key] = $field;
                                break;
                            case 'it_wc_dynamic_pricing':
                                $grouped_fields['compatibles']['iThemeland WooCommerce Dynamic Pricing'][$key] = $field;
                                break;
                        }
                    } else {
                        $grouped_fields['General'][$key] = $field;
                    }
                }
            }
        }
        return $grouped_fields;
    }

    public function get_main_column_keys()
    {
        if (empty($this->main_column_keys)) {
            $this->set_main_column_keys();
        }
        return $this->main_column_keys;
    }

    public function get_column_keys()
    {
        if (empty($this->column_keys)) {
            $this->set_column_keys();
        }
        return $this->column_keys;
    }

    public function get_main_columns()
    {
        if (empty($this->main_columns)) {
            $this->set_columns();
        }

        return $this->main_columns;
    }

    public function get_columns()
    {
        if (empty($this->columns)) {
            $this->set_columns();
        }

        return $this->columns;
    }

    private function set_main_column_keys()
    {
        $main_column = $this->get_main_columns();
        $keys = array_keys($main_column);
        if (!in_array('_sku', $keys)) {
            array_push($keys, "_sku");
        }
        $this->main_column_keys = $keys;
    }

    private function set_column_keys()
    {
        $fields = $this->get_columns();
        $this->column_keys = array_keys($fields);
    }

    public function set_columns()
    {
        $this->main_columns = [
            'post_parent' => [
                'name' => 'post_parent',
                'label' => esc_html__('Parent', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => false,
                'content_type' => 'numeric_without_calculator',
                'allowed_type' => ['variation'],
                'field_type' => 'general',
                'fetch_type' => 'wp_posts'
            ],
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Thumb', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'image',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'gallery_image_ids' => [
                'name' => 'gallery_image_ids',
                'label' => esc_html__('Gallery', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'gallery',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'slug' => [
                'name' => 'slug',
                'label' => esc_html__('Slug', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'description' => [
                'name' => 'description',
                'label' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'short_description' => [
                'name' => 'short_description',
                'label' => esc_html__('Short Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'status' => [
                'name' => 'status',
                'label' => esc_html__('Status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [],
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'product_type' => [
                'name' => 'product_type',
                'label' => esc_html__('Product Type', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_types(),
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            '_product_url' => [
                'name' => '_product_url',
                'label' => esc_html__('Product URL', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'text',
                'allowed_type' => ['external'],
                'field_type' => 'general',
                'update_type' => 'meta_field',
                'fetch_type' => 'meta_field'
            ],
            '_button_text' => [
                'name' => '_button_text',
                'label' => esc_html__('Button Text', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'text',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'meta_field',
                'fetch_type' => 'meta_field'
            ],
            'catalog_visibility' => [
                'name' => 'catalog_visibility',
                'label' => esc_html__('Catalog Visibility', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_visibility_options(),
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'featured' => [
                'name' => 'featured',
                'label' => esc_html__('Featured', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'regular_price' => [
                'name' => 'regular_price',
                'label' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'regular_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sale_price' => [
                'name' => 'sale_price',
                'label' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'sale_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_on_sale_from' => [
                'name' => 'date_on_sale_from',
                'label' => esc_html__('Sale time from', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_on_sale_to' => [
                'name' => 'date_on_sale_to',
                'label' => esc_html__('Sale time to', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            '_children' => [
                'name' => '_children',
                'label' => esc_html__('Grouped Products', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_products',
                'allowed_type' => ['grouped'],
                'field_type' => 'general',
                'update_type' => 'meta_field',
                'fetch_type' => 'meta_field'
            ],
            'downloadable' => [
                'name' => 'downloadable',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variation', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'downloadable_files' => [
                'name' => 'downloadable_files',
                'label' => esc_html__('Downloadable Files', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_files',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'download_limit' => [
                'name' => 'download_limit',
                'label' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'download_expiry' => [
                'name' => 'download_expiry',
                'label' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'tax_status' => [
                'name' => 'tax_status',
                'label' => esc_html__('Tax status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [
                    'taxable' => 'Taxable',
                    'shipping' => 'Shipping Only',
                    'none' => 'None',
                ],
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'tax_class' => [
                'name' => 'tax_class',
                'label' => esc_html__('Tax class', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [],
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sku' => [
                'name' => 'sku',
                'label' => esc_html__('SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'text',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'manage_stock' => [
                'name' => 'manage_stock',
                'label' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'variation'],
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_quantity' => [
                'name' => 'stock_quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_status' => [
                'name' => 'stock_status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_stock_status_options(),
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'backorders' => [
                'name' => 'backorders',
                'label' => esc_html__('Allow backorders', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_backorder_options(),
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sold_individually' => [
                'name' => 'sold_individually',
                'label' => esc_html__('Sold individually', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'external', 'grouped'],
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'weight' => [
                'name' => 'weight',
                'label' => esc_html__('Weight', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'external', 'grouped', 'variation'],
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'length' => [
                'name' => 'length',
                'label' => esc_html__('Length', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'width' => [
                'name' => 'width',
                'label' => esc_html__('Width', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'height' => [
                'name' => 'height',
                'label' => esc_html__('Height', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'shipping_class' => [
                'name' => 'shipping_class',
                'label' => esc_html__('Shipping class', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [],
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'upsell_ids' => [
                'name' => 'upsell_ids',
                'label' => esc_html__('Up-sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_products',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'linked_products',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'cross_sell_ids' => [
                'name' => 'cross_sell_ids',
                'label' => esc_html__('Cross-sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_products',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'linked_products',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'purchase_note' => [
                'name' => 'purchase_note',
                'label' => esc_html__('Purchase note', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'advanced',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'menu_order' => [
                'name' => 'menu_order',
                'label' => esc_html__('Menu order', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'advanced',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'reviews_allowed' => [
                'name' => 'reviews_allowed',
                'label' => esc_html__('Reviews allowed', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'advanced',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'virtual' => [
                'name' => 'virtual',
                'label' => esc_html__('Virtual', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'post_author' => [
                'name' => 'post_author',
                'label' => esc_html__('Author', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_author',
                'options' => [],
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'wp_posts_field',
                'fetch_type' => 'wp_posts'
            ],
            'total_sales' => [
                'name' => 'total_sales',
                'label' => esc_html__('Total sales', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'review_count' => [
                'name' => 'review_count',
                'label' => esc_html__('Review count', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'average_rating' => [
                'name' => 'average_rating',
                'label' => esc_html__('Average rating', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_created' => [
                'name' => 'date_created',
                'label' => esc_html__('Date Published', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date_time_picker',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
        ];

        $this->columns = apply_filters('wcbe_column_fields', $this->main_columns);
    }

    public function set_default_active_columns()
    {
        return $this->set_active_columns(self::DEFAULT_PROFILE_NAME, self::get_default_columns_default());
    }

    public static function get_default_columns_name()
    {
        return [
            'default',
            'variations',
            'stock',
            'prices',
            'attachments',
        ];
    }

    public static function get_default_columns_default()
    {
        return [
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'image',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'description' => [
                'name' => 'description',
                'label' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'short_description' => [
                'name' => 'short_description',
                'label' => esc_html__('Short Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Short Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'product_type' => [
                'name' => 'product_type',
                'label' => esc_html__('Product Type', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Product Type', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_types(),
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'status' => [
                'name' => 'status',
                'label' => esc_html__('Status', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //$product_statuses,
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'regular_price' => [
                'name' => 'regular_price',
                'label' => esc_html__('Regular Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Regular Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'regular_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sale_price' => [
                'name' => 'sale_price',
                'label' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'sale_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sku' => [
                'name' => 'sku',
                'label' => esc_html__('SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'text',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'manage_stock' => [
                'name' => 'manage_stock',
                'label' => esc_html__('Manage Stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Manage Stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_quantity' => [
                'name' => 'stock_quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_status' => [
                'name' => 'stock_status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_stock_status_options(),
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'gallery_image_ids' => [
                'name' => 'gallery_image_ids',
                'label' => esc_html__('Gallery', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Gallery', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'gallery',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
        ];
    }

    public static function get_default_columns_variations()
    {
        return [
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'image',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_quantity' => [
                'name' => 'stock_quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'description' => [
                'name' => 'description',
                'label' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Description', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'textarea',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'regular_price' => [
                'name' => 'regular_price',
                'label' => esc_html__('Regular Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Regular Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'regular_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sale_price' => [
                'name' => 'sale_price',
                'label' => esc_html__('Sale Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale Price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'sale_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_on_sale_from' => [
                'name' => 'date_on_sale_from',
                'label' => esc_html__('Sale time from', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale time from', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_on_sale_to' => [
                'name' => 'date_on_sale_to',
                'label' => esc_html__('Sale time to', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale time to', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sku' => [
                'name' => 'sku',
                'label' => esc_html__('SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('SKU', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'text',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'manage_stock' => [
                'name' => 'manage_stock',
                'label' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_status' => [
                'name' => 'stock_status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_stock_status_options(),
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'virtual' => [
                'name' => 'virtual',
                'label' => esc_html__('Virtual', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Virtual', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'downloadable' => [
                'name' => 'downloadable',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variation', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'downloadable_files' => [
                'name' => 'downloadable_files',
                'label' => esc_html__('Downloadable Files', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Downloadable Files', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_files',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'download_limit' => [
                'name' => 'download_limit',
                'label' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'download_expiry' => [
                'name' => 'download_expiry',
                'label' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'tax_class' => [
                'name' => 'tax_class',
                'label' => esc_html__('Tax class', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Tax class', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //$tax_classes,
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'backorders' => [
                'name' => 'backorders',
                'label' => esc_html__('Allow backorders', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Allow backorders', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_backorder_options(),
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'weight' => [
                'name' => 'weight',
                'label' => esc_html__('Weight', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Weight', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'external', 'grouped', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'length' => [
                'name' => 'length',
                'label' => esc_html__('Length', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Length', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'width' => [
                'name' => 'width',
                'label' => esc_html__('Width', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Width', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'height' => [
                'name' => 'height',
                'label' => esc_html__('Height', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Height', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'shipping',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'post_parent' => [
                'name' => 'post_parent',
                'label' => esc_html__('Parent', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Parent', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => false,
                'content_type' => 'numeric_without_calculator',
                'allowed_type' => ['variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'fetch_type' => 'wp_posts'
            ],
        ];
    }

    public static function get_default_columns_stock()
    {
        return [
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'image',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'manage_stock' => [
                'name' => 'manage_stock',
                'label' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Manage stock', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_quantity' => [
                'name' => 'stock_quantity',
                'label' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Stock quantity', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'stock_status' => [
                'name' => 'stock_status',
                'label' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Stock status', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'select',
                'options' => [], //wc_get_product_stock_status_options(),
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'inventory',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
        ];
    }

    public static function get_default_columns_prices()
    {
        return [
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'image',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'regular_price' => [
                'name' => 'regular_price',
                'label' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Regular price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'regular_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'sale_price' => [
                'name' => 'sale_price',
                'label' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale price', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'sale_price',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_on_sale_from' => [
                'name' => 'date_on_sale_from',
                'label' => esc_html__('Sale time from', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale time from', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'date_on_sale_to' => [
                'name' => 'date_on_sale_to',
                'label' => esc_html__('Sale time to', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Sale time to', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date',
                'allowed_type' => ['simple', 'composite', 'variation', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
        ];
    }

    public static function get_default_columns_attachments()
    {
        return [
            'image_id' => [
                'name' => 'image_id',
                'label' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Thumbnail', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'image',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'downloadable' => [
                'name' => 'downloadable',
                'label' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Downloadable', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'checkbox_dual_mode',
                'allowed_type' => ['simple', 'composite', 'variation', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'downloadable_files' => [
                'name' => 'downloadable_files',
                'label' => esc_html__('Downloadable Files', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Downloadable Files', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_files',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'download_limit' => [
                'name' => 'download_limit',
                'label' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Download limit', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'download_expiry' => [
                'name' => 'download_expiry',
                'label' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Download expiry', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'numeric',
                'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variation'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'upsell_ids' => [
                'name' => 'upsell_ids',
                'label' => esc_html__('Up-sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Up-sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_products',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'linked_products',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            'cross_sell_ids' => [
                'name' => 'cross_sell_ids',
                'label' => esc_html__('Cross-sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Cross-sells', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_products',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'linked_products',
                'update_type' => 'woocommerce_field',
                'fetch_type' => 'woocommerce'
            ],
            '_children' => [
                'name' => '_children',
                'label' => esc_html__('Grouped Products', 'ithemeland-woo-bulk-product-editor-lite'),
                'title' => esc_html__('Grouped Products', 'ithemeland-woo-bulk-product-editor-lite'),
                'editable' => true,
                'content_type' => 'select_products',
                'allowed_type' => ['grouped'],
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
                'update_type' => 'meta_field',
                'fetch_type' => 'meta_field'
            ],
        ];
    }

    public function set_wc_min_max_quantities_fields($fields)
    {
        $plugin_fields = $this->get_wc_min_max_quantities_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_yith_min_max_quantities_fields($fields)
    {
        $plugin_fields = $this->get_yith_min_max_quantities_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_yith_vendors_fields($fields)
    {
        $plugin_fields = $this->get_yith_vendors_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_wc_vendors_fields($fields)
    {
        $plugin_fields = $this->get_wc_vendors_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_yith_cost_of_goods_fields($fields)
    {
        $plugin_fields = $this->get_yith_cost_of_goods_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_wc_cost_of_goods_fields($fields)
    {
        $plugin_fields = $this->get_wc_cost_of_goods_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_woo_multi_currency_fields($fields)
    {
        $plugin_fields = $this->get_woo_multi_currency_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_yith_badge_management_premium_fields($fields)
    {
        $plugin_fields = $this->get_yith_badge_management_premium_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_yith_badge_management_free_fields($fields)
    {
        $plugin_fields = $this->get_yith_badge_management_free_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_wc_advanced_product_labels_fields($fields)
    {
        $plugin_fields = $this->get_wc_advanced_product_labels_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_yikes_custom_product_tabs_fields($fields)
    {
        $plugin_fields = $this->get_yikes_custom_product_tabs_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    public function set_it_wc_dynamic_pricing_fields($fields)
    {
        $plugin_fields = $this->get_it_wc_dynamic_pricing_fields();
        if (!empty($plugin_fields)) {
            foreach ($plugin_fields as $key => $items) {
                $fields[$key] = $items;
            }
        }

        return $fields;
    }

    private function get_wc_min_max_quantities_fields()
    {
        $fields['minimum_allowed_quantity'] = [
            'name' => 'minimum_allowed_quantity',
            'label' => esc_html__('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['maximum_allowed_quantity'] = [
            'name' => 'maximum_allowed_quantity',
            'label' => esc_html__('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['group_of_quantity'] = [
            'name' => 'group_of_quantity',
            'label' => esc_html__('Group of quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['allow_combination'] = [
            'name' => 'allow_combination',
            'label' => esc_html__('Allow combination', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['variable'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['minmax_do_not_count'] = [
            'name' => 'minmax_do_not_count',
            'label' => esc_html__('Order rules: Do not count', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['minmax_cart_exclude'] = [
            'name' => 'minmax_cart_exclude',
            'label' => esc_html__('Order rules: Exclude', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['minmax_category_group_of_exclude'] = [
            'name' => 'minmax_category_group_of_exclude',
            'label' => esc_html__('Category rules: Exclude', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['min_max_rules'] = [
            'name' => 'min_max_rules',
            'label' => esc_html__('Min/Max Rules', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['variation'],
            'field_type' => 'woocommerce_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_yith_min_max_quantities_fields()
    {
        $fields['_ywmmq_product_minimum_quantity'] = [
            'name' => '_ywmmq_product_minimum_quantity',
            'label' => esc_html__('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'yith_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_ywmmq_product_maximum_quantity'] = [
            'name' => '_ywmmq_product_maximum_quantity',
            'label' => esc_html__('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'yith_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_ywmmq_product_step_quantity'] = [
            'name' => '_ywmmq_product_step_quantity',
            'label' => esc_html__('Groups of quantity', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'yith_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_ywmmq_product_exclusion'] = [
            'name' => '_ywmmq_product_exclusion',
            'label' => esc_html__('Exclude product', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'yith_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_ywmmq_product_quantity_limit_override'] = [
            'name' => '_ywmmq_product_quantity_limit_override',
            'label' => esc_html__('Override product', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'yith_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_ywmmq_product_quantity_limit_variations_override'] = [
            'name' => '_ywmmq_product_quantity_limit_variations_override',
            'label' => esc_html__('Enable variation', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable', 'external', 'variation'],
            'field_type' => 'yith_min_max_quantities',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_yith_vendors_fields()
    {
        $fields['yith_shop_vendor'] = [
            'name' => 'yith_shop_vendor',
            'label' => esc_html__('Yith vendor', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'yith_shop_vendor',
            'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
            'field_type' => 'yith_vendors',
            'update_type' => 'taxonomy',
            'fetch_type' => 'taxonomy'
        ];
        $fields['_product_commission'] = [
            'name' => '_product_commission',
            'label' => esc_html__('Product commission', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
            'field_type' => 'yith_vendors',
            'update_type' => 'meta_field',
            'fetch_type' => 'taxonomy'
        ];

        return $fields;
    }

    private function get_wc_vendors_fields()
    {
        $fields['wcpv_product_vendors'] = [
            'name' => 'wcpv_product_vendors',
            'label' => esc_html__('WooCommerce vendor', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'wc_product_vendor',
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'woocommerce_vendors',
            'update_type' => 'taxonomy',
            'fetch_type' => 'taxonomy'
        ];
        $fields['_wcpv_product_taxes'] = [
            'name' => '_wcpv_product_taxes',
            'label' => esc_html__('Tax Handling', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'select',
            'options' => [
                '' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
                'keep-tax' => esc_html__('Keep taxes', 'ithemeland-woo-bulk-product-editor-lite'),
                'pass-tax' => esc_html__('Pass taxes', 'ithemeland-woo-bulk-product-editor-lite'),
                'split-tax' => esc_html__('Split taxes', 'ithemeland-woo-bulk-product-editor-lite'),
            ],
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'woocommerce_vendors',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_wcpv_product_pass_shipping'] = [
            'name' => '_wcpv_product_pass_shipping',
            'label' => esc_html__('Pass shipping', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'woocommerce_vendors',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_wcpv_product_commission'] = [
            'name' => '_wcpv_product_commission',
            'label' => esc_html__('Product commission', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variable', 'variation'],
            'field_type' => 'woocommerce_vendors',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_yith_cost_of_goods_fields()
    {
        $fields['yith_cog_cost'] = [
            'name' => 'yith_cog_cost',
            'label' => esc_html__('Yith Cost of goods', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variation', 'variable'],
            'field_type' => 'yith_cost_of_goods',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_wc_cost_of_goods_fields()
    {
        $fields['_wc_cog_cost'] = [
            'name' => '_wc_cog_cost',
            'label' => esc_html__('WC Cost of goods', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'numeric',
            'allowed_type' => ['simple', 'composite', 'variation', 'variable'],
            'field_type' => 'woocommerce_cost_of_goods',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_woo_multi_currency_fields()
    {
        $fields = [];

        $woo_multi_currency_params = get_option('woo_multi_currency_params');
        if (!empty($woo_multi_currency_params) && isset($woo_multi_currency_params['enable_fixed_price']) && intval($woo_multi_currency_params['enable_fixed_price']) === 1) {
            // delete default currency
            if (!empty($woo_multi_currency_params['currency'][0])) {
                unset($woo_multi_currency_params['currency'][0]);
            }

            // get active currencies
            if (!empty($woo_multi_currency_params['currency'])) {
                if (!empty($woo_multi_currency_params['currency']) && is_array($woo_multi_currency_params['currency'])) {
                    foreach ($woo_multi_currency_params['currency'] as $currency) {
                        $fields['_regular_price_wmcp_-_' . $currency] = [
                            'name' => '_regular_price_wmcp',
                            'sub_name' => $currency,
                            'label' => 'Regular price (' . $currency . ')',
                            'editable' => true,
                            'content_type' => 'numeric',
                            'allowed_type' => ['simple', 'composite', 'variation', 'variable'],
                            'field_type' => 'woo_multi_currency',
                            'update_type' => 'meta_field',
                            'fetch_type' => 'meta_field'
                        ];
                        $fields['_sale_price_wmcp_-_' . $currency] = [
                            'name' => '_sale_price_wmcp',
                            'sub_name' => $currency,
                            'label' => 'Sale price (' . $currency . ')',
                            'editable' => true,
                            'content_type' => 'numeric',
                            'allowed_type' => ['simple', 'composite', 'variation', 'variable'],
                            'field_type' => 'woo_multi_currency',
                            'update_type' => 'meta_field',
                            'fetch_type' => 'meta_field'
                        ];
                    }
                }
            }
        }

        return $fields;
    }

    private function get_yith_badge_management_premium_fields()
    {
        $fields = [];

        $fields['_yith_wcbm_product_meta_-_id_badge'] = [
            'name' => '_yith_wcbm_product_meta',
            'sub_name' => 'id_badge',
            'label' => esc_html__('Product badge', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'yith_product_badge',
            'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variable'],
            'field_type' => 'yith_badge_management',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_yith_wcbm_product_meta_-_start_date'] = [
            'name' => '_yith_wcbm_product_meta',
            'sub_name' => 'start_date',
            'label' => esc_html__('Starting date', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'date',
            'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variable'],
            'field_type' => 'yith_badge_management',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];
        $fields['_yith_wcbm_product_meta_-_end_date'] = [
            'name' => '_yith_wcbm_product_meta',
            'sub_name' => 'end_date',
            'label' => esc_html__('Ending date', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'date',
            'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variable'],
            'field_type' => 'yith_badge_management',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_yith_badge_management_free_fields()
    {
        $fields = [];

        $fields['_yith_wcbm_product_meta_-_id_badge'] = [
            'name' => '_yith_wcbm_product_meta',
            'sub_name' => 'id_badge',
            'label' => esc_html__('Product badge', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'yith_product_badge',
            'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variable'],
            'field_type' => 'yith_badge_management',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_wc_advanced_product_labels_fields()
    {
        $fields = [];

        $fields['ithemeland_badge'] = [
            'name' => 'ithemeland_badge',
            'label' => esc_html__('iThemeland badge', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'ithemeland_badge',
            'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variable'],
            'field_type' => 'ithemeland_badge',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_yikes_custom_product_tabs_fields()
    {
        $fields = [];

        $fields['yikes_custom_product_tabs'] = [
            'name' => 'yikes_custom_product_tabs',
            'label' => esc_html__('Custom Tabs', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'yikes_custom_product_tabs',
            'allowed_type' => ['simple', 'composite', 'grouped', 'external', 'variable'],
            'field_type' => 'yikes_custom_product_tabs',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }

    private function get_it_wc_dynamic_pricing_fields()
    {
        $fields = [];

        $fields['it_product_disable_discount'] = [
            'name' => 'it_product_disable_discount',
            'label' => esc_html__('Disable Discount', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variation'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        $fields['pricing_rules_product'] = [
            'name' => 'pricing_rules_product',
            'label' => esc_html__('Price For Each Role', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'it_pricing_rules_product',
            'allowed_type' => ['simple', 'composite', 'variation'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        $fields['it_product_hide_price_unregistered'] = [
            'name' => 'it_product_hide_price_unregistered',
            'label' => esc_html__('Hide price (unregistered)', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'checkbox_dual_mode',
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        $fields['it_pricing_product_price_user_role'] = [
            'name' => 'it_pricing_product_price_user_role',
            'label' => esc_html__('Hide Price', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'it_wc_dynamic_pricing_select_roles',
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        $fields['it_pricing_product_add_to_cart_user_role'] = [
            'name' => 'it_pricing_product_add_to_cart_user_role',
            'label' => esc_html__('Hide Add to cart', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'it_wc_dynamic_pricing_select_roles',
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        $fields['it_pricing_product_hide_user_role'] = [
            'name' => 'it_pricing_product_hide_user_role',
            'label' => esc_html__('Hide Product', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'it_wc_dynamic_pricing_select_roles',
            'allowed_type' => ['simple', 'composite', 'variable'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        $fields['it_wc_dynamic_pricing_all_fields'] = [
            'name' => 'it_wc_dynamic_pricing_all_fields',
            'label' => esc_html__('All Fields', 'ithemeland-woo-bulk-product-editor-lite'),
            'editable' => true,
            'content_type' => 'it_wc_dynamic_pricing_all_fields',
            'allowed_type' => ['simple', 'composite', 'variable', 'variation'],
            'field_type' => 'it_wc_dynamic_pricing',
            'update_type' => 'meta_field',
            'fetch_type' => 'meta_field'
        ];

        return $fields;
    }
}
