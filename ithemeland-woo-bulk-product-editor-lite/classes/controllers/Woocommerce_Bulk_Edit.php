<?php

namespace wcbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\flush_message\Flush_Message;
use wcbel\classes\helpers\Compatible_Helper;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\Search;
use wcbel\classes\repositories\Setting;
use wcbel\classes\services\background_process\ProductBackgroundProcess;
use wcbel\framework\onboarding\Onboarding;

class Woocommerce_Bulk_Edit
{
    private $plugin_data;
    private $flush_message_repository;

    public static function init()
    {
        // Create an instance of the class and call the index method
        $instance = new self();
        $instance->index();
    }
    public function index()
    {
        $this->flush_message_repository = new Flush_Message();

        if (defined('WBEBL_NAME')) {
            if (! \wbebl\framework\onboarding\Onboarding::is_completed()) {
                return $this->wbebl_activation_page();
            }
        } else {
            if (!Onboarding::is_completed()) {
                return $this->activation_page();
            }
        }

        // "woocommerce currency switcher (woocs)" plugin compatibility
        $this->woocs_compatible();
        $this->set_plugin_data();
        add_filter('wcbe_top_navigation_buttons', [$this, 'add_navigation_buttons']);
        add_filter('wcbe_footer_view_files', [$this, 'add_footer']);

        $this->view();
    }

    private function wbebl_activation_page()
    {
        include_once WBEBL_FW_DIR . "onboarding/views/main.php";
    }

    private function activation_page()
    {
        include_once WCBEL_FW_DIR . "onboarding/views/main.php";
    }

    public function print_script()
    {
        $id_in_url = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

        echo "
        <script>
            var itemIdInUrl = " . esc_attr($id_in_url) . ";
            var defaultPresets = '';
            var wcbeTotalProductCount;
            var goToPageProcessing = false;
            var columnPresetsFields = '';
        </script>";
    }

    public function add_navigation_buttons($output)
    {
        if (empty($output)) {
            $output = '';
        }

        $last_filter_data = $this->plugin_data['last_filter_data'];
        $settings = $this->plugin_data['settings'];

        ob_start();
        include WCBEL_VIEWS_DIR . "navigation/buttons.php";
        $output .= ob_get_clean();

        return $output;
    }

    public function add_footer($output)
    {
        $output['wcbe-footer'] = WCBEL_VIEWS_DIR . "layouts/footer.php";
        return $output;
    }

    private function view()
    {
        $this->print_script();

        extract($this->plugin_data);
        include_once WCBEL_VIEWS_DIR . "layouts/main.php";
    }

    private function set_plugin_data()
    {
        $column_repository = Column::get_instance();
        $search_repository = Search::get_instance();
        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();

        if (!isset($settings['close_popup_after_applying'])) {
            $settings['close_popup_after_applying'] = 'no';
            $settings = $setting_repository->update($settings);
        }

        if (!$column_repository->has_column_fields()) {
            $column_repository->set_default_columns();
        }

        $current_data = $search_repository->get_current_data();
        if (empty($current_data)) {
            $search_repository->set_default_item();
            $current_data = $search_repository->get_current_data();
        }

        $active_columns_array = $column_repository->get_active_columns();
        if (empty($active_columns_array)) {
            $column_repository->set_default_active_columns();
            $active_columns_array = $column_repository->get_active_columns();
        }

        $background_process = ProductBackgroundProcess::get_instance();

        $this->plugin_data = [
            'plugin_key' => 'wcbel',
            'version' => WCBEL_VERSION,
            'title' => WCBEL_LABEL,
            'doc_link' => 'https://ithemelandco.com/docs/woocommerce-bulk-product-editing/',
            'flush_message' => $this->flush_message_repository->get(),
            'settings' => $settings,
            'count_per_page_items' => $setting_repository->get_count_per_page_items(),
            'items_loading' => true,
            'show_id_column' => $column_repository::SHOW_ID_COLUMN,
            'next_static_columns' => $column_repository::get_static_columns(),
            'has_compatible_fields' => Compatible_Helper::has_compatible_fields(),
            'columns' => $active_columns_array['fields'],
            'table' => WCBEL_VIEWS_DIR . 'product_table/table.php',
            'active_columns_key' => $active_columns_array['name'],
            'default_columns_name' => $column_repository::get_default_columns_name(),
            'last_filter_data' => (isset($current_data['last_filter_data'])) ? $current_data['last_filter_data'] : null,
            'is_processing' => $background_process->is_not_queue_empty(),
            'is_force_stopped' => $background_process->is_force_stopped(),
            'complete_message' => $background_process->get_complete_message(),
            'attributes' => wc_get_attribute_taxonomies(),
        ];
    }

    private function woocs_compatible()
    {
        if (class_exists('WOOCS')) {
            global $WOOCS;
            $WOOCS->reset_currency();
            remove_filter('woocommerce_product_get_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
            remove_filter('woocommerce_product_variation_get_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
            remove_filter('woocommerce_product_variation_get_regular_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
            remove_filter('woocommerce_product_variation_get_sale_price', array($WOOCS, 'raw_sale_price_filter'), 9999, 2);
            remove_filter('woocommerce_product_get_regular_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 2);
            remove_filter('woocommerce_product_get_sale_price', array($WOOCS, 'raw_woocommerce_price_sale'), 9999, 2);
            remove_filter('woocommerce_get_variation_regular_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 4);
            remove_filter('woocommerce_get_variation_sale_price', array($WOOCS, 'raw_woocommerce_price'), 9999, 4);
            remove_filter('woocommerce_variation_prices', array($WOOCS, 'woocommerce_variation_prices'), 9999, 3);
        }
    }
}
