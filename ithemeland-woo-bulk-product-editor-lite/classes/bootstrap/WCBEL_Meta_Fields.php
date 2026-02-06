<?php

namespace wcbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\repositories\Meta_Field;
use wcbel\classes\repositories\Product;

class WCBEL_Meta_Fields
{
    private static $instance;

    public static function init()
    {
        if (is_null(self::$instance)) {
            new self();
        }
    }

    private function __construct()
    {
        add_filter('wcbe_column_fields', [$this, 'add_meta_fields_to_column_manager']);
        add_filter('wcbe_column_fields', [$this, 'add_attributes_to_column_manager']);
    }

    public function add_meta_fields_to_column_manager($fields)
    {
        $meta_field_repository = Meta_Field::get_instance();
        $meta_fields = $meta_field_repository->get();
        if (!empty($meta_fields)) {
            foreach ($meta_fields as $meta_field) {
                switch ($meta_field['main_type']) {
                    case "textinput":
                        if ($meta_field['sub_type'] == 'string') {
                            $content_type = 'text';
                        } else {
                            $content_type = 'numeric';
                        }
                        break;
                    case 'textarea':
                    case 'editor':
                        $content_type = 'textarea';
                        break;
                    case 'array':
                        $content_type = 'select';
                        break;
                    case 'calendar':
                        $content_type = 'date';
                        break;
                    default:
                        $content_type = sanitize_text_field($meta_field['main_type']);
                        break;
                }

                $fields[$meta_field['key']] = [
                    'name' => $meta_field['key'],
                    'field_type' => 'custom_field',
                    'label' => $meta_field['title'],
                    'editable' => true,
                    'content_type' => $content_type,
                    'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                    'update_type' => 'meta_field',
                    'fetch_type' => 'meta_field'
                ];

                if (!empty($meta_field['key_value'])) {
                    $fields[$meta_field['key']]['options'] = [];
                    $options = explode('|', $meta_field['key_value']);
                    if (!empty($options)) {
                        foreach ($options as $key => $value) {
                            $fields[$meta_field['key']]['options'][sanitize_text_field($key)] = sanitize_text_field($value);
                        }
                    }
                }
            }
        }
        return $fields;
    }

    public function add_attributes_to_column_manager($fields)
    {
        $taxonomies = (Product::get_instance())->get_taxonomies();
        if (empty($taxonomies)) {
            return $fields;
        }

        foreach ($taxonomies as $key => $taxonomy) {
            $fields[$key] = [
                'name' => $key,
                'label' => $taxonomy['label'],
                'editable' => true,
                'content_type' => 'multi_select',
                'allowed_type' => ['simple', 'composite', 'variable', 'grouped', 'external', 'variation'],
                'update_type' => 'taxonomy',
                'fetch_type' => 'taxonomy'
            ];

            $fields[$key]['field_type'] = (strpos($key, 'pa_') !== false) ? 'attribute' : 'taxonomy';
        }

        return $fields;
    }
}
