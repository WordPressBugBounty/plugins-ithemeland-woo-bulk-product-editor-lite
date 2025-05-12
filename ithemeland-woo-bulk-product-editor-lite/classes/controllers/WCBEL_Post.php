<?php

namespace wcbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\flush_message\Flush_Message;
use wcbe\framework\flush_message\GlobalFlushMessage;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\Setting;
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
        add_action('admin_post_wcbe_column_manager_new_preset', [$this, 'column_manager_new_preset']);
        add_action('admin_post_wcbe_column_manager_edit_preset', [$this, 'column_manager_edit_preset']);
        add_action('admin_post_wcbe_column_manager_delete_preset', [$this, 'column_manager_delete_preset']);
        add_action('admin_post_wcbe_load_column_profile', [$this, 'load_column_profile']);
        add_action('admin_post_wcbe_settings', [$this, 'settings']);
        add_action('admin_post_wcbe_export_products', [$this, 'export_products']);
        add_action('admin_post_wcbe_save_column_profile', [$this, 'save_column_profile']);
        add_action('admin_post_wcbe_variation_attaching', [$this, 'variation_attaching']);
    }

    public function column_manager_new_preset()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'wcbe_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['save_preset']) && !empty($_POST['field_name']) && is_array($_POST['field_name']) && !empty($_POST['preset_name'])) {
            $column_repository = Column::get_instance();
            $fields = $column_repository->get_columns();
            if (!empty($fields)) {
                $preset['name'] = sanitize_text_field(wp_unslash($_POST['preset_name']));
                $preset['date_modified'] = gmdate('Y-m-d H:i:s', time());
                $preset['key'] = 'preset-' . wp_rand(1000000, 9999999);
                if (!empty($_POST['field_name']) && is_array($_POST['field_name'])) {
                    for ($i = 0; $i < count($_POST['field_name']); $i++) {
                        if (isset($fields[$_POST['field_name'][$i]])) {
                            $field_name = sanitize_text_field(wp_unslash($_POST['field_name'][$i]));
                            $preset["fields"][$field_name] = [
                                'name' => $field_name,
                                'label' => (isset($_POST['field_label'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_label'][$i])) : '',
                                'title' => (!empty($_POST['field_title'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_title'][$i])) : sanitize_text_field(wp_unslash($_POST['field_label'][$i])),
                                'editable' => $fields[$field_name]['editable'],
                                'content_type' => $fields[$field_name]['content_type'],
                                'allowed_type' => $fields[$field_name]['allowed_type'],
                                'update_type' => $fields[$field_name]['update_type'],
                                'fetch_type' => $fields[$field_name]['fetch_type'],
                                'background_color' => (!empty($_POST['field_background_color'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_background_color'][$i])) : '',
                                'text_color' => (!empty($_POST['field_text_color'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_text_color'][$i])) : '',
                            ];
                            if (isset($fields[$field_name]['field_type'])) {
                                $preset["fields"][$field_name]['field_type'] = $fields[$field_name]['field_type'];
                            }
                            if (isset($fields[$field_name]['sub_name'])) {
                                $preset["fields"][$field_name]['sub_name'] = $fields[$field_name]['sub_name'];
                            }
                            if (isset($fields[$field_name]['sortable'])) {
                                $preset["fields"][$field_name]['sortable'] = $fields[$field_name]['sortable'];
                            }
                            if (isset($fields[$field_name]['options'])) {
                                $preset["fields"][$field_name]['options'] = $fields[$field_name]['options'];
                            }
                            $preset['checked'][] = $field_name;
                        }
                    }
                    $column_repository->update($preset);
                }
            }
        }
        $this->redirect([
            'message' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            'type' => 'success',
        ]);
    }

    public function column_manager_edit_preset()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'wcbe_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['edit_preset']) && isset($_POST['preset_name']) && !empty($_POST['preset_key'])) {
            $column_repository = Column::get_instance();
            $fields = $column_repository->get_columns();
            if (!empty($fields)) {
                $preset["fields"] = [];
                $preset['name'] = sanitize_text_field(wp_unslash($_POST['preset_name']));
                $preset['date_modified'] = gmdate('Y-m-d H:i:s', time());
                $preset['key'] = sanitize_text_field(wp_unslash($_POST['preset_key']));
                if (!empty($_POST['field_name']) && is_array($_POST['field_name'])) {
                    for ($i = 0; $i < count($_POST['field_name']); $i++) {
                        if (!empty($_POST['field_name'][$i])) {
                            $field_name = sanitize_text_field(wp_unslash($_POST['field_name'][$i]));
                            if (isset($fields[$_POST['field_name'][$i]])) {
                                $preset["fields"][$field_name] = [
                                    'name' => $field_name,
                                    'label' => (!empty($_POST['field_label'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_label'][$i])) : $field_name,
                                    'title' => (!empty($_POST['field_title'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_title'][$i])) : $field_name,
                                    'editable' => $fields[$field_name]['editable'],
                                    'content_type' => $fields[$field_name]['content_type'],
                                    'allowed_type' => $fields[$field_name]['allowed_type'],
                                    'update_type' => $fields[$field_name]['update_type'],
                                    'fetch_type' => $fields[$field_name]['fetch_type'],
                                    'background_color' => (!empty($_POST['field_background_color'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_background_color'][$i])) : '',
                                    'text_color' => (!empty($_POST['field_text_color'][$i])) ? sanitize_text_field(wp_unslash($_POST['field_text_color'][$i])) : '',
                                ];
                                if (isset($fields[$field_name]['sortable'])) {
                                    $preset["fields"][$field_name]['sortable'] = $fields[$field_name]['sortable'];
                                }
                                if (isset($fields[$field_name]['sub_name'])) {
                                    $preset["fields"][$field_name]['sub_name'] = $fields[$field_name]['sub_name'];
                                }
                                if (isset($fields[$field_name]['options'])) {
                                    $preset["fields"][$field_name]['options'] = $fields[$field_name]['options'];
                                }
                                if (isset($fields[$field_name]['field_type'])) {
                                    $preset["fields"][$field_name]['field_type'] = $fields[$field_name]['field_type'];
                                }
                                $preset['checked'][] = $field_name;
                            }
                        }
                    }
                    $column_repository->update($preset);
                    $column_repository->set_active_columns($preset['key'], $preset['fields']);
                }
            }
        }
        $this->redirect([
            'message' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            'type' => 'success',
        ]);
    }

    public function column_manager_delete_preset()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'wcbe_post_nonce')) {
            die('403 Forbidden');
        }

        $column_repository = Column::get_instance();
        if (isset($_POST['delete_key'])) {
            if ($column_repository->get_active_columns()['name'] == $_POST['delete_key']) {
                $column_repository->delete_active_columns();
            }
            $column_repository->delete(sanitize_text_field(wp_unslash($_POST['delete_key'])));
        }

        $this->redirect([
            'message' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            'type' => 'success',
        ]);
    }

    public function load_column_profile()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'wcbe_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['preset_key'])) {
            $preset_key = sanitize_text_field(wp_unslash($_POST['preset_key']));
            $checked_columns = (!empty($_POST["columns"])) ? Sanitizer::array($_POST["columns"]) : []; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            if (!is_array($checked_columns)) {
                return false;
            }
            $checked_columns = array_combine($checked_columns, $checked_columns);
            $column_repository = Column::get_instance();
            $columns = [];
            $fields = $column_repository->get_columns();
            $preset_columns = $column_repository->get_preset($preset_key);
            if (!empty($checked_columns) && is_array($checked_columns)) {
                if (!empty($preset_columns['fields'])) {
                    foreach ($preset_columns['fields'] as $column_key => $preset_column) {
                        if (isset($checked_columns[$column_key]) && isset($fields[$column_key])) {
                            $columns[$column_key] = $fields[$column_key];
                            $columns[$column_key]['title'] = (isset($fields[$column_key]['label'])) ? $fields[$column_key]['label'] : $column_key;
                            unset($checked_columns[$column_key]);
                        }
                    }
                }
                if (!empty($checked_columns)) {
                    foreach ($checked_columns as $diff_item) {
                        if (isset($fields[$diff_item])) {
                            $checked_column = [
                                'name' => sanitize_text_field(wp_unslash($fields[$diff_item]['name'])),
                                'label' => sanitize_text_field(wp_unslash($fields[$diff_item]['label'])),
                                'title' => sanitize_text_field(wp_unslash($fields[$diff_item]['label'])),
                                'editable' => $fields[$diff_item]['editable'],
                                'content_type' => $fields[$diff_item]['content_type'],
                                'allowed_type' => $fields[$diff_item]['allowed_type'],
                                'update_type' => $fields[$diff_item]['update_type'],
                                'fetch_type' => $fields[$diff_item]['fetch_type'],
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
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'wcbe_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['settings'])) {
            $setting_repository = Setting::get_instance();
            $setting_repository->update(Sanitizer::array($_POST['settings'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        }

        $this->redirect([
            'message' => esc_html__('Success !', 'ithemeland-woo-bulk-product-editor-lite'),
            'type' => 'success',
        ]);
    }

    public function export_products()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'wcbe_post_nonce')) {
            die('403 Forbidden');
        }

        if (empty($_POST['products']) || empty($_POST['fields'])) {
            $this->redirect([
                'message' => esc_html__('Error ! try again', 'ithemeland-woo-bulk-product-editor-lite'),
                'type' => 'danger',
            ]);
        }

        $export_service = Export_Service::get_instance();
        $export_service->set_data([
            'delimiter' => (!empty($_POST['wcbe_export_delimiter'])) ? sanitize_text_field(wp_unslash($_POST['wcbe_export_delimiter'])) : ',',
            'select_type' => sanitize_text_field(wp_unslash($_POST['products'])),
            'field_type' => sanitize_text_field(wp_unslash($_POST['fields'])),
            'selected_ids' => isset($_POST['item_ids']) ? Sanitizer::array($_POST['item_ids']) : [],  //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        ]);
        $export_service->perform();

        $this->redirect();
    }

    private function redirect($notice = [], $page = 'main')
    {
        if ($page == 'license') {
            if (!empty($notice) && isset($notice['message'])) {
                GlobalFlushMessage::set($notice);
            }
            wp_redirect(WCBE_ACTIVATION_PAGE);
            die();
        } else {
            if (!empty($notice) && isset($notice['message'])) {
                $flush_message_repository = new Flush_Message();
                $flush_message_repository->set($notice);
            }
            wp_redirect(WCBEL_PLUGIN_MAIN_PAGE);
            die();
        }
    }
}
