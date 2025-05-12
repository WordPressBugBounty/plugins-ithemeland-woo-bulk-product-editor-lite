<?php

namespace wcbel\classes\services\filter;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Product_Helper;
use wcbel\classes\repositories\Product;
use wcbel\classes\repositories\Setting;

class Product_Filter_Service
{
    private static $instance;

    private $field_methods;
    private $query_args;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->field_methods = $this->get_field_methods();
    }

    public function get_filtered_products($data, $args)
    {
        $this->create_query($data, $args);
        $this->query_args = apply_filters('wcbe_filter_query_args', $this->query_args, $data);
        $product_repository = Product::get_instance();
        if (is_string($this->query_args['post_type']) && $this->query_args['post_type'] == 'product_variation' && !empty($this->query_args['post_parent'])) {
            $variation_args = $this->get_query_for_variations();
            $variations = $product_repository->get_products($variation_args);
            $variation_products = $variations->posts;

            return [
                'max_num_pages' => !empty($variations->max_num_pages) ? $variations->max_num_pages : 0,
                'found_posts' => intval($variations->found_posts),
                'show_variations' => (isset($data['show_variations'])) ? sanitize_text_field($data['show_variations']) : 'no',
                'variation_ids' => (!empty($variation_products)) ? $variation_products : [],
            ];
        }

        $parent_args = $this->get_query_for_parents();
        $parents = $product_repository->get_products($parent_args);
        $parent_products = $parents->posts;

        if (
            isset($data['show_variations'])
            && $data['show_variations'] == 'yes'
            && (
                (is_array($this->query_args['post_type']) && in_array('product_variation', $this->query_args['post_type']))
                ||
                (is_string($this->query_args['post_type']) && $this->query_args['post_type'] == 'product_variation'))
        ) {
            $variation_args = $this->get_query_for_variations();
            if (!empty($parent_products)) {
                $variation_args['post_parent__in'] = $parent_products;
            }
            $variations = $product_repository->get_products($variation_args);
            $variation_products = $variations->posts;
        }

        return [
            'max_num_pages' => !empty($parents->max_num_pages) ? $parents->max_num_pages : 0,
            'found_posts' => (!empty($variations)) ? intval($parents->found_posts) + intval($variations->found_posts) : intval($parents->found_posts),
            'show_variations' => (isset($data['show_variations'])) ? sanitize_text_field($data['show_variations']) : 'no',
            'product_ids' => (!empty($parent_products)) ? $parent_products : [],
            'variation_ids' => (!empty($variation_products)) ? $variation_products : [],
        ];
    }

    private function create_query($data, $args)
    {
        $this->query_args = $args;
        $this->set_required_args();

        if (is_array($data) && !empty($data)) {
            if (isset($data['search_type']) && $data['search_type'] == 'quick_search') {
                if (!empty($data['quick_search_text'])) {
                    switch ($data['quick_search_field']) {
                        case 'title':
                            $this->query_args['wcbe_general_column_filter'][] = [
                                'field' => 'post_title',
                                'value' => $data['quick_search_text'],
                                'operator' => $data['quick_search_operator']
                            ];
                            break;
                        case 'sku':
                            $this->query_args['meta_query'][] = $this->get_meta_query('_sku', $data['quick_search_text'], $data['quick_search_operator']);
                            break;
                        case 'id':
                            $ids = Product_Helper::products_id_parser($data['quick_search_text']);
                            $this->query_args['wcbe_general_column_filter'][] = [
                                'field' => 'ID',
                                'value' => $ids,
                                'operator' => "in"
                            ];
                            break;
                    }
                }
            } else {
                if (empty($data['fields'])) {
                    return;
                }

                foreach ($data['fields'] as $item) {
                    if (!isset($this->field_methods[$item['filter_type']]) || !method_exists($this, $this->field_methods[$item['filter_type']])) {
                        continue;
                    }

                    $method = $this->field_methods[$item['filter_type']];
                    $this->{$method}($item);
                }
            }
        }
    }

    private function get_query_for_parents()
    {
        $query_args = $this->query_args;
        $query_args['post_type'] = ['product'];

        if (!empty($query_args['simple_product_attributes']) && is_array($query_args['simple_product_attributes'])) {
            if (!isset($query_args['tax_query']) || !is_array($query_args['tax_query'])) {
                $query_args['tax_query'] = []; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query 
            }

            foreach ($query_args['simple_product_attributes'] as $item) {
                $query_args['tax_query'][] = $item;
            }
            unset($query_args['simple_product_attributes']);
        }

        if (!empty($query_args['variation_product_attributes'])) {
            unset($query_args['variation_product_attributes']);
        }

        return $query_args;
    }

    private function get_query_for_variations()
    {
        $query_args = $this->query_args;
        $query_args['post_type'] = ['product_variation'];

        if (!empty($query_args['variation_product_attributes']) && is_array($query_args['variation_product_attributes'])) {
            if (!isset($query_args['meta_query']) || !is_array($query_args['meta_query'])) {
                $query_args['meta_query'] = []; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query 
            }

            foreach ($query_args['variation_product_attributes'] as $item) {
                $query_args['meta_query'][] = $item;
            }

            $query_args['posts_per_page'] = -1;
            unset($query_args['variation_product_attributes']);
        }

        if (!empty($query_args['simple_product_attributes'])) {
            unset($query_args['simple_product_attributes']);
        }

        if (!empty($query_args['tax_query'])) {
            unset($query_args['tax_query']);
        }

        return $query_args;
    }

    private function set_required_args()
    {
        $settings_repository = Setting::get_instance();
        $settings = $settings_repository->get_settings();
        $column_name = (isset($settings['default_sort_by'])) ? $settings['default_sort_by'] : '';
        $sort_type = (isset($settings['default_sort'])) ? $settings['default_sort'] : '';
        if (!isset($this->query_args['post_type'])) {
            $this->query_args['post_type'] = ['product', 'product_variation'];
        }
        if (!isset($this->query_args['fields'])) {
            $this->query_args['fields'] = 'ids';
        }

        if (!isset($this->query_args['orderby'])) {
            $this->set_orderby($column_name);
        }

        if (!isset($this->query_args['order'])) {
            $this->query_args['order'] = $sort_type;
        }

        if (!isset($this->query_args['posts_per_page'])) {
            $this->query_args['posts_per_page'] = ($settings['count_per_page'] > Setting::MAX_COUNT_PER_PAGE) ? Setting::MAX_COUNT_PER_PAGE : intval($settings['count_per_page']);
        }

        if ($this->query_args['posts_per_page'] > 1) {
            if (!isset($this->query_args['paginate'])) {
                $this->query_args['paginate'] = true;
            }

            if (!isset($this->query_args['paged'])) {
                $this->query_args['paged'] = 1;
            }
        }
    }

    private function set_orderby($orderby)
    {
        switch ($orderby) {
            case 'id':
                $this->query_args['orderby'] = 'ID';
                break;
            case 'title':
                $this->query_args['orderby'] = 'post_title';
                break;
            case 'post_date':
                $this->query_args['orderby'] = 'post_date';
                break;
            case 'regular_price':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'sale_price':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'sku':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_sku'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'manage_stock':
                $this->query_args['orderby'] = 'meta_value';
                $this->query_args['meta_key'] = '_manage_stock'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'stock_quantity':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_stock'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'stock_status':
                $this->query_args['orderby'] = 'meta_value';
                $this->query_args['meta_key'] = '_stock_status'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'width':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_width'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'height':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_height'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'length':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_length'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'weight':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_weight'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'review_count':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_wc_review_count'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'average_rating':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_wc_average_rating'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'date_on_sale_from':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_sale_price_dates_from'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
            case 'date_on_sale_to':
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['meta_key'] = '_sale_price_dates_to'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
                break;
        }
    }

    private function get_field_methods()
    {
        return [
            'product_ids' => 'product_ids_filter',
            'product_title' => 'product_title_filter',
            'product_content' => 'product_content_filter',
            'product_excerpt' => 'product_excerpt_filter',
            'product_slug' => 'product_slug_filter',
            'product_sku' => 'product_sku_filter',
            'product_url' => 'product_url_filter',
            'date_created' => 'date_created_filter',
            'sale_price_date_from' => 'sale_price_date_from_filter',
            'sale_price_date_to' => 'sale_price_date_to_filter',
            'taxonomy' => 'product_taxonomies_filter',
            'attribute' => 'product_attributes_filter',
            'regular_price' => 'product_regular_price_filter',
            'sale_price' => 'product_sale_price_filter',
            'shipping_class' => 'shipping_class_filter',
            'width' => 'product_width_filter',
            'height' => 'product_height_filter',
            'length' => 'product_length_filter',
            'weight' => 'product_weight_filter',
            'stock_quantity' => 'stock_quantity_filter',
            'low_stock_amount' => 'low_stock_amount_filter',
            'manage_stock' => 'manage_stock_filter',
            'menu_order' => 'product_menu_order_filter',
            'product_type' => 'product_type_filter',
            'product_status' => 'product_status_filter',
            'stock_status' => 'stock_status_filter',
            'featured' => 'featured_filter',
            'downloadable' => 'downloadable_filter',
            'backorders' => 'backorders_filter',
            'sold_individually' => 'sold_individually_filter',
            'author' => 'author_filter',
            'catalog_visibility' => 'catalog_visibility_filter',
            'minimum_allowed_quantity' => 'minimum_allowed_quantity_filter',
            'maximum_allowed_quantity' => 'maximum_allowed_quantity_filter',
            'group_of_quantity' => 'group_of_quantity_filter',
            'minmax_do_not_count' => 'minmax_do_not_count_filter',
            'minmax_cart_exclude' => 'minmax_cart_exclude_filter',
            'minmax_category_group_of_exclude' => 'minmax_category_group_of_exclude_filter',
            '_ywmmq_product_minimum_quantity' => 'ywmmq_product_minimum_quantity_filter',
            '_ywmmq_product_maximum_quantity' => 'ywmmq_product_maximum_quantity_filter',
            '_ywmmq_product_step_quantity' => 'ywmmq_product_step_quantity_filter',
            '_ywmmq_product_exclusion' => 'ywmmq_product_exclusion_filter',
            '_ywmmq_product_quantity_limit_override' => 'ywmmq_product_quantity_limit_override_filter',
            '_ywmmq_product_quantity_limit_variations_override' => 'ywmmq_product_quantity_limit_variations_override_filter',
            '_product_commission' => 'product_commission_filter',
            'yith_shop_vendor' => 'yith_shop_vendor_filter',
            '_wcpv_product_commission' => 'wcpv_product_commission_filter',
            '_wcpv_product_taxes' => 'wcpv_product_taxes_filter',
            '_wcpv_product_pass_shipping' => 'wcpv_product_pass_shipping_filter',
            'wcpv_product_vendors' => 'wcpv_product_vendors_filter',
            'yith_cog_cost' => 'yith_cog_cost_filter',
            '_wc_cog_cost' => 'wc_cog_cost_filter',
            '_regular_price_wmcp' => 'regular_price_wmcp_filter',
            '_sale_price_wmcp' => 'sale_price_wmcp_filter',
            '_yith_wcbm_product_meta_-_id_badge' => 'yith_wcbm_product_meta_badge_filter',
            '_yith_wcbm_product_meta_-_start_date' => 'yith_wcbm_product_meta_start_date_filter',
            '_yith_wcbm_product_meta_-_end_date' => 'yith_wcbm_product_meta_end_date_filter',
            'custom_field' => 'product_custom_fields_filter',
        ];
    }

    private function product_ids_filter($item)
    {
        $ids = Product_Helper::products_id_parser($item['value']);
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'ID',
            'value' => $ids,
            'parent_only' => (isset($item['parent_only']) && $item['parent_only'] == 'yes') ? true : false,
            'operator' => "in"
        ];
    }

    private function product_title_filter($item)
    {
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'post_title',
            'value' => $item['value'],
            'parent_only' => false,
            'operator' => $item['operator']
        ];
    }

    private function product_content_filter($item)
    {
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'post_content',
            'value' => $item['value'],
            'operator' => $item['operator']
        ];
    }

    private function product_excerpt_filter($item)
    {
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'post_excerpt',
            'value' => $item['value'],
            'operator' => $item['operator']
        ];
    }

    private function product_slug_filter($item)
    {
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'post_name',
            'value' => urlencode($item['value']),
            'operator' => $item['operator']
        ];
    }

    private function product_sku_filter($item)
    {
        $this->query_args['meta_query'][] =  $this->get_meta_query('_sku', $item['value'], $item['operator']);
    }

    private function product_url_filter($item)
    {
        $this->query_args['meta_query'][] =  $this->get_meta_query('_product_url', $item['value'], $item['operator']);
    }

    private function date_created_filter($item)
    {
        $from = (isset($item['value']['from']) && $item['value']['from'] != '') ? gmdate('Y-m-d', strtotime($item['value']['from'])) : null;
        $to = (isset($item['value']['to']) && $item['value']['to'] != '') ? gmdate('Y-m-d', strtotime($item['value']['to'])) : null;

        if (!is_null($from) & !is_null($to)) {
            $value = [$from, $to];
            $operator = 'between';
        } else if (!is_null($from)) {
            $value = $from;
            $operator = '>=';
        } else {
            $value = $to;
            $operator = '<=';
        }

        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'post_date',
            'value' => $value,
            'operator' => $operator,
        ];
    }

    private function sale_price_date_from_filter($item)
    {
        $this->query_args['meta_query'][] = $this->get_meta_query('_sale_price_dates_from', strtotime($item['value']), '>=');
    }

    private function sale_price_date_to_filter($item)
    {
        $this->query_args['meta_query'][] = $this->get_meta_query('_sale_price_dates_to', strtotime($item['value']), '<=');
    }

    private function product_taxonomies_filter($item)
    {
        if (!empty($item['value'])) {
            $tax_item = $this->get_tax_query($item['name'], $item['value'], $item['operator']);
            $this->query_args['tax_query'][] = $tax_item;
        }
    }

    private function product_attributes_filter($item)
    {
        if (!empty($item['value'])) {
            // for simple products
            $tax_item = $this->get_tax_query($item['name'], $item['value'], $item['operator'], 'slug');
            $this->query_args['simple_product_attributes'][] = $tax_item;

            // for variations
            $meta_item = $this->get_meta_query('attribute_' . $item['name'], $item['value'], $item['operator']);
            $this->query_args['variation_product_attributes'][] = $meta_item;
        }
    }

    private function product_regular_price_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'product_regular_price',
            'query_arg_key' => '_regular_price',
            'value' => $item['value']
        ]);
    }

    private function product_sale_price_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'product_sale_price',
            'query_arg_key' => '_sale_price',
            'value' => $item['value']
        ]);
    }

    private function shipping_class_filter($item)
    {
        $tax_item = $this->get_tax_query('product_shipping_class', $item['value']);
        $this->query_args['tax_query'][] = $tax_item;
    }

    private function product_width_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'product_width',
            'query_arg_key' => '_width',
            'value' => $item['value']
        ]);
    }

    private function product_height_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'product_height',
            'query_arg_key' => '_height',
            'value' => $item['value']
        ]);
    }

    private function product_length_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'product_length',
            'query_arg_key' => '_length',
            'value' => $item['value']
        ]);
    }

    private function product_weight_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'product_weight',
            'query_arg_key' => '_weight',
            'value' => $item['value']
        ]);
    }

    private function stock_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'stock_quantity',
            'query_arg_key' => '_stock',
            'value' => $item['value']
        ]);
    }

    private function low_stock_amount_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'low_stock_amount',
            'query_arg_key' => '_low_stock_amount',
            'value' => $item['value']
        ]);
    }

    private function manage_stock_filter($item)
    {
        $this->query_args['meta_query'][] = [
            'key' => '_manage_stock',
            'value' => $item['value'],
            'compare' => '='
        ];
    }

    private function product_menu_order_filter($item)
    {
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'menu_order',
            'value' => [floatval($item['value']['from']), floatval($item['value']['to'])],
            'operator' => 'between'
        ];
    }

    private function product_type_filter($item)
    {
        $tax_item = $this->get_tax_query('product_type', $item['value'], 'or', 'slug');
        $this->query_args['tax_query'][] = [$tax_item];
    }

    private function product_status_filter($item)
    {
        $this->query_args['post_status'] = esc_sql($item['value']);
    }

    private function stock_status_filter($item)
    {
        $this->query_args['meta_query'][] = [
            'key' => '_stock_status',
            'value' => esc_sql($item['value']),
            'compare' => '='
        ];
    }

    private function featured_filter($item)
    {
        $tax_item = $this->get_tax_query('product_visibility', 'featured', ($item == 'yes') ? 'or' : 'not_in');
        $this->query_args['tax_query'][] = [$tax_item];
    }

    private function downloadable_filter($item)
    {
        $this->query_args['meta_query'][] = [
            'key' => '_downloadable',
            'value' => esc_sql($item['value']),
            'compare' => '='
        ];
    }

    private function backorders_filter($item)
    {
        $this->query_args['meta_query'][] = [
            'key' => '_backorders',
            'value' => esc_sql($item['value']),
            'compare' => '='
        ];
    }

    private function sold_individually_filter($item)
    {
        $this->query_args['meta_query'][] = [
            'key' => '_sold_individually',
            'value' => esc_sql($item['value']),
            'compare' => '='
        ];
    }

    private function author_filter($item)
    {
        $this->query_args['wcbe_general_column_filter'][] = [
            'field' => 'post_author',
            'value' => intval($item['value']),
            'operator' => 'exact'
        ];
    }

    private function catalog_visibility_filter($item)
    {
        switch ($item) {
            case 'visible':
                $tax_item = $this->get_tax_query('product_visibility', ['exclude-from-catalog', 'exclude-from-search'], 'not_in', 'name');
                $this->query_args['tax_query'][] = [$tax_item];
                break;
            case 'catalog':
                $tax_item = $this->get_tax_query('product_visibility', ['exclude-from-search'], 'or', 'name');
                $this->query_args['tax_query'][] = [$tax_item];
                $tax_item2 = $this->get_tax_query('product_visibility', ['exclude-from-catalog'], 'not_in', 'name');
                $this->query_args['tax_query'][] = [$tax_item2];
                break;
            case 'search':
                $tax_item = $this->get_tax_query('product_visibility', ['exclude-from-catalog'], 'or', 'name');
                $this->query_args['tax_query'][] = [$tax_item];
                $tax_item2 = $this->get_tax_query('product_visibility', ['exclude-from-search'], 'not_in', 'name');
                $this->query_args['tax_query'][] = [$tax_item2];
                break;
            case 'hidden':
                $tax_item = $this->get_tax_query('product_visibility', ['exclude-from-catalog', 'exclude-from-search'], 'and', 'name');
                $this->query_args['tax_query'][] = [$tax_item];
                break;
        }
    }

    private function minimum_allowed_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'minimum_allowed_quantity',
            'query_arg_key' => 'minimum_allowed_quantity',
            'value' => $item['value']
        ]);
    }

    private function maximum_allowed_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'maximum_allowed_quantity',
            'query_arg_key' => 'maximum_allowed_quantity',
            'value' => $item['value']
        ]);
    }

    private function group_of_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => 'group_of_quantity',
            'query_arg_key' => 'group_of_quantity',
            'value' => $item['value']
        ]);
    }

    private function minmax_do_not_count_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => 'minmax_do_not_count',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'minmax_do_not_count',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => 'minmax_do_not_count',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function minmax_cart_exclude_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => 'minmax_cart_exclude',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'minmax_cart_exclude',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => 'minmax_cart_exclude',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function minmax_category_group_of_exclude_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => 'minmax_category_group_of_exclude',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'minmax_category_group_of_exclude',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => 'minmax_category_group_of_exclude',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function ywmmq_product_minimum_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => '_ywmmq_product_minimum_quantity',
            'query_arg_key' => '_ywmmq_product_minimum_quantity',
            'value' => $item['value']
        ]);
    }

    private function ywmmq_product_maximum_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => '_ywmmq_product_maximum_quantity',
            'query_arg_key' => '_ywmmq_product_maximum_quantity',
            'value' => $item['value']
        ]);
    }

    private function ywmmq_product_step_quantity_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => '_ywmmq_product_step_quantity',
            'query_arg_key' => '_ywmmq_product_step_quantity',
            'value' => $item['value']
        ]);
    }

    private function ywmmq_product_exclusion_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => '_ywmmq_product_exclusion',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => '_ywmmq_product_exclusion',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => '_ywmmq_product_exclusion',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function ywmmq_product_quantity_limit_override_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => '_ywmmq_product_quantity_limit_override',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => '_ywmmq_product_quantity_limit_override',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => '_ywmmq_product_quantity_limit_override',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function ywmmq_product_quantity_limit_variations_override_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => '_ywmmq_product_quantity_limit_variations_override',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => '_ywmmq_product_quantity_limit_variations_override',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => '_ywmmq_product_quantity_limit_variations_override',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function product_commission_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => '_product_commission',
            'query_arg_key' => '_product_commission',
            'value' => $item['value']
        ]);
    }

    private function yith_shop_vendor_filter($item)
    {
        $this->query_args['tax_query'][] = [
            'taxonomy' => 'yith_shop_vendor',
            'field' => 'slug',
            'terms' => $item['value'],
            'operator' => ($item['operator'] == 'or') ? 'IN' : 'NOT IN',
        ];
    }

    private function wcpv_product_commission_filter($item)
    {
        $this->set_from_to_meta_query([
            'filter_key' => '_wcpv_product_commission',
            'query_arg_key' => '_wcpv_product_commission',
            'value' => $item['value']
        ]);
    }

    private function wcpv_product_taxes_filter($item)
    {
        $this->query_args['meta_query'][] = [
            'key' => '_wcpv_product_taxes',
            'value' => sanitize_text_field($item['value']),
            'compare' => '='
        ];
    }

    private function wcpv_product_pass_shipping_filter($item)
    {
        if ($item['value'] == 'yes') {
            $this->query_args['meta_query'][] = [
                'key' => '_wcpv_product_pass_shipping',
                'value' => 'yes',
                'compare' => '='
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => '_wcpv_product_pass_shipping',
                    'value' => 'no',
                    'compare' => '='
                ],
                [
                    'key' => '_wcpv_product_pass_shipping',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
    }

    private function wcpv_product_vendors_filter($item)
    {
        $this->query_args['tax_query'][] = [
            'taxonomy' => 'wcpv_product_vendors',
            'field' => 'slug',
            'terms' => $item['value'],
            'operator' => ($item['operator'] == 'or') ? 'IN' : 'NOT IN',
        ];
    }

    private function yith_cog_cost_filter($item)
    {
        $from = (isset($item['value']['from']) && $item['value']['from'] != '') ? floatval($item['value']['from']) : null;
        $to = (isset($item['value']['to']) && $item['value']['to'] != '') ? floatval($item['value']['to']) : null;
        if (!is_null($from) & !is_null($to)) {
            $value = [$from, $to];
            $operator = 'BETWEEN';
        } else if (!is_null($from)) {
            $value = $from;
            $operator = '>=';
        } else {
            $value = $to;
            $operator = '<=';
        }

        $this->query_args['meta_query'][] = [
            'relation' => 'OR',
            [
                'key' => 'yith_cog_cost',
                'value' => $value,
                'type' => 'numeric',
                'compare' => $operator
            ],
            [
                'key' => 'yith_cog_cost_variable',
                'value' => $value,
                'type' => 'numeric',
                'compare' => $operator
            ]
        ];
    }

    private function wc_cog_cost_filter($item)
    {
        $from = (isset($item['value']['from']) && $item['value']['from'] != '') ? floatval($item['value']['from']) : null;
        $to = (isset($item['value']['to']) && $item['value']['to'] != '') ? floatval($item['value']['to']) : null;
        if (!is_null($from) & !is_null($to)) {
            $value = [$from, $to];
            $operator = 'BETWEEN';
        } else if (!is_null($from)) {
            $value = $from;
            $operator = '>=';
        } else {
            $value = $to;
            $operator = '<=';
        }

        $this->query_args['meta_query'][] = [
            'relation' => 'OR',
            [
                'key' => '_wc_cog_cost',
                'value' => $value,
                'type' => 'numeric',
                'compare' => $operator
            ],
            [
                'key' => '_wc_cog_cost_variable',
                'value' => $value,
                'type' => 'numeric',
                'compare' => $operator
            ]
        ];
    }

    private function regular_price_wmcp_filter($item)
    {
        foreach ($item as $regular_item) {
            if (!empty($regular_item['name']) && ((isset($regular_item['from']) && $regular_item['from'] != '') || (isset($regular_item['to']) && $regular_item['to'] != ''))) {
                $field_name_arr = explode('_-_', $regular_item['name']);
                if (!empty($field_name_arr[1])) {
                    $from = (!empty($regular_item['from'])) ? floatval($regular_item['from']) : null;
                    $to = (!empty($regular_item['to'])) ? floatval($regular_item['to']) : null;

                    if (!empty($from) & !empty($to)) {
                        $value = [$from, $to];
                        $operator = 'json_between';
                    } else if (!empty($from)) {
                        $value = $from;
                        $operator = 'json_>=';
                    } else {
                        $value = $to;
                        $operator = 'json_<=';
                    }

                    $this->query_args['wcbe_meta_filter'][] = [
                        'key' => '_regular_price_wmcp',
                        'json_key' => $field_name_arr[1],
                        'value' => $value,
                        'operator' => $operator
                    ];
                }
            }
        }
    }

    private function sale_price_wmcp_filter($item)
    {
        foreach ($item as $regular_item) {
            if (!empty($regular_item['name']) && ((isset($regular_item['from']) && $regular_item['from'] != '') || (isset($regular_item['to']) && $regular_item['to'] != ''))) {
                $field_name_arr = explode('_-_', $regular_item['name']);
                if (!empty($field_name_arr[1])) {
                    $from = (!empty($regular_item['from'])) ? floatval($regular_item['from']) : null;
                    $to = (!empty($regular_item['to'])) ? floatval($regular_item['to']) : null;

                    if (!empty($from) & !empty($to)) {
                        $value = [$from, $to];
                        $operator = 'json_between';
                    } else if (!empty($from)) {
                        $value = $from;
                        $operator = 'json_>=';
                    } else {
                        $value = $to;
                        $operator = 'json_<=';
                    }

                    $this->query_args['wcbe_meta_filter'][] = [
                        'key' => '_sale_price_wmcp',
                        'json_key' => $field_name_arr[1],
                        'value' => $value,
                        'operator' => $operator
                    ];
                }
            }
        }
    }

    private function yith_wcbm_product_meta_badge_filter($item)
    {
        switch ($item['operator']) {
            case 'or':
                $operator = 'like';
                break;
            case 'and':
                $operator = 'like_and';
                break;
            case 'not_in':
                $operator = 'not_like';
                break;
            default:
                $operator = 'like';
        }
        $this->query_args['wcbe_meta_filter'][] = [
            'key' => '_yith_wcbm_product_meta',
            'item_key' => 'id_badge',
            'value' => $item['value'],
            'before_str' => ':"',
            'after_str' => '";',
            'operator' => $operator,
        ];
    }

    private function yith_wcbm_product_meta_start_date_filter($item)
    {
        $from = (isset($item['value']['from']) && $item['value']['from'] != '') ? sanitize_text_field($item['value']['from']) : null;
        $to = (isset($item['value']['to']) && $item['value']['to'] != '') ? sanitize_text_field($item['value']['to']) : null;
        if (!is_null($from) & !is_null($to)) {
            $value = [$from, $to];
            $operator = 'serialized_date_between';
        } else if (!is_null($from)) {
            $value = $from;
            $operator = 'serialized_date_>=';
        } else {
            $value = $to;
            $operator = 'serialized_date_<=';
        }
        $this->query_args['wcbe_meta_filter'][] = [
            'key' => '_yith_wcbm_product_meta',
            'item_key' => 'start_date',
            'value' => $value,
            'operator' => $operator
        ];
    }

    private function yith_wcbm_product_meta_end_date_filter($item)
    {
        $from = (isset($item['value']['from']) && $item['value']['from'] != '') ? sanitize_text_field($item['value']['from']) : null;
        $to = (isset($item['value']['to']) && $item['value']['to'] != '') ? sanitize_text_field($item['value']['to']) : null;
        if (!is_null($from) & !is_null($to)) {
            $value = [$from, $to];
            $operator = 'serialized_date_between';
        } else if (!is_null($from)) {
            $value = $from;
            $operator = 'serialized_date_>=';
        } else {
            $value = $to;
            $operator = 'serialized_date_<=';
        }

        $this->query_args['wcbe_meta_filter'][] = [
            'key' => '_yith_wcbm_product_meta',
            'item_key' => 'end_date',
            'value' => $value,
            'operator' => $operator
        ];
    }

    private function product_custom_fields_filter($item)
    {
        switch ($item['field_type']) {
            case 'date':
                $from = (!empty($item['value']['from'])) ? gmdate('Y/m/d', strtotime($item['value']['from'])) : null;
                $to = (!empty($item['value']['to'])) ? gmdate('Y/m/d', strtotime($item['value']['to'])) : null;

                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'BETWEEN';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>=';
                } else {
                    $value = $to;
                    $operator = '<=';
                }

                if (!empty($value)) {
                    $this->query_args['meta_query'][] = [
                        'key' => $item['name'],
                        'value' => $value,
                        'compare' => $operator,
                        'type' => 'DATE'
                    ];
                }
                break;
            case 'time':
                $from = (!empty($item['value']['from'])) ? gmdate('H:i', strtotime($item['value']['from'])) : null;
                $to = (!empty($item['value']['to'])) ? gmdate('H:i', strtotime($item['value']['to'])) : null;

                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'BETWEEN';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>=';
                } else {
                    $value = $to;
                    $operator = '<=';
                }

                if (!empty($value)) {
                    $this->query_args['meta_query'][] = [
                        'key' => $item['name'],
                        'value' => $value,
                        'compare' => $operator,
                        'type' => 'TIME'
                    ];
                }
                break;
            case 'number':
                $from = (!empty($item['value']['from'])) ? floatval($item['value']['from']) : null;
                $to = (!empty($item['value']['to'])) ? floatval($item['value']['to']) : null;
                if (!is_null($from) & !is_null($to)) {
                    $this->query_args['meta_query'][] = [
                        'relation' => 'AND',
                        [
                            'key' => $item['name'],
                            'value' => $from,
                            'compare' => '>=',
                            'type' => 'DECIMAL'
                        ],
                        [
                            'key' => $item['name'],
                            'value' => $to,
                            'compare' => '<=',
                            'type' => 'DECIMAL'
                        ]
                    ];
                } else if (!is_null($from)) {
                    $this->query_args['meta_query'][] = [
                        'key' => $item['name'],
                        'value' => $from,
                        'compare' => '>=',
                        'type' => 'DECIMAL'
                    ];
                } else {
                    $this->query_args['meta_query'][] = [
                        'key' => $item['name'],
                        'value' => $to,
                        'compare' => '<=',
                        'type' => 'DECIMAL'
                    ];
                }
                break;
            case 'text':
            case 'email':
            case 'textarea':
            case 'string':
            case 'textinput':
            case 'password':
            case 'url':
                if (!empty($item['value'])) {
                    $this->query_args['meta_query'][] = [
                        'key' => $item['name'],
                        'value' => $item['value'],
                        'compare' => isset($item['operator']) ? $item['operator'] : '=',
                    ];
                }
                break;
            case 'checkbox':
                if (!empty($item['value'])) {
                    switch ($item['value']) {
                        case 'yes':
                            $this->query_args['meta_query'][] = [
                                'key' => $item['name'],
                                'value' => $item['value'],
                                'compare' => '=',
                            ];
                            break;
                        case 'no':
                            $this->query_args['meta_query'][] = [
                                'relation' => 'OR',
                                [
                                    'key' => $item['name'],
                                    'value' => $item['value'],
                                    'compare' => '=',
                                ],
                                [
                                    'key' => $item['name'],
                                    'compare' => 'NOT EXISTS',
                                ]
                            ];
                            break;
                    }
                }
                break;
            case 'select':
                if (!empty($item['value'])) {
                    $this->query_args['meta_query'][] = [
                        'key' => $item['name'],
                        'value' => $item['value'],
                        'compare' => '=',
                    ];
                }
                break;
        }
    }

    private function get_tax_query($taxonomy, $terms, $operator = null, $field = null)
    {
        $field = !empty($field) ? $field : 'term_id';
        $taxonomy = urldecode($taxonomy);
        $terms = (is_array($terms)) ? array_map('urldecode', $terms) : urldecode($terms);

        switch ($operator) {
            case null:
                $tax_item = [
                    'taxonomy' => $taxonomy,
                    'field' => $field,
                    'terms' => $terms,
                    'operator' => 'AND'
                ];
                break;
            case 'or':
                $tax_item = [
                    'taxonomy' => $taxonomy,
                    'field' => $field,
                    'terms' => $terms,
                    'operator' => 'IN'
                ];
                break;
            case 'and':
                $tax_item['relation'] = 'AND';
                if (is_array($terms) && !empty($terms)) {
                    foreach ($terms as $value) {
                        $tax_item[] = [
                            'taxonomy' => $taxonomy,
                            'field' => $field,
                            'terms' => [$value],
                        ];
                    }
                }
                break;
            case 'not_in':
                $tax_item = [
                    'taxonomy' => $taxonomy,
                    'field' => $field,
                    'terms' => $terms,
                    'operator' => 'NOT IN'
                ];
                break;
        }
        return $tax_item;
    }

    private function get_meta_query($meta_key, $value, $operator = null)
    {
        $meta_key = urldecode($meta_key);
        $value = (is_array($value)) ? array_map('urldecode', $value) : urldecode($value);

        switch ($operator) {
            case null:
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => 'AND'
                ];
                break;
            case 'or':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => 'IN'
                ];
                break;
            case 'and':
                $meta_query['relation'] = 'AND';
                if (is_array($value) && !empty($value)) {
                    foreach ($value as $value_item) {
                        $meta_query[] = [
                            'key' => $meta_key,
                            'value' => [$value_item],
                        ];
                    }
                }
                break;
            case 'not_in':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => 'NOT IN'
                ];
                break;
            case 'like':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => 'LIKE'
                ];
                break;
            case 'exact':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => '='
                ];
                break;
            case 'not':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => '!='
                ];
                break;
            case 'begin':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => '^' . $value,
                    'compare' => 'RLIKE'
                ];
                break;
            case 'end':
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value . '$',
                    'compare' => 'RLIKE'
                ];
                break;
            default:
                $meta_query = [
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => $operator
                ];
                break;
        }
        return $meta_query;
    }

    private function set_from_to_meta_query($data)
    {
        $from = (isset($data['value']['from']) && $data['value']['from'] != '') ? floatval($data['value']['from']) : null;
        $to = (isset($data['value']['to']) && $data['value']['to'] != '') ? floatval($data['value']['to']) : null;

        if (!is_null($from) & !is_null($to)) {
            $this->query_args['meta_query'][] = [
                'relation' => 'AND',
                [
                    'key' => $data['query_arg_key'],
                    'value' => $from,
                    'compare' => '>=',
                    'type' => 'DECIMAL'
                ],
                [
                    'key' => $data['query_arg_key'],
                    'value' => $to,
                    'compare' => '<=',
                    'type' => 'DECIMAL'
                ]
            ];
        } else if (!is_null($from)) {
            $this->query_args['meta_query'][] = [
                'key' => $data['query_arg_key'],
                'value' => $from,
                'compare' => '>=',
                'type' => 'DECIMAL'
            ];
        } else {
            $this->query_args['meta_query'][] = [
                'key' => $data['query_arg_key'],
                'value' => $to,
                'compare' => '<=',
                'type' => 'DECIMAL'
            ];
        }
    }
}
