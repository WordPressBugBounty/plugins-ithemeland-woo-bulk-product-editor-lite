<?php

namespace wcbel\classes\product_table;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Pagination;
use wcbel\classes\helpers\Render;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\Product;
use wcbel\classes\repositories\Setting;
use wcbel\classes\services\filter\Product_Filter_Service;

class ProductTable
{
    private $args;
    private $output_type;
    private $just_variations;
    private $just_included;
    private $row_handler;
    private $settings;
    private $items;
    private $columns;
    private $filter_data;
    private $product_repository;
    private $rows;
    private $status_filters;
    private $found_items;
    private $pagination;

    public static function prepare($params = [])
    {
        return new self($params);
    }

    private function __construct($params = [])
    {
        $this->args = !empty($params['args']) ? $params['args'] : [];
        $this->filter_data = !empty($params['filter_data']) ? $params['filter_data'] : [];
        $this->just_variations = isset($params['just_variations']) ? $params['just_variations'] : false;
        $this->just_included = isset($params['just_included']) ? $params['just_included'] : false;
        $this->output_type = isset($params['output_type']) ? $params['output_type'] : 'string';
        $this->rows = ($this->output_type == 'array') ? [] : '';

        $this->row_handler = RowHandler::get_instance();
        $this->product_repository = Product::get_instance();
        $setting_repository = Setting::get_instance();
        $this->settings = $setting_repository->get_settings();

        $this->set_product_rows();
    }

    public function get_status_filters()
    {
        return $this->status_filters;
    }

    public function get_found_items()
    {
        return $this->found_items;
    }

    public function get_pagination()
    {
        return $this->pagination;
    }

    public function get_rows()
    {
        return $this->rows;
    }

    public function set_product_rows()
    {
        $columns = $this->get_columns();
        $product_filter_service = Product_Filter_Service::get_instance();
        $filtered = $product_filter_service->get_filtered_products($this->filter_data, $this->args);

        if (!$this->just_variations) {
            $this->set_status_filters();
            $page = (!empty($this->args['paged'])) ? intval($this->args['paged']) : 1;
            $this->pagination = Pagination::init($page, intval($filtered['max_num_pages']));
        }

        $this->found_items = $filtered['found_posts'];

        if (empty($filtered['product_ids']) && empty($filtered['variation_ids'])) {
            $this->set_custom_row('<tr><td class="wcbe-text-alert" colspan="100%">No Data Available!</td></tr>');
        }

        $this->items = [
            'parents' => !empty($filtered['product_ids']) ? $filtered['product_ids'] : [],
            'variations' => !empty($filtered['variation_ids']) ? $filtered['variation_ids'] : [],
        ];

        $show_only_filtered_variations = (isset($this->settings['show_only_filtered_variations'])) ? $this->settings['show_only_filtered_variations'] : 'no';

        if (!empty($this->items['parents'])) {
            $products = $this->product_repository->get_product_object_by_ids([
                'include' => array_map('intval', $this->items['parents']),
                'orderby' => 'include',
                'post_status' => ['any', 'trash'],
            ]);

            if (!empty($products)) {
                foreach ($products as $product) {
                    $this->set_product_row($product, $columns);
                    if ($product->get_type() == 'variable' && isset($this->filter_data['show_variations']) && $this->filter_data['show_variations'] == 'yes' && !$this->just_included) {
                        $this->set_children_output($product, $columns);
                    }
                }
            }
        }

        if (!empty($this->items['variations'])) {
            $variations = $this->product_repository->get_product_object_by_ids([
                'include' => array_map('intval', $this->items['variations']),
                'orderby' => 'include',
                'post_status' => ['any', 'trash'],
            ]);

            $parents_listed = [];
            foreach ($variations as $variation) {
                if ($variation instanceof \WC_Product_Variation) {
                    if ($show_only_filtered_variations == 'yes' || $this->just_variations || $this->just_included) {
                        if (!in_array($variation->get_parent_id(), $this->items['parents'])) {
                            $this->set_product_row($variation, $columns);
                        }
                    } else {
                        if (!in_array($variation->get_parent_id(), $this->items['parents']) && !in_array($variation->get_parent_id(), $parents_listed)) {
                            $parent_object = $this->product_repository->get_product($variation->get_parent_id());
                            if ($parent_object instanceof \WC_Product) {
                                $parents_listed[] = $variation->get_parent_id();
                                $this->set_product_row($parent_object, $columns);
                                $this->set_children_output($parent_object, $columns);
                            }
                        }
                    }
                }
            }
        }
    }

