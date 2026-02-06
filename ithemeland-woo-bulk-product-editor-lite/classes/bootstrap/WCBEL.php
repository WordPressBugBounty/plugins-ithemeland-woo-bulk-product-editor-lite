<?php

namespace wcbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\framework\analytics\AnalyticsTracker;
use wcbel\framework\onboarding\Onboarding;
use wcbel\classes\controllers\WCBEL_Ajax;
use wcbel\classes\controllers\WCBEL_Post;
use wcbel\classes\helpers\Lang_Helper;
use wcbel\classes\helpers\Render;
use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\Meta_Field;
use wcbel\classes\repositories\Search;
use wcbel\classes\repositories\Setting;
use wcbel\classes\services\background_process\ProductBackgroundProcess;
use wcbel\classes\services\history\HistoryRedoService;
use wcbel\classes\services\history\HistoryUndoService;
use wcbel\classes\services\product_delete\ProductDeleteService;
use wcbel\classes\services\product_duplicate\ProductDuplicateService;
use wcbel\classes\services\scheduler\Product_Scheduler;

class WCBEL
{
    private static $instance;
    private static $is_initable;

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    private function __construct()
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        AnalyticsTracker::register();
        Onboarding::register();
        WCBEL_Ajax::register_callback();
        WCBEL_Post::register_callback();
        WCBEL_Meta_Fields::init();

