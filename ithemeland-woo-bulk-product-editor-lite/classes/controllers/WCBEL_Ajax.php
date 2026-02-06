<?php

namespace wcbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Others;
use wcbel\classes\helpers\Render;
use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\meta_field\ACF_Plugin_Fields;
use wcbel\classes\helpers\Compatible_Helper;
use wcbel\classes\helpers\Filter_Helper;
use wcbel\classes\helpers\Meta_Field as Meta_Field_Helper;
use wcbel\classes\helpers\Meta_Fields;
use wcbel\classes\helpers\Taxonomy;
use wcbel\classes\product_table\ProductTable;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\History;
use wcbel\classes\services\history\HistoryRedoService;
use wcbel\classes\services\history\HistoryUndoService;
use wcbel\classes\repositories\Meta_Field;
use wcbel\classes\repositories\Product;
use wcbel\classes\repositories\Search;
use wcbel\classes\repositories\Setting;
use wcbel\classes\services\background_process\ProductBackgroundProcess;
use wcbel\classes\services\filter\Product_Filter_Service;
use wcbel\classes\services\product_delete\ProductDeleteService;
use wcbel\classes\services\product_duplicate\ProductDuplicateService;
use wcbel\classes\services\product_update\Update_Service;
use wcbel\classes\repositories\meta_field\Meta_Field_Main;
use wcbel\classes\repositories\EditFormItems;
use wcbel\classes\repositories\FilterFormItems;
use wcbel\classes\repositories\NewFormItems;

class WCBEL_Ajax
{
    private static $instance;
    private $product_repository;
    public $history_repository;