    private function set_children_output($product, $columns)
    {
        $children_ids = $this->product_repository->get_product_variation_ids($product->get_id());
        if (!empty($this->filter_data['fields'])) {
            $children_ids = array_filter($children_ids, function ($variation_id) {
                return in_array($variation_id, $this->items['variations']);
            });
        }

        $max_page = 0;
        if (!empty($children_ids) && is_array($children_ids)) {
            $limit_variations = (!isset($this->settings['enable_load_more_variations']) || (isset($this->settings['enable_load_more_variations']) && $this->settings['enable_load_more_variations'] == 'yes'));

            if ($limit_variations) {
                $max_page = ceil(count($children_ids) / Product::VARIATIONS_PER_PAGE);
            }

            $children_ids = (!empty($this->filter_data['product_attributes'])) ? array_intersect($this->items['variations'], $children_ids) : $children_ids;
            if (!empty($children_ids)) {
                $variation_ids = ($limit_variations) ? array_slice($children_ids, 0, Product::VARIATIONS_PER_PAGE) : $children_ids;
                $children = $this->product_repository->get_product_object_by_ids(['include' => array_map('intval', $variation_ids), 'orderby' => 'include']);
                foreach ($children as $child) {
                    if (($key = array_search($child->get_id(), $this->items['variations'])) !== false) {
                        unset($this->items['variations'][$key]);
                    }
                    $this->set_product_row($child, $columns);
                }

                if ($limit_variations) {
                    if (count($children_ids) > Product::VARIATIONS_PER_PAGE) {
                        $this->set_custom_row('<tr><td class="wcbe-products-table-load-more-variations-row" colspan="100%"><button type="button" data-page="2" data-max-page="' . esc_attr($max_page) . '" data-variable-id="' . esc_attr($product->get_id()) . '" class="wcbe-products-table-load-more-variations">' . esc_html__('Load more', 'ithemeland-woo-bulk-product-editor-lite') . ' <i class="wcbe-icon-chevron-down"></i></button><img class="wcbe-products-table-load-more-variations-loading" src="' . esc_url(WCBEL_IMAGES_URL . 'loading-2.gif') . '" width="16" height="16" alt="Loading..."></td></tr>');
                    }
                }
            }
        }
    }

    private function get_columns()
    {
        if (empty($this->columns)) {
            $this->set_columns();
        }

        return $this->columns;
    }

    private function set_columns()
    {
        $column_repository = Column::get_instance();
        $columns = $column_repository->get_columns();
        $active_columns = $column_repository->get_active_columns();
        if (empty($active_columns['fields'])) {
            return [];
        }

        $this->columns = $active_columns['fields'];

        $deactivated_columns = $column_repository->get_deactivated_columns();
        if (!empty($this->columns) && is_array($this->columns)) {
            foreach ($this->columns as $column_key => $column) {
                if (isset($columns[$column_key])) {
                    if (isset($columns[$column_key]['editable'])) {
                        $this->columns[$column_key]['editable'] = $columns[$column_key]['editable'];
                    }
                    if (isset($columns[$column_key]['update_type'])) {
                        $this->columns[$column_key]['update_type'] = $columns[$column_key]['update_type'];
                    }
                }
                $exploded = explode('_-_', $column_key);
                $col_key = (!empty($exploded[0])) ? $exploded[0] : $column_key;
                if (in_array($col_key, $deactivated_columns) || in_array($column_key, $deactivated_columns)) {
                    unset($this->columns[$column_key]);
                }
            }
        }
    }

    private function set_product_row($product, $columns)
    {
        if ($this->output_type == 'array') {
            $this->rows[$product->get_id()] = $this->row_handler->get_row($product, $columns);
        } else {
            $this->rows .= $this->row_handler->get_row($product, $columns);
        }
    }

    private function set_custom_row($row)
    {
        if ($this->output_type == 'array') {
            $this->rows[] = $row;
        } else {
            $this->rows .= $row;
        }
    }

    private function set_status_filters()
    {
        $product_counts_by_status = $this->product_repository->get_product_counts_group_by_status();
        $product_statuses = $this->product_repository->get_product_statuses();
        $this->status_filters = Render::html(WCBEL_VIEWS_DIR . "bulk_edit/status_filters.php", compact('product_counts_by_status', 'product_statuses'));
    }
}