        add_filter('safe_style_css', function ($styles) {
            $styles[] = 'display';
            return $styles;
        });

        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        }
    }

    public function add_menu()
    {
        if (!defined('WCBE_NAME') && !defined('WBEB_NAME')) {
            if (defined('WBEBL_NAME')) {
                add_submenu_page('wbebl', esc_html__('PBULKiT', 'ithemeland-woo-bulk-product-editor-lite'), esc_html__('PBULKiT', 'ithemeland-woo-bulk-product-editor-lite'), 'manage_woocommerce', 'wcbe', ['wcbel\classes\controllers\Woocommerce_Bulk_Edit', 'init'], 1);
            } else {
                add_menu_page(esc_html__('PBULKiT', 'ithemeland-woo-bulk-product-editor-lite'), wp_kses('PBULK<span style="color: #627ddd;font-weight: 900;">iT</span>', Sanitizer::allowed_html()), 'manage_woocommerce', 'wcbe', ['wcbel\classes\controllers\Woocommerce_Bulk_Edit', 'init'], WCBEL_IMAGES_URL . 'pbulkit-icon-wh20.svg', 59);
            }
        }

        // Add "Go Pro" submenu
        // add_submenu_page(
        //     'wcbe',
        //     esc_html__('Go Pro', 'ithemeland-woo-bulk-product-editor-lite'),
        //     '<img class="wcbe-icon-go-pro" src="' . WCBEL_URL . 'views/go_pro/assets/images/go-pro.png" style="width:20px; height:20px; margin-right:5px; vertical-align:middle;"> ' . esc_html__('Go Pro', 'ithemeland-woo-bulk-product-editor-lite'),
        //     'manage_options',
        //     'wcbel_go_pro',
        //     [$this, 'wcbe_go_pro_page']
        // );

        // Add "Other Plugins" submenu
        // add_submenu_page(
        //     'wcbe',
        //     esc_html__('Other Plugins', 'ithemeland-woo-bulk-product-editor-lite'),
        //     esc_html__('Other Plugins', 'ithemeland-woo-bulk-product-editor-lite'),
        //     'manage_options',
        //     'wcbel_other_plugins',
        //     [$this, 'wcbe_other_plugins_page']
        // );
    }

    // "Go Pro" page callback
    // public function wcbe_go_pro_page()
    // {
    //     include_once WCBEL_VIEWS_DIR . 'go_pro/go_pro.php';
    //     if (!empty($_GET['page']) && $_GET['page'] == 'wcbel_go_pro') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    //         wp_enqueue_style('wcbe-go-pro', WCBEL_URL . 'views/go_pro/assets/css/style.css', [], WCBEL_VERSION);
    //         wp_enqueue_style('wcbel-main', WCBEL_CSS_URL . 'style-core.css', [], WCBEL_VERSION);
    //     }
    // }

    // "Other Plugins" page callback
    // public function wcbe_other_plugins_page()
    // {
    //     include_once WCBEL_VIEWS_DIR . 'go_pro/other_plugins/other_plugins.php';
    //     if (!empty($_GET['page']) && $_GET['page'] == 'wcbel_other_plugins') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
    //         wp_enqueue_style('wcbe-go-pro', WCBEL_URL . 'views/go_pro/assets/css/style.css', [], WCBEL_VERSION);
    //         wp_enqueue_style('wcbel-main', WCBEL_CSS_URL . 'style-core.css', [], WCBEL_VERSION);
    //     }
    // }

    public static function woocommerce_required()
    {
        include WCBEL_VIEWS_DIR . 'alerts/woocommerce_required.php';
    }

    private function reset_filter_data()
    {
        $reset_version = '1.0.0';
        $last_reset = get_option('wcbe_reset_filter_data_version', null);

        if (empty($last_reset) || version_compare($last_reset, $reset_version, '<')) {
            $search_repository = Search::get_instance();
            $search_repository->update_current_data([
                'last_filter_data' => [],
            ]);

            update_option('wcbe_reset_filter_data_version', $reset_version);
        }
    }

    public static function wcbe_wp_init()
    {
        if (!self::is_initable()) {
            return false;
        }

        $version = get_option('wcbel-version');
        if (empty($version) || $version != WCBEL_VERSION) {
            self::create_tables();
            self::update_table();

            $column_repository = Column::get_instance();
            $column_repository->set_default_columns();
            $column_repository->sync_active_columns();
            update_option('wcbel-version', WCBEL_VERSION);
        }

        WCBEL_Custom_Queries::init();
        ProductBackgroundProcess::init();

        if (!defined('DISABLE_WP_CRON') || (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON === false)) {
            Product_Scheduler::init();
        }

        // load textdomain
        load_plugin_textdomain('ithemeland-woo-bulk-product-editor-lite', false, WCBEL_LANGUAGES_DIR);
    }

    public function enqueue_scripts($page)
    {
        if (!empty($_GET['page']) && $_GET['page'] == 'wcbe') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if (defined('WBEBL_NAME')) {
                if (\wbebl\framework\onboarding\Onboarding::is_completed()) {
                    $this->main_enqueue_scripts();
                } else {
                    $this->onboarding_enqueue_scripts();
                }
            } else {
                if (Onboarding::is_completed()) {
                    $this->main_enqueue_scripts();
                } else {
                    $this->onboarding_enqueue_scripts();
                }
            }
        }
    }

    public function main_enqueue_scripts()
    {
        if (!empty($_GET['page']) && $_GET['page'] == 'wcbe') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $setting_repository = Setting::get_instance();
            $meta_field_repository = Meta_Field::get_instance();
            $search_repository = Search::get_instance();

            //fix conflict 
            wp_dequeue_script('bol-sweetalert.min');

            // Styles
            wp_enqueue_style('wcbel-reset', WCBEL_CSS_URL . 'reset.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-icomoon', WCBEL_CSS_URL . 'icomoon.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-datepicker', WCBEL_CSS_URL . 'bootstrap-material-datetimepicker.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-select2', WCBEL_CSS_URL . 'select2.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-sweetalert', WCBEL_CSS_URL . 'sweetalert.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-jquery-ui', WCBEL_CSS_URL . 'jquery-ui.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-tipsy', WCBEL_CSS_URL . 'jquery.tipsy.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-datetimepicker', WCBEL_CSS_URL . 'jquery.datetimepicker.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-main-core', WCBEL_CSS_URL . 'style-core.css', [], WCBEL_VERSION);
            wp_enqueue_style('wcbel-main', WCBEL_CSS_URL . 'style.css', [], WCBEL_VERSION);
            wp_enqueue_style('wp-color-picker');

            // "yith badge management" plugin
            if (class_exists('YITH_WCBM_Frontend')) {
                $yith_frontend = \YITH_WCBM_Frontend::get_instance();
                $yith_frontend->enqueue_scripts();
            }

            // "yith badge management" plugin
            if (function_exists('iThemeland_WooCommerce_Advanced_Product_Labels_Pro')) {
                wp_enqueue_style('icon_it_css', plugins_url('/assets/css/icon-picker.css', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array(), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version);
                wp_enqueue_style('it-woocommerce-advanced-product-labels-pro-front-end-css', plugins_url('/assets/front-end/css/it-woocommerce-advanced-product-labels-pro.css', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array(), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version);
                wp_enqueue_script('countdown-1-js', plugins_url('/assets/jquery-countdown/jquery.countdown.min.js', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array('jquery'), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
                wp_enqueue_script('countdown-2-js', plugins_url('/assets/FlipClock/flipclock.min.js', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array('jquery'), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
                wp_enqueue_script('tooltipster_it_js', plugins_url('/assets/tooltip/tooltipster.bundle.js', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array('jquery'), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
                wp_enqueue_script('icon_it_js', plugins_url('/assets/js/icon-picker.js', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array('jquery'), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
                wp_enqueue_script('it-customscrollbar-js', plugins_url('/assets/admin/js/jquery.mCustomScrollbar.concat.min.js', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array('jquery'), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
                wp_enqueue_script('single-label-js', plugins_url('/assets/admin/js/single-label-option.js', iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->file), array('jquery', 'it-customscrollbar-js'), iThemeland_WooCommerce_Advanced_Product_Labels_Pro()->version); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            }

            // Scripts
            wp_enqueue_script('wcbel-datetimepicker', WCBEL_JS_URL . 'jquery.datetimepicker.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-functions-core', WCBEL_JS_URL . 'functions-core.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-functions', WCBEL_JS_URL . 'functions.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-select2', WCBEL_JS_URL . 'select2.min.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-moment', WCBEL_JS_URL . 'moment-with-locales.min.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-tipsy', WCBEL_JS_URL . 'jquery.tipsy.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-bootstrap_datepicker', WCBEL_JS_URL . 'bootstrap-material-datetimepicker.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-sweetalert', WCBEL_JS_URL . 'sweetalert.min.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-main-core', WCBEL_JS_URL . 'main-core.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_enqueue_script('wcbel-main', WCBEL_JS_URL . 'main.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
            wp_localize_script('wcbel-main', 'WCBE_DATA', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('wcbe_ajax_nonce'),
                'reserved_field_keys' => $meta_field_repository->get_reserved_field_names(),
                'background_process' => [
                    'max_process_count' => [
                        'product_create' => 100,
                        'product_restore' => 100,
                        'product_update' => 10,
                        'product_duplicate_count' => ProductDuplicateService::MAX_PROCESS_COUNT,
                        'product_duplicate_ids' => ProductDuplicateService::MAX_PROCESS_IDS,
                        'product_delete' => ProductDeleteService::MAX_PROCESS_COUNT,
                        'history_undo' => HistoryUndoService::MAX_PROCESS_COUNT,
                        'history_redo' => HistoryRedoService::MAX_PROCESS_COUNT,
                    ],
                    'loading_messages' => [
                        'processing' => Render::html(WCBEL_VIEWS_DIR . 'background_process/processing_message.php'),
                        'stopping' => Render::html(WCBEL_VIEWS_DIR . 'background_process/stopping_message.php'),
                    ]
                ],
                'icons' => [
                    'loading_2' => esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'),
                    'sortUpAndDown' => '<img src="' . esc_url(WCBEL_IMAGES_URL . 'sortable.png') . '" alt="">',
                    'sortUpToDown' => '<i class="dashicons dashicons-arrow-down"></i>',
                    'sortDownToUp' => '<i class="dashicons dashicons-arrow-up"></i>',
                ],
                'strings' => [
                    'please_select_one_item' => esc_html__('Please select one product', 'ithemeland-woo-bulk-product-editor-lite')
                ],
                'filter_option_values' => $search_repository->get_option_values(),
                'wcbe_settings' => $setting_repository->get_settings()
            ]);
            wp_localize_script('wcbel-main', 'wcbeTranslate', Lang_Helper::get_js_strings());
            wp_enqueue_media();
            wp_enqueue_editor();
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('wp-color-picker');
        }
    }

    private function onboarding_enqueue_scripts()
    {
        wp_enqueue_style('wcbel-sweetalert', WCBEL_CSS_URL . 'sweetalert.css', [], WCBEL_VERSION);
        wp_enqueue_script('wcbel-sweetalert', WCBEL_JS_URL . 'sweetalert.min.js', ['jquery'], WCBEL_VERSION);  //phpcs:ignore

        if (defined('WBEBL_NAME')) {
            wp_enqueue_style('wcbel-onboarding', WBEBL_FW_URL . 'onboarding/assets/css/onboarding.css', [], WBEBL_VERSION);
            wp_enqueue_script('wcbel-onboarding', WBEBL_FW_URL . 'onboarding/assets/js/onboarding.js', ['jquery'], WBEBL_VERSION);  //phpcs:ignore
        } else {
            wp_enqueue_style('wcbel-onboarding', WCBEL_FW_URL . 'onboarding/assets/css/onboarding.css', [], WCBEL_VERSION);
            wp_enqueue_script('wcbel-onboarding', WCBEL_FW_URL . 'onboarding/assets/js/onboarding.js', ['jquery'], WCBEL_VERSION);  //phpcs:ignore
        }

        wp_localize_script('wcbel-onboarding', 'ithemeland_onboarding', [
            'nonce' => wp_create_nonce('ithemeland_onboarding_action'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'redirecting_text' => esc_html__('Redirecting...', 'ithemeland-woo-bulk-product-editor-lite'),
            'skip_text' => esc_html__('Skip', 'ithemeland-woo-bulk-product-editor-lite')
        ]);
    }

    public static function activate()
    {
        // 
    }

    public static function deactivate()
    {
        // 
    }

    public static function is_initable()
    {
        if (!is_null(self::$is_initable)) {
            return self::$is_initable;
        }

        if (!class_exists('WooCommerce')) {
            self::woocommerce_required();
            self::$is_initable = false;
            return false;
        }

        if (defined('WCBE_VERSION') && version_compare(WCBE_VERSION, '4.0.0', '<')) {
            self::$is_initable = false;
            return false;
        }

        self::$is_initable = true;
        return true;
    }

    private static function create_tables()
    {
        global $wpdb;
        $history_table_name = esc_sql($wpdb->prefix . 'itbbc_history');
        $history_items_table_name = esc_sql($wpdb->prefix . 'itbbc_history_items');
        $query = '';
        $history_table = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($history_table_name));
        if (!$wpdb->get_var($history_table) == $history_table_name) { //phpcs:ignore
            $query .= "CREATE TABLE {$history_table_name} ( " . //phpcs:ignore
                "id int(11) NOT NULL AUTO_INCREMENT,
                  user_id int(11) NOT NULL,
                  fields text NOT NULL,
                  operation_type varchar(32) NOT NULL,
                  operation_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  reverted tinyint(1) NOT NULL DEFAULT '0',
                  sub_system varchar(64) NOT NULL,
                  PRIMARY KEY (id),
                  INDEX (user_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        }

        $history_items_table = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($history_items_table_name));
        if (!$wpdb->get_var($history_items_table) == $history_items_table_name) { //phpcs:ignore 
            $query .= "CREATE TABLE {$history_items_table_name} (" . //phpcs:ignore 
                "id int(11) NOT NULL AUTO_INCREMENT,
                      history_id int(11) NOT NULL,
                      historiable_id int(11) NOT NULL,
                      field longtext,
                      prev_value longtext,
                      new_value longtext,
                      PRIMARY KEY (id),
                      INDEX (history_id),
                      INDEX (historiable_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $query .= "ALTER TABLE {$history_items_table_name} ADD CONSTRAINT itbbc_history_items_history_id_relation FOREIGN KEY (history_id) REFERENCES {$history_table_name} (id) ON DELETE CASCADE ON UPDATE CASCADE;";
        } else {
            $result = $wpdb->get_results("SELECT DATA_TYPE as itbbc_field_type FROM information_schema.columns WHERE table_name = '{$history_items_table_name}' AND column_name = 'field'"); //phpcs:ignore 
            if (!empty($result[0]->itbbc_field_type) && $result[0]->itbbc_field_type != 'longtext') {
                $wpdb->query("ALTER TABLE {$history_items_table_name} MODIFY field longtext"); //phpcs:ignore
            }
        }

        if (!empty($query)) {
            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($query);
        }
    }

    private static function update_table()
    {
        global $wpdb;
        $history_items_table_name = sanitize_text_field($wpdb->prefix . 'itbbc_history_items');
        $result = $wpdb->get_var("SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$history_items_table_name}' AND COLUMN_NAME = 'prev_total_count'"); //phpcs:ignore
        if (empty($result)) {
            try {
                $wpdb->query("ALTER TABLE {$history_items_table_name} ADD prev_total_count INT(11) NOT NULL DEFAULT 1"); //phpcs:ignore 
                $wpdb->query("ALTER TABLE {$history_items_table_name} ADD new_total_count INT(11) NOT NULL DEFAULT 1"); //phpcs:ignore 
            } catch (\Exception $e) {
                update_option('wcbe_update_table_log', $e->getMessage());
            }
        }
    }
}
