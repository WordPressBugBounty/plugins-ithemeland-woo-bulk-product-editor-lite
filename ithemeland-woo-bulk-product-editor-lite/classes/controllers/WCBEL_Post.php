<?php

namespace wcbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\Flush_Message;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\Setting;
use wcbel\classes\services\activation\Activation_Service;
use wcbel\classes\services\export\Export_Service;

class WCBEL_Post
{
    private static $instance;

    public static function register_callback()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_post_wcbel_load_column_profile', [$this, 'load_column_profile']);
        add_action('admin_post_wcbel_settings', [$this, 'settings']);
        add_action('admin_post_wcbel_export_products', [$this, 'export_products']);
        add_action('admin_post_wcbel_save_column_profile', [$this, 'save_column_profile']);
        add_action('admin_post_wcbel_activation_plugin', [$this, 'activation_plugin']);
        add_action('admin_post_wcbel_column_manager_new_preset', [$this, 'column_manager_new_preset']);
        add_action('admin_post_wcbel_column_manager_edit_preset', [$this, 'column_manager_edit_preset']);
        add_action('admin_post_wcbel_column_manager_delete_preset', [$this, 'column_manager_delete_preset']);
    }

    public function column_manager_new_preset()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['save_preset']) && !empty($_POST['field_name']) && is_array($_POST['field_name'])) {
            $column_repository = new Column();
            $fields = $column_repository->get_fields();
            if (!empty($fields)) {
                $preset['name'] = esc_sql($_POST['preset_name']);
                $preset['date_modified'] = date('Y-m-d H:i:s', time());
                $preset['key'] = 'preset-' . rand(1000000, 9999999);
                if (!empty($_POST['field_name']) && is_array($_POST['field_name'])) {
                    for ($i = 0; $i < count($_POST['field_name']); $i++) {
                        if (isset($fields[$_POST['field_name'][$i]])) {
                            $preset["fields"][esc_sql($_POST['field_name'][$i])] = [
                                'name' => esc_sql($_POST['field_name'][$i]),
                                'label' => esc_sql($_POST['field_label'][$i]),
                                'title' => (!empty($_POST['field_title'][$i])) ? esc_sql($_POST['field_title'][$i]) : esc_sql($_POST['field_label'][$i]),
                                'editable' => $fields[$_POST['field_name'][$i]]['editable'],
                                'content_type' => $fields[$_POST['field_name'][$i]]['content_type'],
                                'allowed_type' => $fields[$_POST['field_name'][$i]]['allowed_type'],
                                'update_type' => $fields[$_POST['field_name'][$i]]['update_type'],
                                'fetch_type' => $fields[$_POST['field_name'][$i]]['fetch_type'],
                                'background_color' => $_POST['field_background_color'][$i],
                                'text_color' => $_POST['field_text_color'][$i],
                            ];
                            if (isset($fields[$_POST['field_name'][$i]]['field_type'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['field_type'] = $fields[$_POST['field_name'][$i]]['field_type'];
                            }
                            if (isset($fields[$_POST['field_name'][$i]]['sub_name'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['sub_name'] = $fields[$_POST['field_name'][$i]]['sub_name'];
                            }
                            if (isset($fields[$_POST['field_name'][$i]]['sortable'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['sortable'] = $fields[$_POST['field_name'][$i]]['sortable'];
                            }
                            if (isset($fields[$_POST['field_name'][$i]]['options'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['options'] = $fields[$_POST['field_name'][$i]]['options'];
                            }
                            $preset['checked'][] = esc_sql($_POST['field_name'][$i]);
                        }
                    }
                    $column_repository->update($preset);
                }
            }
        }
        $this->redirect([
            'message' => __('Success !', 'ithemeland-woocommerce-bulk-product-editing-lite'),
            'type' => 'success',
        ]);
    }

    public function column_manager_edit_preset()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['edit_preset'])) {
            $column_repository = new Column();
            $fields = $column_repository->get_fields();
            if (!empty($fields)) {
                $preset["fields"] = [];
                $preset['name'] = esc_sql($_POST['preset_name']);
                $preset['date_modified'] = date('Y-m-d H:i:s', time());
                $preset['key'] = $_POST['preset_key'];
                if (!empty($_POST['field_name']) && is_array($_POST['field_name'])) {
                    for ($i = 0; $i < count($_POST['field_name']); $i++) {
                        if (isset($fields[$_POST['field_name'][$i]])) {
                            $preset["fields"][esc_sql($_POST['field_name'][$i])] = [
                                'name' => esc_sql($_POST['field_name'][$i]),
                                'label' => esc_sql($_POST['field_label'][$i]),
                                'title' => (!empty($_POST['field_title'][$i])) ? esc_sql($_POST['field_title'][$i]) : esc_sql($_POST['field_label'][$i]),
                                'editable' => $fields[$_POST['field_name'][$i]]['editable'],
                                'content_type' => $fields[$_POST['field_name'][$i]]['content_type'],
                                'allowed_type' => $fields[$_POST['field_name'][$i]]['allowed_type'],
                                'update_type' => $fields[$_POST['field_name'][$i]]['update_type'],
                                'fetch_type' => $fields[$_POST['field_name'][$i]]['fetch_type'],
                                'background_color' => $_POST['field_background_color'][$i],
                                'text_color' => $_POST['field_text_color'][$i],
                            ];
                            if (isset($fields[$_POST['field_name'][$i]]['sortable'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['sortable'] = $fields[$_POST['field_name'][$i]]['sortable'];
                            }
                            if (isset($fields[$_POST['field_name'][$i]]['sub_name'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['sub_name'] = $fields[$_POST['field_name'][$i]]['sub_name'];
                            }
                            if (isset($fields[$_POST['field_name'][$i]]['options'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['options'] = $fields[$_POST['field_name'][$i]]['options'];
                            }
                            if (isset($fields[$_POST['field_name'][$i]]['field_type'])) {
                                $preset["fields"][esc_sql($_POST['field_name'][$i])]['field_type'] = $fields[$_POST['field_name'][$i]]['field_type'];
                            }
                            $preset['checked'][] = esc_sql($_POST['field_name'][$i]);
                        }
                    }
                    $column_repository->update($preset);
                    $column_repository->set_active_columns($preset['key'], $preset['fields']);
                }
            }
        }
        $this->redirect([
            'message' => __('Success !', 'ithemeland-woocommerce-bulk-product-editing-lite'),
            'type' => 'success',
        ]);
    }

    public function column_manager_delete_preset()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        $column_repository = new Column();
        if (isset($_POST['delete_key'])) {
            if ($column_repository->get_active_columns()['name'] == $_POST['delete_key']) {
                $column_repository->delete_active_columns();
            }
            $column_repository->delete(esc_sql($_POST['delete_key']));
        }

        $this->redirect([
            'message' => __('Success !', 'ithemeland-woocommerce-bulk-product-editing-lite'),
            'type' => 'success',
        ]);
    }

    public function load_column_profile()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['preset_key'])) {
            $preset_key = sanitize_text_field($_POST['preset_key']);
            $checked_columns = Sanitizer::array($_POST["columns"]);
            $checked_columns = array_combine($checked_columns, $checked_columns);
            $column_repository = new Column();
            $columns = [];
            $fields = $column_repository->get_fields();
            $preset_columns = $column_repository->get_preset($preset_key);
            if (!empty($checked_columns) && is_array($checked_columns)) {
                if (!empty($preset_columns['fields'])) {
                    foreach ($preset_columns['fields'] as $column_key => $preset_column) {
                        if (isset($checked_columns[$column_key])) {
                            $columns[$column_key] = $preset_column;
                            unset($checked_columns[$column_key]);
                        }
                    }
                }
                if (!empty($checked_columns)) {
                    foreach ($checked_columns as $diff_item) {
                        if (isset($fields[$diff_item])) {
                            $checked_column = [
                                'name' => $fields[$diff_item]['name'],
                                'label' => $fields[$diff_item]['label'],
                                'title' => $fields[$diff_item]['label'],
                                'editable' => $fields[$diff_item]['editable'],
                                'content_type' => $fields[$diff_item]['content_type'],
                                'allowed_type' => $fields[$diff_item]['allowed_type'],
                                'update_type' => $fields[$diff_item]['update_type'],
                                'background_color' => '#fff',
                                'text_color' => '#444',
                            ];
                            if (isset($fields[$diff_item]['sortable'])) {
                                $checked_column['sortable'] = ($fields[$diff_item]['sortable']);
                            }
                            if (isset($fields[$diff_item]['sub_name'])) {
                                $checked_column['sub_name'] = ($fields[$diff_item]['sub_name']);
                            }
                            if (isset($fields[$diff_item]['options'])) {
                                $checked_column['options'] = $fields[$diff_item]['options'];
                            }
                            if (isset($fields[$diff_item]['field_type'])) {
                                $checked_column['field_type'] = $fields[$diff_item]['field_type'];
                            }
                            $columns[$diff_item] = $checked_column;
                        }
                    }
                }
            }
            $column_repository->set_active_columns($preset_key, $columns);
        }

        $this->redirect();
    }

    public function settings()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['settings'])) {
            $setting_repository = new Setting();
            $setting_repository->update(Sanitizer::array($_POST['settings']));
        }

        $this->redirect([
            'message' => __('Success !', 'ithemeland-bulk-product-editing-lite-for-woocommerce'),
            'type' => 'success',
        ]);
    }

    public function export_products()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (empty($_POST['products']) || empty($_POST['fields'])) {
            $this->redirect([
                'message' => __('Error ! try again', 'ithemeland-bulk-product-editing-lite-for-woocommerce'),
                'type' => 'danger',
            ]);
        }

        $export_service = Export_Service::get_instance();
        $export_service->set_data([
            'delimiter' => sanitize_text_field($_POST['wcbel_export_delimiter']),
            'select_type' => sanitize_text_field($_POST['products']),
            'field_type' => sanitize_text_field($_POST['fields']),
            'selected_ids' => isset($_POST['item_ids']) ? Sanitizer::array($_POST['item_ids']) : [],
        ]);
        $export_service->perform();

        $this->redirect();
    }

    public function activation_plugin()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wcbel_post_nonce')) {
            die('403 Forbidden');
        }

        $message = "Error! Try again";

        if (isset($_POST['activation_type'])) {
            if ($_POST['activation_type'] == 'skip') {
                update_option('wcbel_is_active', 'skipped');
                return $this->redirect('bulk-edit');
            } else {
                if (!empty($_POST['email']) && !empty($_POST['industry'])) {
                    $activation_service = new Activation_Service();
                    $info = $activation_service->activation([
                        'email' => sanitize_email($_POST['email']),
                        'domain' => $_SERVER['SERVER_NAME'],
                        'product_id' => 'wcbel',
                        'product_name' => WCBEL_LABEL,
                        'industry' => sanitize_text_field($_POST['industry']),
                        'multi_site' => is_multisite(),
                        'core_version' => null,
                        'subsystem_version' => WCBEL_VERSION,
                    ]);

                    if (!empty($info) && is_array($info)) {
                        if (!empty($info['result']) && $info['result'] == true) {
                            update_option('wcbel_is_active', 'yes');
                            $message = esc_html__('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
                        } else {
                            update_option('wcbel_is_active', 'no');
                            $message = (!empty($info['message'])) ? esc_html($info['message']) : esc_html__('System Error !', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
                        }
                    } else {
                        update_option('wcbel_is_active', 'no');
                        $message = esc_html__('Connection Timeout! Please Try Again', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
                    }
                }
            }
        }

        $this->redirect($message);
    }

    private function redirect($notice = [])
    {
        if (!empty($notice) && isset($notice['message'])) {
            $flush_message_repository = new Flush_Message();
            $flush_message_repository->set($notice);
        }

        return wp_redirect(WCBEL_PLUGIN_MAIN_PAGE);
    }
}