    public static function register_callback()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->product_repository = Product::get_instance();
        $this->history_repository = History::get_instance();
        add_action('wp_ajax_wcbe_add_meta_keys_by_product_id', [$this, 'add_meta_keys_by_product_id']);
        add_action('wp_ajax_wcbe_column_manager_add_field', [$this, 'column_manager_add_field']);
        add_action('wp_ajax_wcbe_column_manager_get_fields_for_edit', [$this, 'column_manager_get_fields_for_edit']);
        add_action('wp_ajax_wcbe_products_filter', [$this, 'products_filter']);
        add_action('wp_ajax_wcbe_save_filter_preset', [$this, 'save_filter_preset']);
        add_action('wp_ajax_wcbe_product_edit', [$this, 'product_edit']);
        add_action('wp_ajax_wcbe_get_products_name', [$this, 'get_products_name']);
        add_action('wp_ajax_wcbe_create_new_product', [$this, 'create_new_product']);
        add_action('wp_ajax_wcbe_get_attribute_values', [$this, 'get_attribute_values']);
        add_action('wp_ajax_wcbe_get_attribute_values_for_delete', [$this, 'get_attribute_values_for_delete']);
        add_action('wp_ajax_wcbe_get_attribute_values_for_attach', [$this, 'get_attribute_values_for_attach']);
        add_action('wp_ajax_wcbe_get_product_variations', [$this, 'get_product_variations']);
        add_action('wp_ajax_wcbe_get_product_variations_for_attach', [$this, 'get_product_variations_for_attach']);
        add_action('wp_ajax_wcbe_set_products_variations', [$this, 'set_products_variations']);
        add_action('wp_ajax_wcbe_delete_products_variations', [$this, 'delete_products_variations']);
        add_action('wp_ajax_wcbe_delete_products', [$this, 'delete_products']);
        add_action('wp_ajax_wcbe_untrash_products', [$this, 'untrash_products']);
        add_action('wp_ajax_wcbe_empty_trash', [$this, 'empty_trash']);
        add_action('wp_ajax_wcbe_duplicate_product', [$this, 'duplicate_product']);
        add_action('wp_ajax_wcbe_add_product_taxonomy', [$this, 'add_product_taxonomy']);
        add_action('wp_ajax_wcbe_add_product_attribute', [$this, 'add_product_attribute']);
        add_action('wp_ajax_wcbe_load_filter_profile', [$this, 'load_filter_profile']);
        add_action('wp_ajax_wcbe_delete_filter_profile', [$this, 'delete_filter_profile']);
        add_action('wp_ajax_wcbe_save_column_profile', [$this, 'save_column_profile']);
        add_action('wp_ajax_wcbe_get_text_editor_content', [$this, 'get_text_editor_content']);
        add_action('wp_ajax_wcbe_history_filter', [$this, 'history_filter']);
        add_action('wp_ajax_wcbe_history_undo', [$this, 'history_undo']);
        add_action('wp_ajax_wcbe_history_redo', [$this, 'history_redo']);
        add_action('wp_ajax_wcbe_change_count_per_page', [$this, 'change_count_per_page']);
        add_action('wp_ajax_wcbe_filter_profile_change_use_always', [$this, 'filter_profile_change_use_always']);
        add_action('wp_ajax_wcbe_get_default_filter_profile_products', [$this, 'get_default_filter_profile_products']);
        add_action('wp_ajax_wcbe_get_taxonomy_parent_select_box', [$this, 'get_taxonomy_parent_select_box']);
        add_action('wp_ajax_wcbe_get_product_data', [$this, 'get_product_data']);
        add_action('wp_ajax_wcbe_get_product_by_ids', [$this, 'get_product_by_ids']);
        add_action('wp_ajax_wcbe_get_product_files', [$this, 'get_product_files']);
        add_action('wp_ajax_wcbe_add_new_file_item', [$this, 'add_new_file_item']);
        add_action('wp_ajax_wcbe_variation_attaching', [$this, 'variation_attaching']);
        add_action('wp_ajax_wcbe_sort_by_column', [$this, 'sort_by_column']);
        add_action('wp_ajax_wcbe_clear_filter_data', [$this, 'clear_filter_data']);
        add_action('wp_ajax_wcbe_get_product_badge_ids', [$this, 'get_product_badge_ids']);
        add_action('wp_ajax_wcbe_get_product_ithemeland_badge', [$this, 'get_product_ithemeland_badge']);
        add_action('wp_ajax_wcbe_get_yikes_custom_product_tabs', [$this, 'get_yikes_custom_product_tabs']);
        add_action('wp_ajax_wcbe_add_yikes_saved_tab', [$this, 'add_yikes_saved_tab']);
        add_action('wp_ajax_wcbe_get_product_gallery_images', [$this, 'get_product_gallery_images']);
        add_action('wp_ajax_wcbe_get_it_wc_role_prices', [$this, 'get_it_wc_role_prices']);
        add_action('wp_ajax_wcbe_get_it_wc_dynamic_pricing_selected_roles', [$this, 'get_it_wc_dynamic_pricing_selected_roles']);
        add_action('wp_ajax_wcbe_get_it_wc_dynamic_pricing_all_fields', [$this, 'get_it_wc_dynamic_pricing_all_fields']);
        add_action('wp_ajax_wcbe_history_change_page', [$this, 'history_change_page']);
        add_action('wp_ajax_wcbe_get_product_custom_field_files', [$this, 'get_product_custom_field_files']);
        add_action('wp_ajax_wcbe_add_custom_field_file_item', [$this, 'add_custom_field_file_item']);
        add_action('wp_ajax_wcbe_bulk_edit_add_custom_field_file_item', [$this, 'bulk_edit_add_custom_field_file_item']);
        add_action('wp_ajax_wcbe_get_users', [$this, 'get_users']);
        add_action('wp_ajax_wcbe_get_bulk_new_tabs_content', [$this, 'get_bulk_new_tabs_content']);
        add_action('wp_ajax_wcbe_get_bulk_edit_tabs_content', [$this, 'get_bulk_edit_tabs_content']);
        add_action('wp_ajax_wcbe_get_filter_form_tabs_content', [$this, 'get_filter_form_tabs_content']);
        add_action('wp_ajax_wcbe_get_taxonomy_terms', [$this, 'get_taxonomy_terms']);
        add_action('wp_ajax_wcbe_get_more_variations', [$this, 'get_more_variations']);
        add_action('wp_ajax_wcbe_get_meta_fields_content', [$this, 'get_meta_fields_content']);
        add_action('wp_ajax_wcbe_get_column_manager_content', [$this, 'get_column_manager_content']);
        add_action('wp_ajax_wcbe_get_filter_profile_content', [$this, 'get_filter_profile_content']);
        add_action('wp_ajax_wcbe_get_column_profile_content', [$this, 'get_column_profile_content']);
        add_action('wp_ajax_wcbe_get_product_author', [$this, 'get_product_author']);
        add_action('wp_ajax_wcbe_get_product_taxonomy_terms', [$this, 'get_product_taxonomy_terms']);
        add_action('wp_ajax_wcbe_get_acf_taxonomy_terms', [$this, 'get_acf_taxonomy_terms']);
        add_action('wp_ajax_wcbe_get_product_attribute_terms', [$this, 'get_product_attribute_terms']);
        add_action('wp_ajax_wcbe_get_manage_variation_attributes_content', [$this, 'get_manage_variation_attributes_content']);
        add_action('wp_ajax_wcbe_is_processing', [$this, 'is_processing']);
        add_action('wp_ajax_wcbe_background_process_force_stop', [$this, 'background_process_force_stop']);
        add_action('wp_ajax_wcbe_background_process_clear_complete_message', [$this, 'background_process_clear_complete_message']);
        add_action('wp_ajax_wcbe_background_process_clear_tasks_count', [$this, 'background_process_clear_tasks_count']);
        add_action('wp_ajax_wcbe_column_manager_add_field', [$this, 'column_manager_add_field']);
        add_action('wp_ajax_wcbe_add_meta_keys_manual', [$this, 'add_meta_keys_manual']);
        add_action('wp_ajax_wcbe_add_acf_meta_field', [$this, 'add_acf_meta_field']);
    }

    public function is_processing()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $background_process = ProductBackgroundProcess::get_instance();
        $crashed = $background_process->crash_handler();

        $this->make_response([
            'is_processing' => $background_process->is_not_queue_empty(),
            'is_force_stopped' => $background_process->is_force_stopped(),
            'complete_message' => $background_process->get_complete_message(),
            'total_tasks' => $background_process->get_total_tasks(),
            'completed_tasks' => $background_process->get_completed_tasks_count(),
            'remaining_time' => $background_process->get_remaining_time(),
            'crashed' => $crashed
        ]);
    }

    public function background_process_clear_tasks_count()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $background_process = ProductBackgroundProcess::get_instance();
        $background_process->clear_tasks_count();
        $this->make_response([
            'success' => true,
        ]);
    }

    public function background_process_clear_complete_message()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $background_process = ProductBackgroundProcess::get_instance();
        $background_process->clear_complete_message();
        $this->make_response([
            'success' => true,
        ]);
    }

    public function background_process_force_stop()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $background_process = ProductBackgroundProcess::get_instance();
        $background_process->stop_process();

        $this->make_response([
            'success' => true,
        ]);
    }

    public function get_default_filter_profile_products()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $filter_data = Filter_Helper::get_active_filter_data();
        $show_variations = (isset($filter_data['show_variations'])) ? $filter_data['show_variations'] : 'no';
        $search_repository = Search::get_instance();
        $use_always = $search_repository->get_use_always();
        if ($use_always != 'default') {
            $preset = $search_repository->get_preset($use_always);
            if (!empty($preset['filter_data'])) {
                $filter_data = $preset['filter_data'];
                $filter_data['show_variations'] = $show_variations;
            }
        }

        if (!isset($filter_data['show_variations'])) {
            $filter_data['show_variations'] = 'no';
        }

        $product_table = ProductTable::prepare([
            'filter_data' => $filter_data
        ]);

        $histories = $this->history_repository->get_histories([], 1, 0);
        $reverted = $this->history_repository->get_latest_reverted();
        $this->make_response([
            'success' => true,
            'filter_data' => $filter_data,
            'rows' => $product_table->get_rows(),
            'status_filters' => $product_table->get_status_filters(),
            'pagination' => $product_table->get_pagination(),
            'products_count' => $product_table->get_found_items(),
            'history' => !empty($histories),
            'reverted' => !empty($reverted),
        ]);
    }

    public function products_filter()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['filter_data'])) {
            $filter_data = Sanitizer::array($_POST['filter_data']); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $search_repository = Search::get_instance();
            $search_repository->update_current_data([
                'last_filter_data' => $filter_data
            ]);

            if (!empty($_POST['option_values'])) {
                $search_repository->update_option_values(Sanitizer::array($_POST['option_values'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            }

            $current_page = !empty($_POST['current_page']) ? intval($_POST['current_page']) : 1;
            $product_table = ProductTable::prepare([
                'filter_data' => $filter_data,
                'args' => [
                    'paged' => $current_page
                ]
            ]);
            $this->make_response([
                'success' => true,
                'rows' => $product_table->get_rows(),
                'status_filters' => $product_table->get_status_filters(),
                'pagination' => $product_table->get_pagination(),
                'products_count' => $product_table->get_found_items(),
            ]);
        }

        return false;
    }

    public function get_users()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $list = [];
        if (!empty($_POST['search'])) {
            $query = new \WP_User_Query([
                'search' => '*' . sanitize_text_field(wp_unslash($_POST['search'])) . '*',
                'search_columns' => [
                    'user_login',
                    'user_email',
                    'display_name',
                ],
            ]);

            $users = $query->get_results();
            if (!empty($users)) {
                foreach ($users as $user) {
                    if ($user instanceof \WP_User) {
                        $list['results'][] = [
                            'id' => $user->ID,
                            'text' => $user->user_login
                        ];
                    }
                }
            }
        }

        $this->make_response($list);
    }

    public function add_meta_keys_by_product_id()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $product_id = intval($_POST['product_id']);
        $product = wc_get_product($product_id);
        if (!($product instanceof \WC_Product)) {
            die();
        }
        $meta_keys = Meta_Fields::remove_default_meta_keys(array_keys(get_post_meta($product_id)));
        $output = "";
        if (!empty($meta_keys)) {
            foreach ($meta_keys as $meta_key) {
                $meta_field['key'] = $meta_key;
                $meta_fields_main_types = Meta_Field::get_main_types();
                $meta_fields_sub_types = Meta_Field::get_sub_types();
                $output .= Render::html(WCBEL_VIEWS_DIR . "meta_field/meta_field_item.php", compact('meta_field', 'meta_fields_main_types', 'meta_fields_sub_types'));
            }
        }

        $this->make_response($output);
        return false;
    }

    public function column_manager_add_field()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['field_name']) && is_array($_POST['field_name']) && !empty($_POST['field_name']) && !empty($_POST['field_action'])) {
            $output = '';
            $field_action = sanitize_text_field(wp_unslash($_POST['field_action']));
            for ($i = 0; $i < count($_POST['field_name']); $i++) {
                $field_name = (isset($_POST['field_name'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_name'][$i])) : 'N\A';
                $field_label = (!empty($_POST['field_label'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_label'][$i])) : $field_name;
                $field_title = (!empty($_POST['field_label'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_label'][$i])) : $field_name;
                $output .= Render::html(WCBEL_VIEWS_DIR . "column_manager/field_item.php", compact('field_name', 'field_label', 'field_action', 'field_title'));
            }
            $this->make_response($output);
        }

        return false;
    }

    public function column_manager_get_fields_for_edit()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            $column_repository = Column::get_instance();
            $preset = $column_repository->get_preset(sanitize_text_field(wp_unslash($_POST['preset_key'])));
            if ($preset) {
                $output = '';
                $fields = [];
                if (isset($preset['fields'])) {
                    foreach ($preset['fields'] as $field) {
                        $field_info = [
                            'field_name' => $field['name'],
                            'field_label' => $field['label'],
                            'field_title' => $field['title'],
                            'field_background_color' => $field['background_color'],
                            'field_text_color' => $field['text_color'],
                            'field_action' => "edit",
                        ];
                        $fields[] = sanitize_text_field($field['name']);
                        $output .= Render::html(WCBEL_VIEWS_DIR . 'column_manager/field_item.php', $field_info);
                    }
                }

                $this->make_response([
                    'html' => $output,
                    'fields' => implode(',', $fields),
                ]);
            }
        }

        return false;
    }

    public function save_filter_preset()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['option_values'])) {
            $search_repository = Search::get_instance();
            $search_repository->update_option_values(Sanitizer::array($_POST['option_values'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        }

        if (!empty($_POST['preset_name'])) {
            $filter_item['name'] = sanitize_text_field(wp_unslash($_POST['preset_name']));
            $filter_item['date_modified'] = gmdate('Y-m-d H:i:s');
            $filter_item['key'] = 'preset-' . random_int(1000000, 9999999);
            $filter_item['filter_data'] = (!empty($_POST['filter_data'])) ? Sanitizer::array($_POST['filter_data']) : []; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $search_repository = Search::get_instance();
            $save_result = $search_repository->update($filter_item);
            if (!$save_result) {
                return false;
            }
            $new_item = Render::html(WCBEL_VIEWS_DIR . 'modals/filter_profile_item.php', compact('filter_item'));
            $this->make_response([
                'success' => $save_result,
                'new_item' => $new_item,
            ]);
        }
        return false;
    }

    public function product_edit()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_data']) || !is_array($_POST['product_data'])) {
            return false;
        }

        if (empty($_POST['product_ids'])) {
            return false;
        }

        if (is_array($_POST['product_ids'])) {
            if (!empty($_POST['type']) && $_POST['type'] == 'product_variations') {
                $product_ids = [];
                $ids = array_map('intval', $_POST['product_ids']);
                foreach ($ids as $variable_id) {
                    if (empty($variable_id)) {
                        continue;
                    }
                    $product_ids[] = $this->product_repository->get_product_variation_ids(intval($variable_id));
                }
            } else {
                $product_ids = array_map('intval', $_POST['product_ids']);
            }
        } elseif ($_POST['product_ids'] == 'all_filtered') {
            $product_ids = $this->get_all_filtered();
        } elseif ($_POST['product_ids'] == 'all' && !empty($_POST['variable_id'])) {
            $product_ids = $this->product_repository->get_product_variation_ids(intval($_POST['variable_id']));
        }

        if (empty($product_ids)) {
            return false;
        }

        $product_ids = Others::array_flatten($product_ids);
        $update_service = Update_Service::get_instance();
        $update_service->set_update_data([
            'update_type' => 'product',
            'product_ids' => $product_ids,
            'product_data' => Sanitizer::array($_POST['product_data']), //phpcs:ignore
            'save_history' => true,
        ]);
        $update_result = $update_service->perform();

        if ($update_service->is_processing()) {
            $this->make_response([
                'success' => $update_result,
                'product_ids' => $product_ids,
                'is_processing' => $update_service->is_processing(),
                'total_tasks' => count($product_ids),
                'completed_tasks' => 0,
            ]);
        } else {
            $product_table = ProductTable::prepare([
                'filter_data' => [
                    'show_variations' => 'yes'
                ],
                'output_type' => 'array',
                'just_included' => true,
                'args' => [
                    'post__in' => $product_ids
                ],
            ]);
            $history_count = $this->history_repository->get_history_count();
            $histories = $this->history_repository->get_histories();
            $reverted = $this->history_repository->get_latest_reverted();
            $product_repository = Product::get_instance();
            $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));
            $this->make_response([
                'success' => $update_result,
                'products' => $product_table->get_rows(),
                'product_statuses' => $product_repository->get_product_statuses_by_id($product_ids),
                'status_filters' => $product_table->get_status_filters(),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'is_processing' => $update_service->is_processing(),
                'reverted' => !empty($reverted),
            ]);
        }
    }

    public function get_variation()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['variation_id'])) {
            return $this->make_response(['success' => false]);
        }

        $variation = $this->product_repository->get_product(intval($_POST['variation_id']));
        if (!($variation instanceof \WC_Product_Variation)) {
            return $this->make_response(['success' => false]);
        }

        $variation_data = $variation->get_data();
        $variation_data['is_visible'] = ($variation->get_status() == 'publish');

        if (!empty($variation_data['downloads'])) {
            $downloads = [];
            foreach ($variation_data['downloads'] as $download_object) {
                $downloads[] = $download_object->get_data();
            }
            $variation_data['downloads'] = $downloads;
        }

        if (!empty($variation_data['date_on_sale_from'])) {
            $variation_data['date_on_sale_from'] = $variation_data['date_on_sale_from']->date('Y/m/d');
        }

        if (!empty($variation_data['date_on_sale_to'])) {
            $variation_data['date_on_sale_to'] = $variation_data['date_on_sale_to']->date('Y/m/d');
        }

        return $this->make_response([
            'success' => true,
            'variation' => $variation_data,
        ]);
    }

    public function history_change_page()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['page'])) {
            return false;
        }

        $where = [];

        if (isset($_POST['filters'])) {
            if (isset($_POST['filters']['operation']) && !empty($_POST['filters']['operation'])) {
                $where['operation_type'] = sanitize_text_field(wp_unslash($_POST['filters']['operation']));
            }
            if (isset($_POST['filters']['author']) && !empty($_POST['filters']['author'])) {
                $where['user_id'] = sanitize_text_field(wp_unslash($_POST['filters']['author']));
            }
            if (isset($_POST['filters']['fields']) && !empty($_POST['filters']['fields'])) {
                $where['fields'] = (is_array($_POST['filters']['fields'])) ? Sanitizer::array($_POST['filters']['fields']) : sanitize_text_field(wp_unslash($_POST['filters']['fields'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            }
            if (isset($_POST['filters']['date'])) {
                $where['operation_date'] = sanitize_text_field(wp_unslash($_POST['filters']['date']));
            }
        }

        $per_page = 10;
        $history_count = $this->history_repository->get_history_count($where);
        $current_page = intval($_POST['page']);
        $offset = intval($current_page - 1) * $per_page;
        $histories = $this->history_repository->get_histories($where, $per_page, $offset);
        $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
        $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count', 'per_page', 'current_page'));

        $this->make_response([
            'success' => true,
            'history_items' => $histories_rendered,
            'history_pagination' => $history_pagination,
        ]);
    }

    public function get_products_name()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $list['results'] = [];
        if (!empty($_POST['search'])) {
            $products = $this->product_repository->get_products([
                'posts_per_page' => 50,
                'post_status' => 'any',
                's' => sanitize_text_field(wp_unslash($_POST['search'])),
            ]);

            if (!empty($products->posts)) {
                foreach ($products->posts as $post) {
                    $product = $this->product_repository->get_product($post->ID);
                    if ($product instanceof \WC_Product) {
                        $list['results'][] = [
                            'id' => $product->get_id(),
                            'text' => $product->get_title(),
                        ];
                    }
                }
            }
        }

        $this->make_response($list);
    }

    public function create_new_product()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $product_data = (!empty($_POST['productData'])) ? Sanitizer::array($_POST['productData']) : []; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

        if (!empty($_POST['count'])) {
            $count_input = intval(wp_unslash($_POST['count']));
            if ($count_input > 100 && ProductBackgroundProcess::is_enable()) {
                $max = 50;
                $background_process = ProductBackgroundProcess::get_instance();
                if ($background_process->is_not_queue_empty()) {
                    $this->make_response([
                        'success' => false,
                        'message' => esc_html__('Another operation is running. It is not possible to edit the information at this moment. Please wait until it finishes or set a scheduled task.', 'ithemeland-woo-bulk-product-editor-lite'),
                    ]);
                }
                $round = ceil($count_input / $max);
                for ($i = 1; $i <= intval($round); $i++) {
                    if ($i == $round) {
                        $count = intval($count_input) - (($round - 1) * $max);
                    } else {
                        $count = $max;
                    }

                    $background_process->push_to_queue([
                        'handler' => 'product_create',
                        'count' => $count,
                        'product_data' => $product_data
                    ]);
                    $background_process->save();
                }

                $background_process->set_total_tasks(intval($_POST['count']));
                $background_process->start();

                $this->make_response([
                    'success' => true,
                    'is_processing' => true,
                    'total_tasks' => intval($_POST['count']),
                    'completed_tasks' => 0,
                ]);
            } else {
                $products = [];
                for ($i = 1; $i <= intval($_POST['count']); $i++) {
                    $products[] = $this->product_repository->create($product_data);
                }
                $this->make_response([
                    'success' => true,
                    'product_ids' => $products,
                ]);
            }
        }
    }

    public function get_product_variations()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['product_id'])) {
            $variations_output = '';
            $attributes_output = '';
            $individual_output = '';
            $variations_single_delete_output = '';
            $product = $this->product_repository->get_product(intval($_POST['product_id']));
            if (!($product instanceof \WC_Product) || $product->get_type() != 'variable') {
                return false;
            }

            $product_attributes = $product->get_attributes();
            if (!empty($product_attributes)) {
                foreach ($product_attributes as $key => $product_attribute) {
                    if (!($product_attribute instanceof \WC_Product_Attribute)) {
                        continue;
                    }

                    if ($product_attribute->get_variation() !== true) {
                        continue;
                    }

                    $selected_values = [];
                    $selected_items[] = urldecode(mb_substr($key, 3));
                    $attribute_selected_items = get_the_terms($product->get_id(), urldecode($key));
                    $attribute_name = mb_substr(urldecode($key), 3);
                    if (is_array($attribute_selected_items)) {
                        $individual_output .= "<div data-id='wcbe-variation-bulk-edit-attribute-item-{$attribute_name}'><select class='wcbe-variation-bulk-edit-manual-item' data-attribute-name='{$attribute_name}'>";
                        foreach ($attribute_selected_items as $attribute_selected_item) {
                            $selected_values[] = urldecode($attribute_selected_item->slug);
                            $individual_output .= "<option value='" . urldecode($attribute_selected_item->slug) . "'>{$attribute_selected_item->name}</option>";
                        }
                        $individual_output .= '</select></div>';
                    }
                    $values = get_terms(['taxonomy' => urldecode($key), 'hide_empty' => false]);
                    $attributes_output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/attribute_item.php', compact('selected_values', 'attribute_name', 'values'));
                }
            }
            $product_children = $product->get_children();
            if ($product_children > 0) {
                $default_variation = implode(' | ', array_map('urldecode', $product->get_default_attributes()));
                $i = 1;
                foreach ($product_children as $child) {
                    $variation = $this->product_repository->get_product(intval($child));
                    if ($variation->get_status() == 'trash') {
                        continue;
                    }
                    $variation_id = $variation->get_id();
                    $attributes = $variation->get_attributes();
                    $val = [];
                    $variation_attributes_labels = [];
                    if (!empty($attributes)) {
                        foreach ($attributes as $key => $attribute) {
                            $val[] = str_replace('pa_', '', $key) . ',' . $attribute;
                            $variation_attributes_labels[] = (!empty($attribute)) ? urldecode($attribute) : 'Any ' . urldecode($key);
                        }
                    }
                    $variation_attributes = (!empty($variation_attributes_labels)) ? implode(' | ', $variation_attributes_labels) : '';
                    $attribute_value = implode('&&', $val);
                    $variations_output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/variation_item.php', compact('variation_attributes', 'default_variation', 'attribute_value', 'variation_id'));
                    $variations_single_delete_output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/variation_item_single_delete.php', compact('variation_attributes', 'variation_id'));
                    $i++;
                }
            }

            $this->make_response([
                'success' => true,
                'variations' => $variations_output,
                'attributes' => $attributes_output,
                'individual' => $individual_output,
                'selected_items' => $selected_items,
                'variations_single_delete' => $variations_single_delete_output,
            ]);
        }

        return false;
    }

    public function set_products_variations()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_ids']) && !is_array($_POST['product_ids']) || empty($_POST['variations']) || !is_array($_POST['variations'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $product_ids = array_map('intval', $_POST['product_ids']);

        $attributes = [];
        if (!empty($_POST['attributes'])) {
            foreach ($_POST['attributes'] as $attribute_item) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                if (!isset($attribute_item[0]) && !isset($attribute_item[1])) {
                    continue;
                }

                $attributes[] = [
                    'name' => 'pa_' . sanitize_text_field($attribute_item[0]),
                    'type' => 'taxonomy',
                    'value' => (is_array($attribute_item[1])) ? array_map('intval', $attribute_item[1]) : [],
                    'operator' => 'taxonomy_replace',
                    'used_for_variations' => 'yes',
                    'attribute_is_visible' => 'yes',
                ];
            }
        }

        $new_variations = [];
        $variations_key = 0;
        foreach ($_POST['variations'] as $variations_item) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            if (isset($variations_item[0]) && !empty($variations_item[0])) {
                $new_variations[$variations_key]['variation_id'] = (!empty($variations_item[1])) ? intval($variations_item[1]) : '';

                $variations = explode('&&', $variations_item[0]);
                if (!is_array($variations) && empty($variations)) {
                    continue;
                }
                foreach ($variations as $variation_item) {
                    $variation = explode(',', $variation_item);
                    if (isset($variation[0]) && isset($variation[1])) {
                        $key = sanitize_text_field(strtolower(urlencode($variation[0])));
                        $new_variations[$variations_key]['items']['attribute_pa_' . $key] = sanitize_text_field(strtolower(urlencode($variation[1])));
                    }
                }
            }
            $variations_key++;
        }

        $default_variations = (isset($_POST['default_variation'])) ? sanitize_text_field(wp_unslash($_POST['default_variation'])) : null;
        if (!empty($default_variations)) {
            $default_variation_items = explode('&&', $default_variations);
            if (!is_array($default_variation_items) && empty($default_variation_items)) {
                return false;
            }

            foreach ($default_variation_items as $default_variation_item) {
                $default_variation = explode(',', $default_variation_item);
                if (isset($default_variation[0]) && isset($default_variation[1])) {
                    $key = strtolower(urlencode($default_variation[0]));
                    $default_attributes["pa_{$key}"] = strtolower(urlencode($default_variation[1]));
                }
            }
        }

        $update_service = Update_Service::get_instance();
        $update_service->set_update_data([
            'update_type' => 'variation',
            'product_ids' => $product_ids,
            'product_data' => [
                [
                    'name' => 'bulk_variation_update',
                    'type' => 'variation',
                    'value' => [
                        'product_type' => 'variable',
                        'attributes' => $attributes,
                        'variations' => $new_variations,
                        'default_variation' => (!empty($default_attributes)) ? $default_attributes : null,
                    ]
                ]
            ],
            'save_history' => true,
        ]);

        $update_result = $update_service->perform();

        $histories = $this->history_repository->get_histories();
        $history_count = $this->history_repository->get_history_count();
        $reverted = $this->history_repository->get_latest_reverted();
        $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
        $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

        $this->make_response([
            'success' => $update_result,
            'reverted' => !empty($reverted),
            'history_items' => $histories_rendered,
            'history_pagination' => $history_pagination,
        ]);
    }

    public function delete_products_variations()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['product_ids']) && is_array($_POST['product_ids']) && !empty($_POST['variations']) && !empty($_POST['delete_type'])) {
            $variations = [];

            if (isset($_POST['variations']) && is_array($_POST['variations'])) {
                $variations = array_map('intval', $_POST['variations']);
            }

            foreach ($_POST['product_ids'] as $product_id) {  //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $product = $this->product_repository->get_product(intval($product_id));
                if (!($product instanceof \WC_Product_Variable) || $product->get_type() != 'variable') {
                    return false;
                }
                $product_variations = $product->get_children();
                if (count($product_variations) > 0) {
                    foreach ($product_variations as $variation_id) {
                        $variation = $this->product_repository->get_product(intval($variation_id));
                        if (!($variation instanceof \WC_Product_Variation)) {
                            return false;
                        }
                        switch ($_POST['delete_type']) {
                            case 'all_variations':
                                wp_delete_post(intval($variation->get_id()), true);
                                break;
                            case 'single_product':
                                if (is_array($variations) && in_array($variation_id, $variations)) {
                                    wp_delete_post(intval($variation->get_id()), true);
                                }
                                break;
                            case 'multiple_product':
                                $delete_variation = Others::array_flatten($variations);
                                $product_variation = $variation->get_variation_attributes();
                                if (Others::array_equal($delete_variation, $product_variation)) {
                                    wp_delete_post(intval($variation->get_id()), true);
                                }
                                break;
                        }
                    }
                }
            }
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function delete_products()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['delete_type'])) {
            $this->make_response([
                'success' => false,
                'message' => esc_html__('Delete type is required', 'ithemeland-woo-bulk-product-editor-lite'),
            ]);
        }

        $product_ids = [];
        global $wpdb;

        $delete_type = sanitize_text_field(wp_unslash($_POST['delete_type']));
        $post_type = 'product'; // We are dealing with products, so post_type is 'product'.
        $selected_product_ids = isset($_POST['product_ids']) ? array_map('intval', $_POST['product_ids']) : [];

        // Handle case when all products or filtered products are selected
        if ($delete_type == 'all' || ($selected_product_ids == 'all_filtered' && !is_array($_POST['product_ids'])) && !empty($_POST['filter_data'])) {
            // Similar to the posts case, fetch filtered products if 'all_filtered' is selected
            $filter_service = Product_Filter_Service::get_instance();
            $products = $filter_service->get_filtered_products(Sanitizer::array($_POST['filter_data']), [ //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                'posts_per_page' => '-1',
                'fields' => 'ids',
                'post_type' => [$post_type],
            ]);
            $product_ids = $products['product_ids'];
        }

        if (empty($product_ids)) {
            $product_ids = !empty($selected_product_ids) ? $selected_product_ids : [];
        }

        // Condition: If selected_product_ids exist, filter within them
        $product_filter = !empty($selected_product_ids) ? "AND ID IN (" . implode(',', $selected_product_ids) . ")" : "";
        $product = Product::get_instance();
        // Handle delete operations similar to the post deletion
        switch ($delete_type) {
            case 'dupoldest_title':
            case 'duplatest_title':
            case 'dupoldest_content':
            case 'duplatest_content':
            case 'dupoldest_title_content':
            case 'duplatest_title_content':
                $product_ids = $product->delete_duplicates($wpdb, $post_type, $delete_type, $product_filter);
                break;
        }

        if (empty($product_ids)) {
            $this->make_response([
                'success' => false,
                'message' => esc_html__('No duplicate products found', 'ithemeland-woo-bulk-product-editor-lite'),
            ]);
        }

        $product_delete = new ProductDeleteService();
        $product_delete->perform($product_ids, $delete_type);

        if ($product_delete->is_processing()) {
            $this->make_response([
                'success' => true,
                'is_processing' => true,
                'total_tasks' => count($product_ids),
                'completed_tasks' => 0,
            ]);
        } else {
            $histories = $this->history_repository->get_histories();
            $history_count = $this->history_repository->get_history_count();
            $reverted = $this->history_repository->get_latest_reverted();
            $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

            $this->make_response([
                'success' => true,
                'message' => esc_html__('Products deleted successfully!', 'ithemeland-woo-bulk-product-editor-lite'),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'reverted' => !empty($reverted),
                'edited_ids' => $product_ids,
            ]);
        }
        return false;
    }



    public function untrash_products()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $product_ids = (!empty($_POST['product_ids']) && is_array($_POST['product_ids'])) ? array_map('intval', $_POST['product_ids']) : $this->product_repository->get_trash();
        if (empty($product_ids) || !is_array($product_ids)) {
            $this->make_response([
                'success' => false,
                'message' => esc_html__('Product not found !', 'ithemeland-woo-bulk-product-editor-lite'),
            ]);
        }

        $max = 100;
        if (count($product_ids) > $max && ProductBackgroundProcess::is_enable()) {
            $background_process = ProductBackgroundProcess::get_instance();
            if ($background_process->is_not_queue_empty()) {
                $this->make_response([
                    'success' => false,
                    'message' => esc_html__('Another operation is running. It is not possible to edit the information at this moment. Please wait until it finishes or set a scheduled task.', 'ithemeland-woo-bulk-product-editor-lite'),
                ]);
            }

            $ids = array_chunk($product_ids, $max);
            foreach ($ids as $items) {
                if (empty($items) || !is_array($items)) {
                    continue;
                }
                $background_process->push_to_queue([
                    'handler' => 'product_restore',
                    'product_ids' => array_map('intval', $items),
                ]);
                $background_process->save();
            }

            $background_process->set_total_tasks(count($product_ids));
            $background_process->start();
            $this->make_response([
                'success' => true,
                'is_processing' => true,
                'total_tasks' => count($product_ids),
                'completed_tasks' => 0,
            ]);
        } else {
            foreach ($product_ids as $product_id) {
                wp_untrash_post(intval($product_id));
            }
            $this->make_response([
                'success' => true,
                'message' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            ]);
        }
    }

    public function empty_trash()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $product_ids = $this->product_repository->get_trash();
        if (empty($product_ids)) {
            $this->make_response([
                'success' => false,
                'message' => esc_html__('Product not found !', 'ithemeland-woo-bulk-product-editor-lite'),
            ]);
        }

        $product_delete = new ProductDeleteService();
        $product_delete->perform($product_ids, 'permanently');

        $this->make_response([
            'success' => true,
            'message' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            'is_processing' => $product_delete->is_processing(),
            'total_tasks' => count($product_ids),
            'completed_tasks' => 0,
        ]);
    }

    public function duplicate_product()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_ids']) || !is_array($_POST['product_ids']) || empty($_POST['count'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $product_duplicate = new ProductDuplicateService();
        $product_duplicate->perform([
            'product_ids' => array_map('intval', $_POST['product_ids']),
            'count' => intval($_POST['count']),
        ]);

        $this->make_response([
            'success' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            'is_processing' => $product_duplicate->is_processing(),
            'total_tasks' => count($_POST['product_ids']) * intval($_POST['count']),
            'completed_tasks' => 0,
        ]);
    }

    public function add_product_taxonomy()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['taxonomy_info']) && !empty($_POST['taxonomy_name']) && !empty($_POST['taxonomy_info']['name']) && !empty($_POST['taxonomy_info']['product_id'])) {
            $result = wp_insert_category([
                'taxonomy' => sanitize_text_field(wp_unslash($_POST['taxonomy_name'])),
                'cat_name' => sanitize_text_field(wp_unslash($_POST['taxonomy_info']['name'])),
                'category_nicename' => (!empty($_POST['taxonomy_info']['slug'])) ? sanitize_text_field(wp_unslash($_POST['taxonomy_info']['slug'])) : '',
                'category_description' => (!empty($_POST['taxonomy_info']['description'])) ? sanitize_text_field(wp_unslash($_POST['taxonomy_info']['description'])) : '',
                'category_parent' => (isset($_POST['taxonomy_info']['parent'])) ? intval($_POST['taxonomy_info']['parent']) : 0,
            ]);
            $checked = wp_get_post_terms(intval($_POST['taxonomy_info']['product_id']), sanitize_text_field(wp_unslash($_POST['taxonomy_name'])), [
                'fields' => 'ids',
            ]);
            if (!empty($result)) {
                $taxonomy_items = Taxonomy::wcbe_product_taxonomy_list(sanitize_text_field(wp_unslash($_POST['taxonomy_name'])), $checked);
                $this->make_response([
                    'success' => true,
                    'product_id' => intval($_POST['taxonomy_info']['product_id']),
                    'taxonomy_items' => $taxonomy_items,
                ]);
            }
        }
    }

    public function add_product_attribute()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['attribute_info']) && !empty($_POST['attribute_name']) && !empty($_POST['attribute_info']['name']) && !empty($_POST['attribute_info']['product_id'])) {
            $result = wp_insert_category([
                'taxonomy' => sanitize_text_field(wp_unslash($_POST['attribute_name'])),
                'cat_name' => sanitize_text_field(wp_unslash($_POST['attribute_info']['name'])),
                'category_nicename' => (!empty($_POST['attribute_info']['slug'])) ? sanitize_text_field(wp_unslash($_POST['attribute_info']['slug'])) : '',
                'category_description' => (!empty($_POST['attribute_info']['description'])) ? sanitize_text_field(wp_unslash($_POST['attribute_info']['description'])) : '',
            ]);
            $items = get_terms([
                'taxonomy' => sanitize_text_field(wp_unslash($_POST['attribute_name'])),
                'hide_empty' => false,
            ]);
            $product_terms = wp_get_post_terms(intval($_POST['attribute_info']['product_id']), sanitize_text_field(wp_unslash($_POST['attribute_name'])), [
                'fields' => 'ids',
            ]);
            $attribute_items = '';
            if (!empty($items)) {
                foreach ($items as $item) {
                    $checked = (is_array($product_terms) && in_array($item->term_id, $product_terms)) ? 'checked="checked"' : '';
                    $attribute_items .= "<li><label><input type='checkbox' class='wcbe-inline-edit-tax' data-field='value' value='{$item->term_id}' {$checked}>{$item->name}</label></li>";
                }
            }
            if (!empty($result)) {
                $this->make_response([
                    'success' => true,
                    'product_id' => intval($_POST['attribute_info']['product_id']),
                    'attribute_items' => $attribute_items,
                ]);
            }
        }
        return false;
    }

    public function add_new_term()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['attribute_name']) || empty($_POST['term_name'])) {
            return $this->make_response([
                'success' => false
            ]);
        }

        $taxonomy = 'pa_' . sanitize_text_field(wp_unslash($_POST['attribute_name']));
        $term_id = wp_insert_category([
            'taxonomy' => $taxonomy,
            'cat_name' => sanitize_text_field(wp_unslash($_POST['term_name'])),
        ]);

        if ($term_id) {
            $attribute_term = get_term_by('term_id', intval($term_id), $taxonomy);
            $new_term_html = Render::html(WCBEL_VIEWS_DIR . "variations/add_variations/term-item.php", compact('attribute_term'));
        }

        return ($term_id) ? $this->make_response([
            'success' => true,
            'new_term_html' => $new_term_html,
        ]) : $this->make_response([
            'success' => false
        ]);
    }

    public function add_variations()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['variable_ids']) || empty($_POST['attributes']) || empty($_POST['variations'])) {
            return $this->make_response([
                'success' => false
            ]);
        }

        $variable_ids = [];
        if (is_array($_POST['variable_ids']) && !empty($_POST['variable_ids'])) {
            $variable_ids = array_map('intval', $_POST['variable_ids']);
        }

        if (is_string($_POST['variable_ids']) && $_POST['variable_ids'] == 'all_filtered') {
            $variable_ids = $this->get_all_filtered();
        }

        $attributes = Sanitizer::array(json_decode(wp_unslash($_POST['attributes']), true)); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $variations = Sanitizer::array(json_decode(wp_unslash($_POST['variations']), true)); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

        if (empty($attributes) || empty($variations)) {
            return $this->make_response([
                'success' => false
            ]);
        }

        $update_service = Update_Service::get_instance();
        $update_service->set_update_data([
            'update_type' => 'variation',
            'product_ids' => $variable_ids,
            'product_data' => [
                [
                    'name' => 'bulk_variation_update',
                    'type' => 'variation',
                    'action' => 'add_variations',
                    'value' => [
                        'product_type' => 'variable',
                        'attributes' => Sanitizer::array($attributes), //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                        'variations' => Sanitizer::array($variations), //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    ]
                ]
            ],
            'save_history' => true,
        ]);

        $update_result = $update_service->perform();

        if ($update_service->is_processing()) {
            $this->make_response([
                'success' => $update_result,
                'product_ids' => $variable_ids,
                'is_processing' => $update_service->is_processing(),
                'total_tasks' => count($variable_ids),
                'completed_tasks' => 0,
            ]);
        } else {
            $histories = $this->history_repository->get_histories();
            $history_count = $this->history_repository->get_history_count();
            $reverted = $this->history_repository->get_latest_reverted();
            $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

            $this->make_response([
                'success' => $update_result,
                'reverted' => !empty($reverted),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
            ]);
        }
    }

    public function get_attribute_values()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['attribute_name'])) {
            $output = '';
            $attribute_name = sanitize_text_field(wp_unslash($_POST['attribute_name']));
            $values = get_terms([
                'taxonomy' => "pa_{$attribute_name}",
                'hide_empty' => false,
            ]);

            if (!empty($values) && count($values) > 0) {
                $output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/attribute_item.php', compact('values', 'attribute_name'));
            }

            $this->make_response([
                'success' => true,
                'attribute_item' => $output,
            ]);
        }
        return false;
    }

    public function get_attribute_values_for_delete()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['attribute_name'])) {
            $output = '';
            $attribute_name = sanitize_text_field(wp_unslash($_POST['attribute_name']));
            $values = get_terms([
                'taxonomy' => "pa_{$attribute_name}",
                'hide_empty' => false,
            ]);

            if (!empty($values) && count($values) > 0) {
                $output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/attribute_item_for_delete.php', compact('values', 'attribute_name'));
            }

            $this->make_response([
                'success' => true,
                'attribute_item' => $output,
            ]);
        }
        return false;
    }

    public function get_attribute_values_for_attach()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['attribute_name'])) {
            $output = '';
            $attribute_name = sanitize_text_field(wp_unslash($_POST['attribute_name']));
            $values = get_terms([
                'taxonomy' => "pa_{$attribute_name}",
                'hide_empty' => false,
            ]);

            if (!empty($values) && count($values) > 0) {
                $output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/attribute_item_for_attach.php', compact('values', 'attribute_name'));
            }

            $this->make_response([
                'success' => true,
                'attribute_items' => $output,
            ]);
        }
        return false;
    }

    public function load_filter_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $search_repository = Search::get_instance();
        if (isset($_POST['preset_key'])) {

            $preset = $search_repository->get_preset(sanitize_text_field(wp_unslash($_POST['preset_key'])));
            if (!isset($preset['filter_data'])) {
                return false;
            }
            $search_repository->update_current_data([
                'last_filter_data' => $preset['filter_data'],
            ]);

            $product_table = ProductTable::prepare([
                'filter_data' => $preset['filter_data'],
                'args' => [
                    'paged' => 1
                ]
            ]);

            $this->make_response([
                'success' => true,
                'filter_data' => $preset['filter_data'],
                'option_values' => $search_repository->get_option_values(),
                'rows' => $product_table->get_rows(),
                'status_filters' => $product_table->get_status_filters(),
                'pagination' => $product_table->get_pagination(),
                'products_count' => $product_table->get_found_items(),
            ]);
        }
        return false;
    }

    public function delete_filter_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            $search_repository = Search::get_instance();
            $delete_result = $search_repository->delete(sanitize_text_field(wp_unslash($_POST['preset_key'])));
            if (!$delete_result) {
                return false;
            }

            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function save_column_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key']) && isset($_POST['type'])) {
            $column_repository = Column::get_instance();
            $fields = $column_repository->get_columns();
            $preset['date_modified'] = gmdate('Y-m-d H:i:s', time());

            switch ($_POST['type']) {
                case 'save_as_new':
                    $preset['name'] = "Preset " . random_int(100, 999);
                    $preset['key'] = 'preset-' . random_int(1000000, 9999999);
                    break;
                case 'update_changes':
                    $preset_item = $column_repository->get_preset(sanitize_text_field(wp_unslash($_POST['preset_key'])));
                    if (!$preset_item) {
                        return false;
                    }
                    $preset['name'] = sanitize_text_field($preset_item['name']);
                    $preset['key'] = sanitize_text_field($preset_item['key']);
                    break;
            }

            $preset['fields'] = [];
            if (!empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    if (isset($fields[$item])) {
                        $preset['fields'][$item] = [
                            'name' => sanitize_text_field($item),
                            'label' => sanitize_text_field($fields[$item]['label']),
                            'title' => sanitize_text_field($fields[$item]['label']),
                            'editable' => ($fields[$item]['editable']) ? true : false,
                            'content_type' => sanitize_text_field($fields[$item]['content_type']),
                            'allowed_type' => Sanitizer::array($fields[$item]['allowed_type']), //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                            'update_type' => sanitize_text_field($fields[$item]['update_type']),
                            'fetch_type' => sanitize_text_field($fields[$item]['fetch_type']),
                            'background_color' => '#fff',
                            'text_color' => '#444',
                        ];
                        if (isset($fields[$item]['sortable'])) {
                            $preset["fields"][$item]['sortable'] = $fields[$item]['sortable'];
                        }
                        if (isset($fields[$item]['sub_name'])) {
                            $preset["fields"][$item]['sub_name'] = $fields[$item]['sub_name'];
                        }
                        if (isset($fields[$item]['options'])) {
                            $preset["fields"][$item]['options'] = $fields[$item]['options'];
                        }
                        if (isset($fields[$item]['field_type'])) {
                            $preset["fields"][$item]['field_type'] = $fields[$item]['field_type'];
                        }
                        $preset['checked'][] = $item;
                    }
                }
            }

            $column_repository->update($preset);
            $column_repository->set_active_columns($preset['key'], $preset['fields']);
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function get_text_editor_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id']) || empty($_POST['field']) || empty($_POST['fetch_type'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        if (isset($_POST['product_id']) && isset($_POST['field'])) {
            $product_object = $this->product_repository->get_product(intval($_POST['product_id']));
            if (!($product_object instanceof \WC_Product)) {
                return false;
            }
            $value = $this->product_repository->get_product_field($product_object, sanitize_text_field(wp_unslash($_POST['fetch_type'])), sanitize_text_field(wp_unslash($_POST['field'])));
            $this->make_response([
                'success' => true,
                'content' => $value,
            ]);
        }
        return false;
    }


    public function history_filter()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['filters'])) {
            $where = [];
            if (isset($_POST['filters']['operation']) && !empty($_POST['filters']['operation'])) {
                $where['operation_type'] = sanitize_text_field(wp_unslash($_POST['filters']['operation']));
            }
            if (isset($_POST['filters']['author']) && !empty($_POST['filters']['author'])) {
                $where['user_id'] = sanitize_text_field(wp_unslash($_POST['filters']['author']));
            }
            if (isset($_POST['filters']['fields']) && !empty($_POST['filters']['fields'])) {
                $where['fields'] = (is_array($_POST['filters']['fields'])) ? Sanitizer::array($_POST['filters']['fields']) : sanitize_text_field(wp_unslash($_POST['filters']['fields'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            }
            if (isset($_POST['filters']['date'])) {
                $where['operation_date'] = sanitize_text_field(wp_unslash($_POST['filters']['date']));
            }

            $histories = $this->history_repository->get_histories($where);
            $history_count = $this->history_repository->get_history_count($where);

            $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));
            $reverted = $this->history_repository->get_latest_reverted();

            $this->make_response([
                'success' => true,
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'reverted' => !empty($reverted),
            ]);
        }
        return false;
    }

    public function change_count_per_page()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['count_per_page'])) {
            $setting_repository = Setting::get_instance();
            $setting_repository->update([
                'count_per_page' => (intval($_POST['count_per_page']) > Setting::MAX_COUNT_PER_PAGE) ? Setting::MAX_COUNT_PER_PAGE : intval($_POST['count_per_page']),
            ]);
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function filter_profile_change_use_always()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            $search_repository = Search::get_instance();
            $search_repository->update_use_always(sanitize_text_field(wp_unslash($_POST['preset_key'])));
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function history_undo()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!defined('WCBE_ACTIVE')) {
            $this->make_response([
                'success' => true,
                'wcbe' => false,
                'message' => 'Pro Version!'
            ]);

            return false;
        }

        $latest_history = $this->history_repository->get_latest_history();
        if (!empty($latest_history[0]) && !empty($latest_history[0]->id)) {
            $history_undo_service = new HistoryUndoService();
            $history_undo_service->set_data(['history_id' => intval($latest_history[0]->id)]);
            $history_undo_service->perform();

            $histories = $this->history_repository->get_histories();
            $history_count = $this->history_repository->get_history_count();
            $reverted = $this->history_repository->get_latest_reverted();
            $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

            $this->make_response([
                'success' => true,
                'is_processing' => $history_undo_service->is_processing(),
                'total_tasks' => $history_undo_service->get_total_tasks(),
                'completed_tasks' => 0,
                'product_ids' => $history_undo_service->get_product_ids(),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'reverted' => !empty($reverted),
            ]);
        }

        return false;
    }

    public function history_redo()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!defined('WCBE_ACTIVE')) {
            $this->make_response([
                'success' => true,
                'wcbe' => false,
                'message' => 'Pro Version!'
            ]);

            return false;
        }

        $latest_history = $this->history_repository->get_latest_reverted();
        if (!empty($latest_history[0]) && !empty($latest_history[0]->id)) {
            $history_redo_service = new HistoryRedoService();
            $history_redo_service->set_data(['history_id' => intval($latest_history[0]->id)]);
            $history_redo_service->perform();

            $histories = $this->history_repository->get_histories();
            $history_count = $this->history_repository->get_history_count();
            $reverted = $this->history_repository->get_latest_reverted();
            $histories_rendered = Render::html(WCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

            $this->make_response([
                'success' => true,
                'is_processing' => $history_redo_service->is_processing(),
                'total_tasks' => $history_redo_service->get_total_tasks(),
                'completed_tasks' => 0,
                'product_ids' => $history_redo_service->get_product_ids(),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'reverted' => !empty($reverted),
            ]);
        }

        return false;
    }

    public function get_taxonomy_parent_select_box()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['taxonomy']) && $_POST['taxonomy'] != 'product_tag') {
            $taxonomies = get_terms(['taxonomy' => sanitize_text_field(wp_unslash($_POST['taxonomy'])), 'hide_empty' => false]);
            $options = '<option value="-1">None</option>';
            if (!empty($taxonomies)) {
                foreach ($taxonomies as $taxonomy) {
                    $term_id = intval($taxonomy->term_id);
                    $taxonomy_name = sanitize_text_field($taxonomy->name);
                    $options .= "<option value='{$term_id}'>{$taxonomy_name}</option>";
                }
            }
            $this->make_response([
                'success' => true,
                'options' => $options,
            ]);
        }
        return false;
    }

    public function get_product_data()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['product_id'])) {
            $product_object = $this->product_repository->get_product(intval($_POST['product_id']));
            if ($product_object instanceof \WC_Product) {
                $this->make_response([
                    'success' => true,
                    'product_data' => $product_object->get_data(),
                ]);
            }
        }

        $this->make_response([
            'success' => false,
        ]);
    }

    public function get_product_by_ids()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_ids']) || !is_array($_POST['product_ids'])) {
            return false;
        }

        $products = [];
        $product_object = $this->product_repository->get_product_object_by_ids(['include' => array_map('intval', $_POST['product_ids'])]);
        if (!empty($product_object)) {
            foreach ($product_object as $product) {
                $products[$product->get_id()] = $product->get_title();
            }
        }

        $this->make_response([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function get_product_variations_for_attach()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['product_id']) && !empty($_POST['attribute']) && !empty($_POST['attribute_item'])) {
            $product = $this->product_repository->get_product(intval($_POST['product_id']));
            if (!($product instanceof \WC_Product_Variable)) {
                return false;
            }

            $variations = '';
            $attribute_items = get_terms(['taxonomy' => 'pa_' . sanitize_text_field(wp_unslash($_POST['attribute']))]);
            $attribute_item = sanitize_text_field(wp_unslash($_POST['attribute_item']));
            $product_children = $product->get_children();
            if ($product_children > 0) {
                foreach ($product_children as $child) {
                    $variation = $this->product_repository->get_product(intval($child));
                    if ($variation->get_status() == 'trash') {
                        continue;
                    }
                    $variation_id = $variation->get_id();
                    $attributes = $variation->get_attributes();
                    $variation_attributes_labels = [];
                    if (!empty($attributes)) {
                        foreach ($attributes as $key => $attribute) {
                            $variation_attributes_labels[] = (!empty($attribute)) ? urldecode($attribute) : 'Any ' . urldecode($key);
                        }
                    }
                    $variation_attributes = (!empty($variation_attributes_labels)) ? implode(' | ', $variation_attributes_labels) : '';
                    $variations .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/variation_item_for_attach.php', compact('variation_attributes', 'variation_id', 'attribute_items', 'attribute_item'));
                }
            }

            $this->make_response([
                'success' => true,
                'variations' => $variations,
            ]);
        }
        return false;
    }

    public function get_product_files()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['product_id'])) {
            $output = '';
            $product = $this->product_repository->get_product(intval($_POST['product_id']));
            if (!($product instanceof \WC_Product)) {
                return false;
            }
            $files = $product->get_downloads();
            if (!empty($files)) {
                foreach ($files as $file_item) {
                    $file_id = $file_item->get_id();
                    $output .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/file_item.php', compact('file_item', 'file_id'));
                }

                $this->make_response([
                    'success' => true,
                    'files' => $output,
                ]);
            }
            return false;
        }
        return false;
    }

    public function add_new_file_item()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $file_id = md5(time() . random_int(100, 999));
        $output = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/file_item.php', compact('file_id'));
        $this->make_response([
            'success' => true,
            'file_item' => $output,
        ]);

        return false;
    }

    public function variation_attaching()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['variation_id']) && !empty($_POST['attribute_key']) && !empty($_POST['attribute_item'])) {
            $product_repository = Product::get_instance();
            $attribute_key = 'pa_' . sanitize_text_field(wp_unslash($_POST['attribute_key']));
            $variation_ids = array_map('intval', $_POST['variation_id']);
            $attribute_items = array_map('intval', $_POST['attribute_item']);

            if (!is_array($variation_ids) && !is_array($attribute_items)) {
                return false;
            }
            for ($i = 0; $i < count($variation_ids); $i++) {
                // save new attribute
                $variation = $product_repository->get_product(intval($variation_ids[$i]));
                if (!($variation instanceof \WC_Product_Variation)) {
                    return false;
                }
                $params = [
                    'field' => $attribute_key,
                    'value' => [intval($attribute_items[$i])],
                    'operator' => 'taxonomy_append',
                    'used_for_variations' => 'yes',
                ];
                $result = $product_repository->product_attribute_update($variation->get_parent_id(), $params);

                // save new combination
                $term = get_term(intval($attribute_items[$i]));
                if ($term instanceof \WP_Term && $result) {
                    $variation = $product_repository->get_product(intval($variation_ids[$i]));
                    $combinations = $variation->get_attributes();
                    $combinations[strtolower(urlencode($attribute_key))] = $term->slug;
                    $variation->set_attributes($combinations);
                    $variation->save();
                }
            }
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function sort_by_column()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['column_name']) && !empty($_POST['sort_type'])) {
            $filter_data = $this->get_filter_data();
            $setting_repository = Setting::get_instance();
            $setting_repository->update([
                'default_sort_by' => sanitize_text_field(wp_unslash($_POST['column_name'])),
                'default_sort' => sanitize_text_field(wp_unslash($_POST['sort_type'])),
            ]);

            $product_table = ProductTable::prepare([
                'filter_data' => $filter_data
            ]);

            $this->make_response([
                'success' => true,
                'filter_data' => $filter_data,
                'rows' => $product_table->get_rows(),
                'status_filters' => $product_table->get_status_filters(),
                'pagination' => $product_table->get_pagination(),
                'products_count' => $product_table->get_found_items(),
            ]);
        }
        return false;
    }

    public function clear_filter_data()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $search_repository = Search::get_instance();
        $search_repository->delete_current_data();
        $this->make_response([
            'success' => true,
        ]);
    }

    public function get_product_badge_ids()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $product_badges = get_post_meta(intval($_POST['product_id']), '_yith_wcbm_product_meta', true);
        $badges = (!empty($product_badges) && is_array($product_badges) && isset($product_badges['id_badge'])) ? $product_badges['id_badge'] : [];

        $this->make_response([
            'success' => true,
            'badges' => $badges,
        ]);
    }

    public function get_product_ithemeland_badge()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $fields = [];
        $badge_fields = get_post_meta(intval($_POST['product_id']), '', true);
        if (!empty($badge_fields)) {
            $ithemeland_badge_fields = $this->product_repository->get_ithemeland_badge_fields();
            foreach ($badge_fields as $key => $value) {
                if (in_array($key, $ithemeland_badge_fields) && isset($value[0])) {
                    $fields[$key] = $value[0];
                }
            }
        }

        $this->make_response([
            'success' => true,
            'badge_fields' => $fields,
        ]);
    }

    public function get_yikes_custom_product_tabs()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $custom_tabs = get_post_meta(intval($_POST['product_id']), 'yikes_woo_products_tabs', true);
        $global_applied = get_option('yikes_woo_reusable_products_tabs_applied');
        $product_global_tabs = (!empty($global_applied[intval($_POST['product_id'])])) ? $global_applied[intval($_POST['product_id'])] : [];
        $text_editor_ids = [];
        $tabs = "";
        if (!empty($custom_tabs)) {
            foreach ($custom_tabs as $tab) {
                $tab_unique_id = 'editor-' . sanitize_text_field($tab['id']) . '-' . random_int(100, 999);
                if (!empty($product_global_tabs)) {
                    foreach ($product_global_tabs as $global_tab) {
                        if (!empty($global_tab['tab_id']) && $tab['id'] == $global_tab['tab_id']) {
                            $tab['global'] = $global_tab['reusable_tab_id'];
                            break;
                        }
                    }
                }
                $text_editor_ids[] = $tab_unique_id;
                $tabs .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/yikes_custom_tab_item.php', compact('tab', 'tab_unique_id'));
            }
        }

        $this->make_response([
            'success' => true,
            'tabs_html' => $tabs,
            'text_editor_ids' => $text_editor_ids,
        ]);
    }

    public function add_yikes_saved_tab()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['tab_id'])) {
            return false;
        }

        $saved_tabs = get_option('yikes_woo_reusable_products_tabs');
        $tab = "";
        $tab_unique_id = "";
        if (!empty($saved_tabs[sanitize_text_field(wp_unslash($_POST['tab_id']))])) {
            $saved_tab = $saved_tabs[sanitize_text_field(wp_unslash($_POST['tab_id']))];
            $tab = [
                'id' => $saved_tab['tab_id'],
                'title' => $saved_tab['tab_title'],
                'content' => $saved_tab['tab_content'],
                'global' => $saved_tab['tab_id'],
            ];
            $tab_unique_id = 'editor-' . sanitize_text_field($tab['id']) . '-' . random_int(100, 999);
            $tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/yikes_custom_tab_item.php', compact('tab', 'tab_unique_id'));
        }

        $this->make_response([
            'success' => true,
            'tab_html' => $tab,
            'text_editor_id' => $tab_unique_id,
        ]);
    }

    public function get_product_gallery_images()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $product = $this->product_repository->get_product(intval($_POST['product_id']));
        if (!($product instanceof \WC_Product)) {
            return false;
        }

        $image_ids = $product->get_gallery_image_ids();
        $images = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/gallery_image.php', compact('image_ids'));

        $this->make_response([
            'success' => true,
            'images' => $images,
        ]);
    }

    public function get_it_wc_role_prices()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $product = $this->product_repository->get_product(intval($_POST['product_id']));
        if (!($product instanceof \WC_Product)) {
            return false;
        }

        $pricing_rules_name = ($product->get_type() == 'variation') ? "pricing_rules_variation" : "pricing_rules_product";
        $prices = get_post_meta($product->get_id(), $pricing_rules_name, true);

        if (empty($prices['price_rule'])) {
            return false;
        }

        $this->make_response([
            'success' => true,
            'prices' => $prices['price_rule'],
        ]);
    }

    public function get_it_wc_dynamic_pricing_selected_roles()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id']) || empty($_POST['field'])) {
            return false;
        }

        $selected_roles = get_post_meta(intval($_POST['product_id']), sanitize_text_field(wp_unslash($_POST['field'])), true);

        $this->make_response([
            'success' => true,
            'roles' => $selected_roles,
        ]);
    }

    public function get_it_wc_dynamic_pricing_all_fields()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            return false;
        }

        $product = $this->product_repository->get_product(intval($_POST['product_id']));

        if (!($product instanceof \WC_Product)) {
            return false;
        }

        $pricing_rules_name = ($product->get_type() == 'variation') ? "pricing_rules_variation" : "pricing_rules_product";

        $this->make_response([
            'success' => true,
            'it_product_disable_discount' => get_post_meta($product->get_id(), "it_product_disable_discount", true),
            'it_product_hide_price_unregistered' => get_post_meta($product->get_id(), "it_product_hide_price_unregistered", true),
            'it_pricing_product_price_user_role' => get_post_meta($product->get_id(), "it_pricing_product_price_user_role", true),
            'it_pricing_product_add_to_cart_user_role' => get_post_meta($product->get_id(), "it_pricing_product_add_to_cart_user_role", true),
            'it_pricing_product_hide_user_role' => get_post_meta($product->get_id(), "it_pricing_product_hide_user_role", true),
            'pricing_rules_product' => get_post_meta($product->get_id(), $pricing_rules_name, true),
        ]);
    }

    public function get_product_custom_field_files()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id']) || empty($_POST['field_name'])) {
            $this->make_response([
                'success' => false
            ]);
        }

        $files = get_post_meta(intval($_POST['product_id']), sanitize_text_field(wp_unslash($_POST['field_name'])), true);
        if (empty($files) || !is_array($files)) {
            $this->make_response([
                'success' => false
            ]);
        }

        $files_html = "";
        foreach ($files as $file) {
            $file_id = md5(time() . random_int(100, 999));
            $files_html .= Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/custom_field_file_item.php', compact('file', 'file_id'));
        }

        $this->make_response([
            'success' => true,
            'files' => $files_html
        ]);
    }

    public function add_custom_field_file_item()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $file_id = md5(time() . random_int(100, 999));
        $output = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/custom_field_file_item.php', compact('file_id'));
        $this->make_response([
            'success' => true,
            'file_item' => $output,
        ]);

        return false;
    }

    public function bulk_edit_add_custom_field_file_item()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $file_id = md5(time() . random_int(100, 999));
        $output = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/custom_field_file_item.php', compact('file_id'));
        $this->make_response([
            'success' => true,
            'file_item' => $output,
        ]);

        return false;
    }

    public function get_bulk_new_tabs_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $new_form_items = $this->new_form_items();

        $taxonomies = $this->product_repository->get_grouped_taxonomies();
        $taxonomies_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/taxonomies.php', [
            'taxonomies' => $taxonomies,
            'new_form_items' => $new_form_items
        ]);
        $acf = ACF_Plugin_Fields::get_instance('product');
        $acf_fields = $acf->get_fields();
        $meta_field_repository = Meta_Field::get_instance();
        $meta_fields = $meta_field_repository->get();
        //$custom_fields_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_form/custom_fields.php', compact('meta_fields', 'acf_fields'));

        $product_statuses = $this->product_repository->get_product_statuses();
        $visibility_items = wc_get_product_visibility_options();

        $new_form_items['general']['status']['options'] = $product_statuses;
        $new_form_items['general']['visibility']['options'] = $visibility_items;
        $general_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/general.php', ['new_form_items' => $new_form_items]);

        $tax_classes = $this->product_repository->get_tax_classes();
        $new_form_items['pricing']['tax_class']['options'] = $tax_classes;
        $pricing_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/pricing.php', [
            'new_form_items' => $new_form_items,
            'tax_classes' => $tax_classes
        ]);

        $shipping_classes = $this->product_repository->get_shipping_classes();

        $new_form_items['shipping']['shipping_class']['options'] = $shipping_classes;
        $shipping_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/shipping.php', [
            'new_form_items' => $new_form_items,
            'shipping_classes' => $shipping_classes // Pass directly to template for compatibility
        ]);

        $stock_statuses = wc_get_product_stock_status_options();
        $backorders = wc_get_product_backorder_options();
        $new_form_items['stock']['stock_status']['options'] = $stock_statuses;
        $new_form_items['stock']['backorders']['options'] = $backorders;
        $stock_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/stock.php', [
            'new_form_items' => $new_form_items
        ]);

        $product_types = wc_get_product_types();
        $new_form_items['type']['product_type']['options'] = $product_types;
        $type_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/type.php', [
            'new_form_items' => $new_form_items
        ]);

        $has_compatible_fields = Compatible_Helper::has_compatible_fields();
        $compatible_fields_status = Compatible_Helper::get_compatible_fields_status();
        $compatible_tabs_label = Compatible_Helper::get_compatible_tabs_label();
        $compatibles = Compatible_Helper::get_compatibles();
        $compatibles_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_new_products_form/compatibles.php', compact('compatibles', 'compatible_tabs_label', 'compatible_fields_status', 'has_compatible_fields'));

        $this->make_response([
            'success' => true,
            'general' => $general_tab,
            'taxonomies' => $taxonomies_tab,
            'pricing' => $pricing_tab,
            'shipping' => $shipping_tab,
            'stock' => $stock_tab,
            'type' => $type_tab,
            'compatibles' => $compatibles_tab,
            //'custom_fields' => $custom_fields_tab,
        ]);
    }

    public function get_bulk_edit_tabs_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $edit_form_items = $this->get_edit_items();

        $taxonomies = $this->product_repository->get_grouped_taxonomies();
        $taxonomies_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/taxonomies.php', compact('taxonomies', 'edit_form_items'));

        $acf = ACF_Plugin_Fields::get_instance('product');
        $acf_fields = $acf->get_fields();
        $meta_field_repository = Meta_Field::get_instance();
        $meta_fields = $meta_field_repository->get();
        $custom_fields_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/custom_fields.php', compact('meta_fields', 'acf_fields'));

        $product_statuses = $this->product_repository->get_product_statuses();
        $visibility_items = wc_get_product_visibility_options();

        $edit_form_items['general']['status']['options'] = array_merge(['' => 'Select'], $product_statuses);
        $edit_form_items['general']['catalog_visibility']['options'] = array_merge(['' => 'Select'], $visibility_items);
        $general_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/general.php', compact('edit_form_items'));

        $tax_classes = ['' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')] + $this->product_repository->get_tax_classes();
        $edit_form_items['pricing']['tax_class']['options'] = array_merge(['' => 'Select'], $tax_classes);
        $pricing_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/pricing.php', compact('edit_form_items'));

        $shipping_classes = $this->product_repository->get_shipping_classes();
        $shipping_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/shipping.php', compact('edit_form_items'));

        $stock_statuses = wc_get_product_stock_status_options();
        $backorders = wc_get_product_backorder_options();

        $edit_form_items['stock']['stock_status']['options'] = array_merge(['' => 'Select'], $stock_statuses);
        $edit_form_items['stock']['backorders']['options'] = array_merge(['' => 'Select'], $backorders);
        $stock_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/stock.php', compact('edit_form_items'));

        $product_types = wc_get_product_types();
        $edit_form_items['type']['product_type']['options'] = array_merge(['' => 'Select'], $product_types);
        $type_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/type.php', compact('edit_form_items'));

        $has_compatible_fields = Compatible_Helper::has_compatible_fields();
        $compatible_fields_status = Compatible_Helper::get_compatible_fields_status();
        $compatible_tabs_label = Compatible_Helper::get_compatible_tabs_label();
        $compatibles = Compatible_Helper::get_compatibles();
        $compatibles_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/compatibles.php', compact('compatibles', 'compatible_tabs_label', 'compatible_fields_status', 'has_compatible_fields'));

        $this->make_response([
            'success' => true,
            'general' => $general_tab,
            'taxonomies' => $taxonomies_tab,
            'pricing' => $pricing_tab,
            'shipping' => $shipping_tab,
            'stock' => $stock_tab,
            'type' => $type_tab,
            'compatibles' => $compatibles_tab,
            'custom_fields' => $custom_fields_tab,
        ]);
    }

    public function get_filter_form_tabs_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }
        $filter_form_items = $this->get_filter_items();

        $taxonomies = $this->product_repository->get_grouped_taxonomies();
        $taxonomies_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/taxonomies.php', compact('taxonomies', 'filter_form_items'));

        $acf = ACF_Plugin_Fields::get_instance('product');
        $acf_fields = $acf->get_fields();
        $meta_field_repository = Meta_Field::get_instance();
        $meta_fields = $meta_field_repository->get();
        $custom_fields_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/custom_fields.php', compact('meta_fields', 'acf_fields', 'filter_form_items'));

        $general_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/general.php',  compact('filter_form_items'));

        $tax_classes = ['' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')] + $this->product_repository->get_tax_classes();
        $pricing_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/pricing.php', compact('tax_classes', 'filter_form_items'));

        $shipping_classes = $this->product_repository->get_shipping_classes();
        $filter_form_items['shipping']['shipping_class']['options'] = $shipping_classes;

        $shipping_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/shipping.php', [
            'filter_form_items' => $filter_form_items,
            'shipping_classes' => $shipping_classes // For backward compatibility
        ]);

        $stock_statuses = ['' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')] + wc_get_product_stock_status_options();
        $backorders = ['' => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite')] + wc_get_product_backorder_options();
        $filter_form_items['stock']['stock_status']['options'] = $stock_statuses;
        $filter_form_items['stock']['backorders']['options'] = $backorders;
        $stock_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/stock.php', [
            'filter_form_items' => $filter_form_items,
            'stock_statuses' => $stock_statuses,
            'backorders' => $backorders
        ]);


        $product_types = wc_get_product_types();
        $product_statuses = $this->product_repository->get_product_statuses();
        $visibility_items = wc_get_product_visibility_options();
        $filter_form_items['type']['product_type']['options'] = $product_types;
        $filter_form_items['type']['product_status']['options'] = $product_statuses;
        $filter_form_items['type']['visibility']['options'] = $visibility_items;

        $type_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/type.php', [
            'filter_form_items' => $filter_form_items,
            'product_types' => $product_types,
            'product_statuses' => $product_statuses,
            'visibility_items' => $visibility_items
        ]);

        $has_compatible_fields = Compatible_Helper::has_compatible_fields();
        $compatible_fields_status = Compatible_Helper::get_compatible_fields_status();
        $compatible_tabs_label = Compatible_Helper::get_compatible_tabs_label();
        $compatibles = Compatible_Helper::get_compatibles();
        $compatibles_tab = Render::html(WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/compatibles.php', compact('compatibles', 'compatible_tabs_label', 'compatible_fields_status', 'has_compatible_fields'));

        $this->make_response([
            'success' => true,
            'general' => $general_tab,
            'taxonomies' => $taxonomies_tab,
            'pricing' => $pricing_tab,
            'shipping' => $shipping_tab,
            'stock' => $stock_tab,
            'type' => $type_tab,
            'compatibles' => $compatibles_tab,
            'custom_fields' => $custom_fields_tab,
        ]);
    }

    public function get_taxonomy_terms()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $list['results'] = [];
        if (!empty($_POST['taxonomy']) && isset($_POST['search'])) {
            $terms = get_terms([
                'taxonomy' => sanitize_text_field(wp_unslash($_POST['taxonomy'])),
                'hide_empty' => false,
                'name__like' => sanitize_text_field(wp_unslash($_POST['search'])),
            ]);
        }

        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (!($term instanceof \WP_Term)) {
                    continue;
                }

                $list['results'][] = [
                    'id' => (isset($_POST['output']) && $_POST['output'] == 'slug') ? $term->slug : $term->term_id,
                    'text' => $term->name
                ];
            }
        }

        $this->make_response($list);
    }

    public function get_more_variations()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();
        $page = (isset($_POST['page'])) ? intval($_POST['page']) : 2;
        $variations_per_page = (!isset($settings['enable_load_more_variations']) || (isset($settings['enable_load_more_variations']) && $settings['enable_load_more_variations'] == 'yes')) ? Product::VARIATIONS_PER_PAGE : -1;
        $product_table = ProductTable::prepare([
            'filter_data' => $this->get_filter_data(),
            'just_variations' => true,
            'args' => [
                'post_parent' => intval($_POST['product_id']),
                'post_type' => 'product_variation',
                'orderby' => ($settings['default_sort_by'] == 'id') ? 'ID' : $settings['default_sort_by'],
                'order' => $settings['default_sort'],
                'posts_per_page' => $variations_per_page,
                'paginate' => true,
                'paged' => $page,
                'post_status' => 'any',
            ],
        ]);

        $this->make_response([
            'success' => true,
            'rows' => $product_table->get_rows(),
        ]);
    }

    public function get_meta_fields_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $acf = ACF_Plugin_Fields::get_instance('product');
        $acf_grouped_fields = $acf->get_grouped_fields();
        $acf_fields = $acf->get_fields();
        $acf_fields_content = '';

        if (!empty($acf_grouped_fields)) {
            foreach ($acf_grouped_fields as $acf_group) {
                $acf_fields_content = ' <option value="">' . esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite') . '</option><optgroup label="' . esc_attr($acf_group['title']) . '">';
                if (!empty($acf_group['fields'])) {
                    foreach ($acf_group['fields'] as $field_item) {
                        $acf_fields_content .= '<option value="' . esc_attr($field_item['name']) . '" data-type="' . ((isset($field_item['field_type'])) ? esc_attr($field_item['field_type']) : esc_attr($field_item['type'])) . '">' . esc_html($field_item['label']) . '</option>';
                    }
                }
                $acf_fields_content .= '</optgroup>';
            }
        }

        $meta_fields_content = '';
        $meta_field_repository = Meta_Field::get_instance();
        $meta_fields = $meta_field_repository->get();
        $meta_fields_main_types = $meta_field_repository->get_main_types();
        $meta_fields_sub_types = $meta_field_repository->get_sub_types();
        if (!empty($meta_fields)) {
            foreach ($meta_fields as $meta_field) {
                if (!empty($acf_fields) && is_array($acf_fields) && array_key_exists($meta_field['key'], $acf_fields)) {
                    $field_type = isset($acf_fields[$meta_field['key']]['field_type']) ? $acf_fields[$meta_field['key']]['field_type'] : $acf_fields[$meta_field['key']]['type'];
                    $type = Meta_Field_Helper::get_field_type_by_acf_type($acf_fields[$meta_field['key']]);
                    $meta_field['main_type'] = (isset($type['main_type'])) ? $type['main_type'] : '';
                    $meta_field['sub_type'] = (isset($type['sub_type'])) ? $type['sub_type'] : '';
                    $meta_field['type_disabled'] = true;
                }
                $meta_fields_content .= Render::html(WCBEL_VIEWS_DIR . 'meta_field/meta_field_item.php', compact('meta_field', 'meta_fields_main_types', 'meta_fields_sub_types'));
            }
        }

        $this->make_response([
            'success' => true,
            'meta_fields' => $meta_fields_content,
            'acf_fields' => $acf_fields_content,
        ]);
    }

    public function get_column_manager_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $profiles = '';
        $column_repository = Column::get_instance();
        $column_manager_presets = $column_repository->get_presets();
        $profiles .= Render::html(WCBEL_VIEWS_DIR . 'column_manager/preset_rows.php', compact('column_manager_presets'));

        $columns = '';
        $column_items = $column_repository->get_columns();
        $columns .= Render::html(WCBEL_VIEWS_DIR . 'column_manager/columns_list.php', compact('column_items'));

        $this->make_response([
            'success' => true,
            'profiles' => $profiles,
            'columns' => $columns,
        ]);
    }

    public function get_filter_profile_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $profiles = '';
        $search_repository = Search::get_instance();
        $filters_preset = $search_repository->get_presets();
        $filter_profile_use_always = $search_repository->get_use_always();
        if (!empty($filters_preset)) {
            foreach ($filters_preset as $filter_item) {
                $profiles .= Render::html(WCBEL_VIEWS_DIR . 'modals/filter_profile_item.php', compact('filter_item', 'filter_profile_use_always'));
            }
        }

        $this->make_response([
            'success' => true,
            'profiles' => $profiles,
        ]);
    }

    public function get_column_profile_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $column_repository = Column::get_instance();
        $grouped_fields = $column_repository->get_grouped_fields();
        $columns = Render::html(WCBEL_VIEWS_DIR . 'column_profile/columns.php', compact('grouped_fields'));

        $default_presets = $column_repository::get_default_columns_name();
        $column_manager_presets = $column_repository->get_presets();
        $active_columns_array = $column_repository->get_active_columns();
        $active_columns_key = (!empty($active_columns_array['name'])) ? sanitize_text_field($active_columns_array['name']) : '';
        $column_presets_fields = $column_repository->get_presets_fields();
        if (isset($column_presets_fields[$active_columns_key])) {
            $column_presets_fields[$active_columns_key] = array_keys($active_columns_array['fields']);
        }

        $profiles = Render::html(WCBEL_VIEWS_DIR . 'column_profile/profiles.php', compact('column_manager_presets', 'active_columns_key'));
        $this->make_response([
            'success' => true,
            'default_presets' => $default_presets,
            'column_presets_fields' => $column_presets_fields,
            'columns' => (!empty($columns)) ? $columns : '',
            'profiles' => (!empty($profiles)) ? $profiles : '',
        ]);
    }

    public function get_product_author()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id'])) {
            $this->make_response([
                'success' => false
            ]);
        }

        $post_object = get_post(intval($_POST['product_id']));
        if (!($post_object instanceof \WP_Post)) {
            $this->make_response([
                'success' => false
            ]);
        }

        $user = get_user_by('ID', intval($post_object->post_author));
        if (!($user instanceof \WP_User)) {
            $this->make_response([
                'success' => false
            ]);
        }

        $this->make_response([
            'success' => true,
            'author_id' => $user->ID,
            'author_name' => $user->user_login
        ]);
    }

    public function get_product_taxonomy_terms()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id']) || empty($_POST['taxonomy'])) {
            $this->make_response([
                'success' => true,
                'terms' => 'Empty !',
            ]);
        }

        $terms = get_terms([
            'taxonomy' => sanitize_text_field(wp_unslash($_POST['taxonomy'])),
            'hide_empty' => false,
        ]);

        $return_field = (in_array($_POST['taxonomy'], ['product_tag'])) ? 'slugs' : 'ids';
        $value_field = (in_array($_POST['taxonomy'], ['product_tag'])) ? 'slug' : 'term_id';

        $post_terms = wp_get_post_terms(intval($_POST['product_id']), sanitize_text_field(wp_unslash($_POST['taxonomy'])), [
            'fields' => $return_field
        ]);
        if (empty($post_terms)) {
            $post_terms = [];
        }

        $items = '';
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (!($term instanceof \WP_Term)) {
                    continue;
                }
                $items .= '<label><input type="checkbox" value="' . $term->{$value_field} . '" ' . ((in_array($term->{$value_field}, $post_terms)) ? 'checked="checked"' : '') . '> ' . $term->name . '</label>';
            }
        }

        $this->make_response([
            'success' => true,
            'terms' => $items,
        ]);
    }

    public function get_acf_taxonomy_terms()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id']) || empty($_POST['field_name'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $field_name = sanitize_text_field(wp_unslash($_POST['field_name']));

        $acf = ACF_Plugin_Fields::get_instance('product');
        $acf_fields = $acf->get_fields();
        if (empty($acf_fields[$field_name]) && !empty($acf_fields[$field_name]['taxonomy'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $product_terms = get_post_meta(intval($_POST['product_id']), $field_name, true);
        if (empty($product_terms)) {
            $product_terms = [];
        }
        $terms = get_terms([
            'taxonomy' => $acf_fields[$field_name]['taxonomy'],
            'hide_empty' => false,
            'fields' => 'id=>name'
        ]);

        $items = '';
        if (!empty($terms)) {
            foreach ($terms as $term_id => $term_label) {
                $items .= '<option value="' . $term_id . '" ' . ((in_array($term_id, $product_terms)) ? 'selected' : '') . '>' . $term_label . '</option>';
            }
        }

        $this->make_response([
            'success' => true,
            'terms' => $items,
        ]);
    }

    public function get_product_attribute_terms()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['product_id']) || empty($_POST['attribute'])) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $product_repository = Product::get_instance();
        $product = $product_repository->get_product(intval($_POST['product_id']));
        if (!($product instanceof \WC_Product)) {
            $this->make_response([
                'success' => false,
            ]);
        }

        $attribute_decoded = strtolower(urlencode(sanitize_text_field(wp_unslash($_POST['attribute']))));
        $attributes = $product->get_attributes();

        if (!empty($attributes) && isset($attributes[$attribute_decoded])) {
            $product_attribute = $attributes[$attribute_decoded];
        } else {
            $product_attribute['options'] = [];
            $product_attribute['visible'] = false;
            $product_attribute['variation'] = false;
        }

        $terms = get_terms([
            'taxonomy' => sanitize_text_field(wp_unslash($_POST['attribute'])),
            'hide_empty' => false,
        ]);

        $items = '';
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (!($term instanceof \WP_Term)) {
                    continue;
                }
                $items .= '<label><input type="checkbox" value="' . $term->term_id . '" ' . ((in_array($term->term_id, $product_attribute['options'])) ? 'checked="checked"' : '') . '> ' . $term->name . '</label>';
            }
        }

        $this->make_response([
            'success' => true,
            'terms' => $items,
            'visible' => $product_attribute['visible'],
            'variation' => $product_attribute['variation']
        ]);
    }

    public function get_manage_variation_attributes_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        $product_repository = Product::get_instance();
        $attributes = $product_repository->get_attributes();
        $attributes_html = Render::html(WCBEL_VIEWS_DIR . 'variations/add_variations/attributes.php', compact('attributes'));

        $this->make_response([
            'success' => true,
            'attributes' => $attributes_html
        ]);
    }

    private function get_all_filtered($args = [])
    {
        if (!isset($args['posts_per_page'])) {
            $args['posts_per_page'] = -1;
        }
        if (!isset($args['fields'])) {
            $args['fields'] = 'ids';
        }

        $product_filter_service = Product_Filter_Service::get_instance();
        $filtered_products = $product_filter_service->get_filtered_products($this->get_filter_data(), $args);

        $product_ids = [];
        if (!empty($filtered_products['product_ids'])) {
            $product_ids[] = $filtered_products['product_ids'];
        }
        if (!empty($filtered_products['variation_ids'])) {
            $product_ids[] = $filtered_products['variation_ids'];
        }

        return Others::array_flatten($product_ids);
    }

    private function get_filter_data()
    {
        $search_repository = Search::get_instance();
        $current_data = $search_repository->get_current_data();

        return (!empty($current_data['last_filter_data'])) ? $current_data['last_filter_data'] : [];
    }

    public function add_meta_keys_manual()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (isset($_POST['meta_key_name'])) {
            $meta_field['key'] = strtolower(str_replace(' ', '_', sanitize_text_field(wp_unslash($_POST['meta_key_name']))));
            $meta_fields_main_types = Meta_Field_Main::get_main_types();
            $meta_fields_sub_types = Meta_Field_Main::get_sub_types();
            $output = Render::html(WCBEL_VIEWS_DIR . "meta_field/meta_field_item.php", compact('meta_field', 'meta_fields_main_types', 'meta_fields_sub_types'));
            $this->make_response($output);
        }
        return false;
    }

    public function add_acf_meta_field()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['field_name'])) {
            $meta_field['key'] = sanitize_text_field(wp_unslash($_POST['field_name']));
            $meta_field['title'] = (!empty($_POST['field_label'])) ? sanitize_text_field(wp_unslash($_POST['field_label'])) : sanitize_text_field(wp_unslash($_POST['field_name']));
            $meta_field['type_disabled'] = true;
            $type = Meta_Field_Helper::get_field_type_by_acf_type([
                'field_type' => (!empty($_POST['field_type'])) ? sanitize_text_field(wp_unslash($_POST['field_type'])) : '',
                'type' => (!empty($_POST['field_type'])) ?  sanitize_text_field(wp_unslash($_POST['field_type'])) : '',
            ]);

            if (isset($type['main_type'])) {
                $meta_field['main_type'] = $type['main_type'];
            }
            if (isset($type['sub_type'])) {
                $meta_field['sub_type'] = $type['sub_type'];
            }

            $meta_fields_main_types = Meta_Field_Main::get_main_types();
            $meta_fields_sub_types = Meta_Field_Main::get_sub_types();
            $output = Render::html(WCBEL_VIEWS_DIR . "meta_field/meta_field_item.php", compact('meta_field', 'meta_fields_main_types', 'meta_fields_sub_types'));
            $this->make_response($output);
        }
        return false;
    }

    private function get_edit_items()
    {
        $merged_items = [
            'general' => EditFormItems::general_tab(),
            'taxonomies' => EditFormItems::taxonomies_tab(),
            'pricing' => EditFormItems::pricing_tab(),
            'stock' => EditFormItems::stock_tab(),
            'shipping' => EditFormItems::shipping_tab(),
            'type' => EditFormItems::type_tab(),
        ];

        foreach ($merged_items as $tab => $items) {
            $merged_items[$tab] = apply_filters("wcbel_bulk_edit_form_{$tab}_items", $items);
        }

        return $merged_items;
    }

    private function get_filter_items()
    {
        $configs = [
            'general' => FilterFormItems::general_tab(),
            'taxonomies' => FilterFormItems::taxonomies_tab(),
            'pricing' => FilterFormItems::pricing_tab(),
            'stock' => FilterFormItems::stock_tab(),
            'shipping' => FilterFormItems::shipping_tab(),
            'type' => FilterFormItems::type_tab(),
        ];

        $merged_items = [
            'general' => $configs['general'],
            'taxonomies' => $configs['taxonomies'],
            'pricing' => $configs['pricing'],
            'stock' => $configs['stock'],
            'shipping' => $configs['shipping'],
            'type' => $configs['type'],
        ];

        foreach ($merged_items as $tab => $items) {
            $merged_items[$tab] = apply_filters("wcbel_bulk_filter_form_{$tab}_items", $items);
        }

        return $merged_items;
    }

    private function new_form_items()
    {
        $merged_items = [
            'general' => NewFormItems::general_tab(),
            'taxonomies' => NewFormItems::taxonomies_tab(),
            'pricing' => NewFormItems::pricing_tab(),
            'stock' => NewFormItems::stock_tab(),
            'shipping' => NewFormItems::shipping_tab(),
            'type' => NewFormItems::type_tab(),
        ];

        foreach ($merged_items as $tab => $items) {
            $merged_items[$tab] = apply_filters("wcbel_bulk_new_form_{$tab}_items", $items);
        }

        return $merged_items;
    }

    private function make_response($data)
    {
        echo (is_array($data)) ? wp_json_encode($data) : wp_kses($data, Sanitizer::allowed_html());
        die();
    }
}
