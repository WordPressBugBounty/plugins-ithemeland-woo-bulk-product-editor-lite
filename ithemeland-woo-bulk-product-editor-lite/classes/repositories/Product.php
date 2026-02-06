<?php

namespace wcbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Others;
use wcbel\classes\helpers\Meta_Fields;
use wcbel\classes\helpers\Product_Helper;

class Product
{
    const VARIATIONS_PER_PAGE = 5;

    private static $instance;

    private $wpdb;
    private $fetch_methods;
    private $shipping_classes;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function get_product($product_id)
    {
        return wc_get_product(intval($product_id));
    }

    public function get_product_statuses()
    {
        $statuses = get_post_statuses();
        $statuses['trash'] = esc_html__('Trash', 'ithemeland-woo-bulk-product-editor-lite');
        return $statuses;
    }

    public function get_product_statuses_by_id($product_ids)
    {
        $statuses = [];
        if (!empty($product_ids) && is_array($product_ids)) {
            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                if ($product instanceof \WC_Product) {
                    $statuses[$product->get_id()] = $product->get_status();
                }
            }
        }

        return $statuses;
    }

    public function get_product_ids_by_custom_query($join, $where, $types_in = 'all')
    {
        $allowed_types = [
            'all' => ['product', 'product_variation'],
            'product' => ['product'],
            'product_variation' => ['product_variation'],
        ];

        $types = isset($allowed_types[$types_in]) ? $allowed_types[$types_in] : ['product'];
        $placeholders = implode(',', array_fill(0, count($types), '%s'));
        $where_clause = (!empty($where)) ? " AND ({$where})" : '';
        $query = $this->wpdb->prepare("SELECT posts.ID, posts.post_parent FROM {$this->wpdb->posts} AS posts {$join} WHERE posts.post_type IN ($placeholders) {$where_clause}", ...$types); //phpcs:ignore

        $products = $this->wpdb->get_results($query, ARRAY_N); //phpcs:ignore
        $products = array_unique(Others::array_flatten($products, 'int'));

        if (($key = array_search(0, $products)) !== false) {
            unset($products[$key]);
        }

        return implode(',', $products);
    }

    public function get_products($args = [])
    {
        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();

        if (!isset($args['post_type'])) {
            $args['post_type'] = ['product'];
        }

        if (!isset($args['posts_per_page'])) {
            $args['posts_per_page'] = -1;
        }

        if (!isset($args['orderby'])) {
            $args['orderby'] = ($settings['default_sort_by'] == 'id') ? 'ID' : $settings['default_sort_by'];
        }

        if (!isset($args['order'])) {
            $args['order'] = $settings['default_sort'];
        }

        $posts = new \WP_Query($args);
        return $posts;
    }

    public function get_product_variation_ids($product_id)
    {
        if (empty($product_id)) {
            return [];
        }

        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();

        $query = new \WP_Query([
            'post_type' => 'product_variation',
            'post_parent' => intval($product_id),
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => -1,
            'orderby' => ($settings['default_sort_by'] == 'id') ? 'ID' : $settings['default_sort_by'],
            'order' => $settings['default_sort'],
            'fields' => 'ids'
        ]);

        return $query->posts;
    }

    public function get_product_object_by_ids($args)
    {
        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();

        if (!isset($args['limit'])) {
            $args['limit'] = -1;
        }
        if (!isset($args['type'])) {
            $args['type'] = array_merge(array_keys(wc_get_product_types()), ['variation']);
        }
        if (!isset($args['orderby'])) {
            $args['orderby'] = ($settings['default_sort_by'] == 'id') ? 'ID' : $settings['default_sort_by'];
            if (!isset($args['order'])) {
                $args['order'] = $settings['default_sort'];
            }
        }

        return wc_get_products($args);
    }

    public function delete_duplicates($wpdb, $post_type, $delete_type, $post_filter)
    {
        $criteria_map = [
            'dupoldest_title'          => ['column' => 'post_title', 'order' => 'DESC'],
            'duplatest_title'          => ['column' => 'post_title', 'order' => 'ASC'],
            'dupoldest_content'        => ['column' => 'post_content', 'order' => 'DESC'],
            'duplatest_content'        => ['column' => 'post_content', 'order' => 'ASC'],
            'dupoldest_title_content'  => ['column' => ['post_title', 'post_content'], 'order' => 'DESC'],
            'duplatest_title_content'  => ['column' => ['post_title', 'post_content'], 'order' => 'ASC'],
        ];

        if (!isset($criteria_map[$delete_type])) {
            return []; // Invalid type, return empty array
        }

        $criteria = $criteria_map[$delete_type];
        $column = $criteria['column'];
        $order = $criteria['order'];

        // Handle single-column and multi-column cases
        if (is_array($column)) {
            $group_by = implode(", ", $column);
            $select_columns = implode(", ", $column);
            $where_clause = implode(" AND ", array_map(fn($col) => "$col = %s", $column));
            $prepare_params = $column;
        } else {
            $group_by = $column;
            $select_columns = $column;
            $where_clause = "$column = %s";
            $prepare_params = [$column];
        }

        // Find duplicate records
        $query = "
        SELECT $select_columns FROM {$wpdb->posts} 
        WHERE post_type = %s AND post_status IN ('publish', 'draft', 'pending') $post_filter
        GROUP BY $group_by HAVING COUNT(*) > 1";
        $duplicates = $wpdb->get_results($wpdb->prepare($query, $post_type), ARRAY_A); //phpcs:ignore

        $product_ids = [];

        foreach ($duplicates as $dup) {
            $query = "
            SELECT ID FROM {$wpdb->posts} 
            WHERE $where_clause AND post_type = %s $post_filter 
            ORDER BY post_date $order";

            $params = array_map(fn($col) => $dup[$col], $prepare_params);
            $params[] = $post_type;

            $posts = $wpdb->get_results($wpdb->prepare($query, ...$params), ARRAY_A);  //phpcs:ignore
            array_shift($posts); // Keep the first one
            $product_ids = array_merge($product_ids, array_column($posts, 'ID'));
        }

        return $product_ids;
    }

    public function product_attribute_update($product_id, $data)
    {
        if (is_array($data) && !empty($data)) {
            $product = $this->get_product($product_id);
            if (!($product instanceof \WC_Product)) {
                return false;
            }

            $attr = new \WC_Product_Attribute();
            $attributes_result = $product->get_attributes();
            $product_attributes = (!empty($attributes_result) ? $attributes_result : []);
            $attribute_taxonomies = wc_get_attribute_taxonomies();
            $data['value'] = (is_array($data['value'])) ? array_map('intval', $data['value']) : [];
            if (is_array($attribute_taxonomies) && !empty($attribute_taxonomies)) {
                foreach ($attribute_taxonomies as $attribute_taxonomy) {
                    if (!empty($product_attributes) && isset($product_attributes[strtolower(urlencode($data['field']))])) {
                        $old_attr = $product_attributes[strtolower(urlencode($data['field']))];
                        if ($old_attr->get_name() == $data['field']) {
                            $value = Product_Helper::apply_operator($old_attr->get_options(), $data);
                            $attr->set_id($old_attr->get_id());
                            $attr->set_name($old_attr->get_name());
                            $attr->set_options($value);
                            $attr->set_position($old_attr->get_position());

                            if (isset($data['attribute_is_visible']) && $data['attribute_is_visible'] != '') {
                                $attr->set_visible($data['attribute_is_visible'] == 'yes');
                            } else {
                                $attr->set_visible($old_attr->get_visible());
                            }

                            if (isset($data['used_for_variations']) && $data['used_for_variations'] != '') {
                                $attr->set_variation($data['used_for_variations'] == 'yes');
                            } else {
                                $attr->set_variation($old_attr->get_variation());
                            }

                            $product_attributes[] = $attr;
                        }
                    } else {
                        if ('pa_' . $attribute_taxonomy->attribute_name == $data['field']) {
                            $attr->set_id($attribute_taxonomy->attribute_id);
                            $attr->set_name('pa_' . $attribute_taxonomy->attribute_name);
                            $attr->set_options($data['value']);
                            $attr->set_position(count($product_attributes));
                            $attr->set_visible(1);
                            if (isset($data['used_for_variations'])) {
                                if ($data['used_for_variations'] == 'yes') {
                                    $attr->set_variation(true);
                                } else {
                                    $attr->set_variation(false);
                                }
                            }
                            $product_attributes[] = $attr;
                        }
                    }
                }
            }

            $product->set_attributes($product_attributes);
            $product->save();
            return true;
        }
        return false;
    }

    public function get_attributes()
    {
        return wc_get_attribute_taxonomies();
    }

    public function get_taxonomies()
    {
        $output = [];
        $taxonomies = get_object_taxonomies('product', 'objects');
        $default_taxonomies = Meta_Fields::get_default_taxonomies();
        foreach ($taxonomies as $taxonomy) {
            if (taxonomy_exists($taxonomy->name) && !in_array($taxonomy->name, $default_taxonomies)) {
                $output[$taxonomy->name] = [
                    'label' => $taxonomy->labels->singular_name,
                ];
            }
        }
        return $output;
    }

    public function get_grouped_taxonomies()
    {
        $output['taxonomy'] = [];
        $output['attribute'] = [];
        $taxonomies = get_object_taxonomies('product', 'objects');
        $default_taxonomies = Meta_Fields::get_default_taxonomies();
        foreach ($taxonomies as $taxonomy) {
            if (taxonomy_exists($taxonomy->name) && !in_array($taxonomy->name, $default_taxonomies)) {
                $tax_type = \wcbel\classes\helpers\Meta_Fields::get_taxonomy_type($taxonomy->name);
                $output[$tax_type][$taxonomy->name] = [
                    'label' => $taxonomy->label,
                ];
            }
        }
        return $output;
    }

    public function get_taxonomy_groups()
    {
        return [
            'taxonomy' => esc_html__('Taxonomy', 'ithemeland-woo-bulk-product-editor-lite'),
            'attribute' => esc_html__('Attribute', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public function get_product_taxonomies($product_id)
    {
        $output = [];
        $taxonomies = get_post_taxonomies(intval($product_id));
        if (!empty($taxonomies) && is_array($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $terms = wc_get_product_term_ids(intval($product_id), $taxonomy);
                if (!empty($terms) && is_array($terms)) {
                    $output[$taxonomy] = $terms;
                }
            }
        }

        return $output;
    }

    public function get_product_fields($product_object)
    {
        if (!($product_object instanceof \WC_Product)) {
            return [];
        }

        $post_object = get_post($product_object->get_id());
        $post_meta = get_post_meta($product_object->get_id());
        $product_taxonomy = $this->get_product_taxonomies($product_object->get_id());
        $variation_name = ($product_object->get_type() == 'variation') ? "variation_" : '';
        $cog_variable = ($product_object->get_type() == 'variable') ? "_variable" : '';

        $yith_badge = (!empty($post_meta['_yith_wcbm_product_meta'][0])) ? unserialize($post_meta['_yith_wcbm_product_meta'][0]) : [];
        return [
            'id' => $product_object->get_id(),
            'post_parent' => $product_object->get_parent_id(),
            'type' => $product_object->get_type(),
            'title' => $product_object->get_name(),
            'slug' => $product_object->get_slug(),
            'description' => wpautop($product_object->get_description()),
            'short_description' => wpautop($product_object->get_short_description()),
            'date_created' => (!empty($product_object->get_date_created()) && !empty($product_object->get_date_created()->date('Y/m/d H:i'))) ? $product_object->get_date_created()->format('Y/m/d H:i') : '',
            'status' => $product_object->get_status(),
            'regular_price' => $product_object->get_regular_price(),
            'sale_price' => $product_object->get_sale_price(),
            'image_id' => [
                'id' => $product_object->get_image_id(),
                'small' => $product_object->get_image([40, 40]),
                'medium' => $product_object->get_image([300, 300]),
                'big' => $product_object->get_image([600, 600]),
            ],
            'gallery_image_ids' => $product_object->get_gallery_image_ids(),
            'manage_stock' => $product_object->get_manage_stock(),
            'product_cat' => $product_object->get_category_ids(),
            'product_tag' => $product_object->get_tag_ids(),
            'catalog_visibility' => $product_object->get_catalog_visibility(),
            'featured' => $product_object->get_featured(),
            'date_on_sale_from' => (!empty($product_object->get_date_on_sale_from()) && !empty($product_object->get_date_on_sale_from()->date('Y/m/d'))) ? $product_object->get_date_on_sale_from()->format('Y/m/d') : '',
            'date_on_sale_to' => (!empty($product_object->get_date_on_sale_to()) && !empty($product_object->get_date_on_sale_to()->date('Y/m/d'))) ? $product_object->get_date_on_sale_to()->format('Y/m/d') : '',
            'downloadable' => $product_object->get_downloadable(),
            'sku' => $product_object->get_sku(),
            'stock_status' => $product_object->get_stock_status(),
            'sold_individually' => $product_object->get_sold_individually(),
            'shipping_class' => $product_object->get_shipping_class_id(),
            'upsell_ids' => $product_object->get_upsell_ids(),
            'cross_sell_ids' => $product_object->get_cross_sell_ids(),
            'purchase_note' => $product_object->get_purchase_note(),
            'reviews_allowed' => $product_object->get_reviews_allowed(),
            'average_rating' => $product_object->get_average_rating(),
            'virtual' => $product_object->get_virtual(),
            'download_limit' => $product_object->get_download_limit(),
            'download_expiry' => $product_object->get_download_expiry(),
            'stock_quantity' => $product_object->get_stock_quantity(),
            'tax_class' => $product_object->get_tax_class(),
            'tax_status' => $product_object->get_tax_status(),
            'width' => $product_object->get_width(),
            'height' => $product_object->get_height(),
            'length' => $product_object->get_length(),
            'weight' => $product_object->get_weight(),
            'backorders' => $product_object->get_backorders(),
            'menu_order' => $product_object->get_menu_order(),
            'total_sales' => $product_object->get_total_sales(),
            'review_count' => $product_object->get_review_count(),
            'product_type' => $product_object->get_type(),
            '_button_text' => (!empty($post_meta['_button_text'])) ? $post_meta['_button_text'] : '',
            '_product_url' => (!empty($post_meta['_product_url'])) ? $post_meta['_product_url'] : '',
            '_children' => (!empty($post_meta['_children'])) ? $post_meta['_children'] : '',
            'downloadable_files' => $product_object->get_downloads(),
            'post_author' => $post_object->post_author,
            'minimum_allowed_quantity' => (!empty($post_meta[$variation_name . 'minimum_allowed_quantity'][0])) ? $post_meta[$variation_name . 'minimum_allowed_quantity'][0] : '',
            'maximum_allowed_quantity' => (!empty($post_meta[$variation_name . 'maximum_allowed_quantity'][0])) ? $post_meta[$variation_name . 'maximum_allowed_quantity'][0] : '',
            'group_of_quantity' => (!empty($post_meta[$variation_name . 'group_of_quantity'][0])) ? $post_meta[$variation_name . 'group_of_quantity'][0] : '',
            'minmax_do_not_count' => (!empty($post_meta[$variation_name . 'minmax_do_not_count'][0])) ? $post_meta[$variation_name . 'minmax_do_not_count'][0] : '',
            'minmax_cart_exclude' => (!empty($post_meta[$variation_name . 'minmax_cart_exclude'][0])) ? $post_meta[$variation_name . 'minmax_cart_exclude'][0] : '',
            'minmax_category_group_of_exclude' => (!empty($post_meta[$variation_name . 'minmax_category_group_of_exclude'][0])) ? $post_meta[$variation_name . 'minmax_category_group_of_exclude'][0] : '',
            'min_max_rules' => (!empty($post_meta['min_max_rules'][0])) ? $post_meta['min_max_rules'][0] : '',
            'allow_combination' => (!empty($post_meta['allow_combination'][0])) ? $post_meta['allow_combination'][0] : '',
            '_ywmmq_product_minimum_quantity' => (!empty($post_meta['_ywmmq_product_minimum_quantity'][0])) ? $post_meta['_ywmmq_product_minimum_quantity'][0] : '',
            '_ywmmq_product_maximum_quantity' => (!empty($post_meta['_ywmmq_product_maximum_quantity'][0])) ? $post_meta['_ywmmq_product_maximum_quantity'][0] : '',
            '_ywmmq_product_step_quantity' => (!empty($post_meta['_ywmmq_product_step_quantity'][0])) ? $post_meta['_ywmmq_product_step_quantity'][0] : '',
            '_ywmmq_product_exclusion' => (!empty($post_meta['_ywmmq_product_exclusion'][0])) ? $post_meta['_ywmmq_product_exclusion'][0] : '',
            '_ywmmq_product_quantity_limit_override' => (!empty($post_meta['_ywmmq_product_quantity_limit_override'][0])) ? $post_meta['_ywmmq_product_quantity_limit_override'][0] : '',
            '_ywmmq_product_quantity_limit_variations_override' => (!empty($post_meta['_ywmmq_product_quantity_limit_variations_override'][0])) ? $post_meta['_ywmmq_product_quantity_limit_variations_override'][0] : '',
            '_product_commission' => (!empty($post_meta['_product_commission'][0])) ? $post_meta['_product_commission'][0] : '',
            'yith_shop_vendor' => wp_get_post_terms($product_object->get_id(), 'yith_shop_vendor', ['fields' => 'id=>slug']),
            '_wcpv_product_commission' => (!empty($post_meta['_wcpv_product_commission'][0])) ? $post_meta['_wcpv_product_commission'][0] : '',
            '_wcpv_product_taxes' => (!empty($post_meta['_wcpv_product_taxes'][0])) ? $post_meta['_wcpv_product_taxes'][0] : '',
            '_wcpv_product_pass_shipping' => (!empty($post_meta['_wcpv_product_pass_shipping'][0])) ? $post_meta['_wcpv_product_pass_shipping'][0] : '',
            'wcpv_product_vendors' => wp_get_post_terms($product_object->get_id(), 'wcpv_product_vendors', ['fields' => 'id=>slug']),
            'yith_cog_cost' => (!empty($post_meta['yith_cog_cost' . $cog_variable][0])) ? $post_meta['yith_cog_cost' . $cog_variable][0] : '',
            '_wc_cog_cost' => (!empty($post_meta['_wc_cog_cost' . $cog_variable][0])) ? $post_meta['_wc_cog_cost' . $cog_variable][0] : '',
            '_regular_price_wmcp' => (!empty($post_meta['_regular_price_wmcp'])) ? $post_meta['_regular_price_wmcp'] : [],
            '_sale_price_wmcp' => (!empty($post_meta['_sale_price_wmcp'])) ? $post_meta['_sale_price_wmcp'] : [],
            '_yith_wcbm_product_meta' => (!empty($post_meta['_yith_wcbm_product_meta'])) ? $post_meta['_yith_wcbm_product_meta'] : [],
            '_yith_wcbm_product_meta_-_id_badge' => (!empty($yith_badge['id_badge'])) ? $yith_badge['id_badge'] : [],
            '_yith_wcbm_product_meta_-_start_date' => (!empty($yith_badge['start_date'])) ? $yith_badge['start_date'] : [],
            '_yith_wcbm_product_meta_-_end_date' => (!empty($yith_badge['end_date'])) ? $yith_badge['end_date'] : [],
            'it_product_disable_discount' => (!empty($post_meta['it_product_disable_discount'][0])) ? $post_meta['it_product_disable_discount'][0] : [],
            'it_product_hide_price_unregistered' => (!empty($post_meta['it_product_hide_price_unregistered'][0])) ? $post_meta['it_product_hide_price_unregistered'][0] : [],
            'custom_field' => $post_meta,
            'taxonomy' => $product_taxonomy,
        ];
    }

    private function set_fetch_methods()
    {
        $this->fetch_methods = [
            'id' => 'get_id',
            'title' => 'get_name',
            'product_type' => 'get_type',
            'description' => 'get_description',
            'short_description' => 'get_short_description',
            'status' => 'get_status',
            'date_created' => 'get_date_created',
            'manage_stock' => 'get_manage_stock',
            'image_id' => 'get_image_id',
            'regular_price' => 'get_regular_price',
            'sale_price' => 'get_sale_price',
            'catalog_visibility' => 'get_catalog_visibility',
            'slug' => 'get_slug',
            'sku' => 'get_sku',
            'purchase_note' => 'get_purchase_note',
            'menu_order' => 'get_menu_order',
            'sold_individually' => 'get_sold_individually',
            'reviews_allowed' => 'get_reviews_allowed',
            'gallery_image_ids' => 'get_gallery_image_ids',
            'date_on_sale_from' => 'get_date_on_sale_from',
            'date_on_sale_to' => 'get_date_on_sale_to',
            'tax_status' => 'get_tax_status',
            'tax_class' => 'get_tax_class',
            'shipping_class' => 'get_shipping_class_id',
            'width' => 'get_width',
            'height' => 'get_height',
            'length' => 'get_length',
            'weight' => 'get_weight',
            'stock_status' => 'get_stock_status',
            'stock_quantity' => 'get_stock_quantity',
            'backorders' => 'get_backorders',
            'featured' => 'get_featured',
            'virtual' => 'get_virtual',
            'downloadable' => 'get_downloadable',
            'downloadable_files' => 'get_downloads',
            'download_limit' => 'get_download_limit',
            'download_expiry' => 'get_download_expiry',
            'total_sales' => 'get_total_sales',
            'review_count' => 'get_review_count',
            'average_rating' => 'get_average_rating',
            'upsell_ids' => 'get_upsell_ids',
            'cross_sell_ids' => 'get_cross_sell_ids',
        ];
    }

    public function get_product_field($product_object, $fetch_type, $field_name)
    {
        $value = '';
        switch ($fetch_type) {
            case 'woocommerce':
                if (empty($this->fetch_methods)) {
                    $this->set_fetch_methods();
                }
                if (!isset($this->fetch_methods[$field_name]) || !method_exists($product_object, $this->fetch_methods[$field_name])) {
                    return '';
                }
                $value = $product_object->{$this->fetch_methods[$field_name]}();
                break;
            case 'taxonomy':
                $value =  wc_get_product_term_ids($product_object->get_id(), $field_name);
                break;
            case 'meta_field':
                $value = $product_object->get_meta($field_name, true);
                break;
        }

        return $value;
    }

    public function get_product_column_values($product_object, $columns)
    {
        $values = [];
        if (!empty($columns) && is_array($columns)) {
            foreach ($columns as $column_key => $column_data) {
                if (empty($column_data['fetch_type'])) {
                    continue;
                }

                switch ($column_data['fetch_type']) {
                    case 'woocommerce':
                        if (empty($this->fetch_methods)) {
                            $this->set_fetch_methods();
                        }
                        if (!isset($this->fetch_methods[$column_data['name']]) || !method_exists($product_object, $this->fetch_methods[$column_data['name']])) {
                            $values[$column_data['name']] = '';
                        }
                        $values[$column_data['name']] = $product_object->{$this->fetch_methods[$column_data['name']]}();
                        break;
                    case 'taxonomy':
                        $values[$column_data['name']] =  wc_get_product_term_ids($product_object->get_id(), $column_data['name']);
                        break;
                    case 'meta_field':
                        $values[$column_data['name']] = $product_object->get_meta($column_data['name'], true);
                        break;
                    default:
                        $values[$column_data['name']] = '';
                }
            }
        }

        return apply_filters('wcbe_table_column_values', $values, $product_object->get_id(), array_keys($columns));
    }

    public function create($data = [])
    {
        $product = new \WC_Product();

        $product->set_name(!empty($data['title']) ? sanitize_text_field($data['title']) : 'New Product');
        $product->set_slug(!empty($data['slug']) ? sanitize_title($data['slug']) : '');
        $product->set_sku($this->generate_incremental_sku(!empty($data['sku']) ? sanitize_text_field($data['sku']) : ''));
        $product->set_description(!empty($data['description']) ? sanitize_textarea_field($data['description']) : '');
        $product->set_short_description(!empty(($data['short_description'])) ? sanitize_textarea_field($data['short_description']) : '');
        $product->set_status(!empty($data['status']) ? sanitize_text_field($data['status']) : 'draft');
        $product->set_purchase_note(!empty($data['purchase_note']) ? sanitize_textarea_field($data['purchase_note']) : '');
        $product->set_menu_order(intval($data['menu_order']));
        $product->set_sold_individually(!empty($data['sold_individually']));
        $product->set_reviews_allowed(!empty($data['reviews_allowed']));
        $product->set_catalog_visibility(!empty($data['catalog_visibility']) ? sanitize_text_field($data['catalog_visibility']) : 'visible');
        $product->set_date_created(!empty($data['date_created']) ? strtotime($data['date_created']) : current_time('timestamp'));
        $product->set_image_id(!empty($data['image_id']) ? intval($data['image_id']) : '');
        $product->set_gallery_image_ids(!empty($data['gallery_image_ids']) ? array_map('intval', $data['gallery_image_ids']) : []);
        $product->set_category_ids(!empty($data["taxonomies"]["product_cat"]) ? array_map('intval', $data["taxonomies"]["product_cat"]) : []);

        $tag_ids = $this->get_tags_new_product(!empty($data["taxonomies"]["product_tag"]) ? $data["taxonomies"]["product_tag"] : []);
        if (!empty($tag_ids)) {
            $product->set_tag_ids(array_map('intval', $tag_ids));
        }

        if (!empty($data["attributes"]) && is_array($data["attributes"])) {
            $position = 0; // Initialize position counter
            $attributes = [];
            foreach ($data["attributes"] as $attribute_name => $data) {
                $attribute = new \WC_Product_Attribute();
                $attribute_name = sanitize_text_field($attribute_name);
                $attribute_id = wc_attribute_taxonomy_id_by_name($attribute_name);
                $attribute->set_id($attribute_id ? $attribute_id : 0); // Use global ID or 0 for custom

                $attribute->set_name($attribute_name);
                $attribute->set_options(array_map('sanitize_text_field', $data["name"]));

                $attribute->set_position($position); // Set position based on loop iteration
                $position++; // Increment position for the next attribute

                $attribute->set_visible($data["is_visible"] === 'yes');
                $attribute->set_variation($data["used_for_variations"] === 'yes');

                $attributes[] = $attribute;
            }
            if (!empty($attributes)) {
                $product->set_attributes($attributes);
            }
        }

        $regular_price = isset($data["regular_price"]) ? sanitize_text_field($data["regular_price"]) : '';
        $round_item_regular_price = isset($data["round_item_regular_price"]) ? sanitize_text_field($data["round_item_regular_price"]) : '';
        $sale_price = isset($data["sale_price"]) ? sanitize_text_field($data["sale_price"]) : '';
        $round_item_sale_price = isset($data["round_item_sale_price"]) ? sanitize_text_field($data["round_item_sale_price"]) : '';

        if (!empty($round_item_regular_price)) {
            $product->set_regular_price(round($regular_price, $round_item_regular_price));
        }
        $product->set_regular_price($regular_price);
        if (!empty($round_item_sale_price)) {
            $product->set_sale_price(round($sale_price, $round_item_sale_price));
        }
        $product->set_sale_price($sale_price);

        $product->set_date_on_sale_from(!empty($data["sale_date_from"]) ? sanitize_text_field($data["sale_date_from"]) : '');
        $product->set_date_on_sale_to(!empty($data["sale_date_to"]) ? sanitize_text_field($data["sale_date_to"]) : '');
        $product->set_tax_status(!empty($data["tax_status"]) ? sanitize_text_field($data["tax_status"]) : '');
        $product->set_tax_class(!empty($data["tax_class"]) ? sanitize_text_field($data["tax_class"]) : '');
        //Set Shipping
        $product->set_shipping_class_id(!empty($data["shipping_class"]) ? sanitize_text_field($data["shipping_class"]) : '');
        $product->set_width(isset($data["width"]) ? sanitize_text_field($data["width"]) : '');
        $product->set_height(isset($data["height"]) ? sanitize_text_field($data["height"]) : '');
        $product->set_length(isset($data["length"]) ? sanitize_text_field($data["length"]) : '');
        $product->set_weight(isset($data["weight"]) ? sanitize_text_field($data["weight"]) : '');
        //Set Stock
        $product->set_manage_stock(isset($data["manage_stock"]) ? sanitize_text_field($data["manage_stock"]) : '');
        $product->set_stock_status(isset($data["stock_status"]) ? sanitize_text_field($data["stock_status"]) : '');
        $product->set_stock_quantity(isset($data["stock_quantity"]) ? sanitize_text_field($data["stock_quantity"]) : '');
        $product->set_backorders(isset($data["backorders"]) ? sanitize_text_field($data["backorders"]) : '');

        $product->set_featured(isset($data["featured"]) ? sanitize_text_field($data["featured"]) : '');
        $product->set_virtual(isset($data["virtual"]) ? sanitize_text_field($data["virtual"]) : '');
        $product->set_downloadable(isset($data["downloadable"]) ? sanitize_text_field($data["downloadable"]) : '');
        $product->set_download_limit(isset($data["download_limit"]) ? sanitize_text_field($data["download_limit"]) : '');
        $product->set_download_expiry(isset($data["download_expiry"]) ? sanitize_text_field($data["download_expiry"]) : '');

        if (!empty($data["product_type"]) && $data["product_type"] == 'external') {
            $product = new \WC_Product_External();
            $product->set_product_url(!empty($data["product_url"]) ? $data["product_url"] : '');
            $product->set_button_text(!empty($data["button_text"]) ? $data["button_text"] : '');
        }

        $product->set_upsell_ids(!empty($data["upsells"]) ? array_map('intval', $data["upsells"]) : '');
        $product->set_cross_sell_ids(!empty($data["cross_sells"]) ? array_map('intval', $data["cross_sells"]) : '');

        $product->save();

        if (!empty($data["product_type"])) {
            wp_set_object_terms($product->get_id(), sanitize_text_field($data["product_type"]), 'product_type');
        }

        if (!empty($data['author'])) {
            wp_update_post([
                'ID' => $product->get_id(),
                'post_author' => intval($data['author'])
            ]);
        }

        return $product->get_id();
    }

    private function get_tags_new_product($tags)
    {
        //Set Tags By ID
        $tag_ids = [];
        foreach ($tags as $tag_name) {
            $tag_obj = get_term_by('name', sanitize_text_field($tag_name), 'product_tag');

            if ($tag_obj) {
                $tag_ids[] = $tag_obj->term_id; // Existing tag ID
            } else {
                $new_tag = wp_insert_term($tag_name, 'product_tag'); // Create new tag
                if (!is_wp_error($new_tag)) {
                    $tag_ids[] = $new_tag['term_id'];
                }
            }
        }

        return $tag_ids;
    }
    private function generate_incremental_sku($base_sku)
    {
        global $wpdb;

        if (empty($base_sku)) {
            return '';
        }

        // Check if the exact base SKU already exists
        $existing_sku = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s LIMIT 1", $base_sku));  //phpcs:ignore

        // If the exact SKU does NOT exist, return it as is
        if (!$existing_sku) {
            return $base_sku;
        }

        // Extract prefix and number from base SKU
        if (preg_match('/(\d+)$/', $base_sku, $matches)) {
            $sku_prefix = preg_replace('/\d+$/', '', $base_sku); // Extract prefix 
            $sku_number = intval($matches[1]); // Extract number 
        } else {
            $sku_prefix = $base_sku; // If no number, keep full SKU as prefix
            $sku_number = 0; // Start numbering from 0
        }

        do {
            // Increase the SKU number
            $sku_number++;

            // Generate the new SKU
            $new_sku = $sku_prefix . $sku_number;

            // Check if the new SKU already exists
            $existing_sku = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s LIMIT 1", $new_sku)); //phpcs:ignore
        } while ($existing_sku); // Keep incrementing if the SKU already exists

        return $new_sku;
    }
    public function get_product_counts_group_by_status()
    {
        $output = [];
        $all = 0;
        $result = $this->wpdb->get_results("SELECT post_status AS 'status',COUNT(*) AS 'count' FROM {$this->wpdb->posts} WHERE post_type = 'product' AND post_status NOT IN ('auto-draft') GROUP BY post_status", ARRAY_A); //phpcs:ignore
        if (!empty($result) && is_array($result)) {
            foreach ($result as $item) {
                if (isset($item['status']) && isset($item['count'])) {
                    if ($item['status'] !== 'trash') {
                        $all += $item['count'];
                    }
                    $output[$item['status']] = $item['count'];
                }
            }
        }
        $output['all'] = intval($all);
        return $output;
    }

    public function get_status_color($status)
    {
        $status_colors = $this->get_status_colors();
        return (isset($status_colors[$status])) ? $status_colors[$status] : null;
    }

    private function get_status_colors()
    {
        return [
            'draft' => '#a3b7a3',
            'pending' => '#80e045',
            'private' => '#f9c662',
            'publish' => '#6ca9d6',
            'trash' => '#808080',
        ];
    }

    public function get_tax_classes()
    {
        $tax_classes[''] = esc_html__('Standard', 'ithemeland-woo-bulk-product-editor-lite');
        foreach (\WC_Tax::get_tax_classes() as $tax_class) {
            $name = str_replace(' ', '-', strtolower($tax_class));
            $tax_classes[$name] = $tax_class;
        }
        return $tax_classes;
    }

    public function get_yith_vendors()
    {
        $yith_shop_vendor_object = [];
        if (defined("YITH_WPV_INIT")) {
            $yith_shop_vendor_object = get_terms([
                'taxonomy' => 'yith_shop_vendor',
                'hide_empty' => false,
            ]);
        }

        return $yith_shop_vendor_object;
    }

    public function get_wc_product_vendors()
    {
        $wc_shop_vendor_object = [];
        if (class_exists("WC_Product_Vendors")) {
            $wc_shop_vendor_object = get_terms([
                'taxonomy' => 'wcpv_product_vendors',
                'hide_empty' => false,
            ]);
        }

        return $wc_shop_vendor_object;
    }

    public function get_ithemeland_badge_fields()
    {
        return [
            '_unique_label_type',
            '_unique_label_shape',
            '_unique_label_advanced',
            '_unique_label_text',
            '_unique_label_badge_icon',
            '_unique-custom-background',
            '_unique-custom-text',
            '_unique_label_align',
            '_unique_label_image',
            '_unique_label_class',
            '_unique_label_font_size',
            '_unique_label_line_height',
            '_unique_label_width',
            '_unique_label_height',
            '_unique_label_border_style',
            '_unique_label_border_width_top',
            '_unique_label_border_width_right',
            '_unique_label_border_width_bottom',
            '_unique_label_border_width_left',
            '_unique_label_border_color',
            '_unique_label_border_r_tl',
            '_unique_label_border_r_tr',
            '_unique_label_border_r_br',
            '_unique_label_border_r_bl',
            '_unique_label_padding_top',
            '_unique_label_padding_right',
            '_unique_label_padding_bottom',
            '_unique_label_padding_left',
            '_unique_label_opacity',
            '_unique_label_rotation_x',
            '_unique_label_rotation_y',
            '_unique_label_rotation_z',
            '_unique_label_pos_top',
            '_unique_label_pos_right',
            '_unique_label_pos_bottom',
            '_unique_label_pos_left',
            '_unique_label_time',
            '_unique_label_start_date',
            '_unique_label_end_date',
            '_unique_label_exclude',
            '_unique_label_flip_text_h',
            '_unique_label_flip_text_v',
        ];
    }

    public function get_product_ids_with_like_names($product_ids = [])
    {
        $product_id_clause = '';
        $placeholders = [];

        if (!empty($product_ids)) {
            $placeholders = array_map('intval', $product_ids);
            $placeholders_str = implode(',', array_fill(0, count($placeholders), '%d'));
            $product_id_clause = "AND ID IN ($placeholders_str)";
        }

        $query = "
            SELECT GROUP_CONCAT(ID) as product_ids, COUNT(*) as product_count
            FROM {$this->wpdb->posts}
            WHERE post_type = 'product' AND post_status != 'trash' {$product_id_clause}
            GROUP BY post_title
            HAVING product_count > 1
            ORDER BY product_count
        ";

        if (!empty($placeholders)) {
            $query = $this->wpdb->prepare($query, ...$placeholders); //phpcs:ignore
        }

        return $this->wpdb->get_results($query, ARRAY_A); //phpcs:ignore
    }

    public function get_trash()
    {
        $args = [
            'post_type' => ['product', 'product_variation'],
            'post_status' => 'trash',
            'fields' => 'ids',
        ];

        $products = $this->get_products($args);
        return $products->posts;
    }

    public function set_shipping_classes()
    {
        $this->shipping_classes = [];

        $shipping_classes = get_terms([
            'taxonomy' => 'product_shipping_class',
            'hide_empty' => '0',
            'orderby' => 'name',
        ]);

        if (!empty($shipping_classes)) {
            foreach ($shipping_classes as $shipping_class) {
                if ($shipping_class instanceof \WP_Term) {
                    $this->shipping_classes[$shipping_class->term_id] = $shipping_class->name;
                }
            }
        }
    }

    public function get_shipping_classes()
    {
        if (empty($this->shipping_classes)) {
            $this->set_shipping_classes();
        }

        return $this->shipping_classes;
    }
}
