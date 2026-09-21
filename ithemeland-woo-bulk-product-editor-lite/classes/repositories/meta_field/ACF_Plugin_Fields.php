<?php

namespace wcbel\classes\repositories\meta_field;

defined('ABSPATH') || exit();

class ACF_Plugin_Fields
{
    private static $instances = [];
    private $grouped_fields;
    private $fields;
    private $post_type;

    public static function get_instance($post_type)
    {
        $post_type = sanitize_key($post_type);
        if (!isset(self::$instances[$post_type])) {
            self::$instances[$post_type] = new self($post_type);
        }
        return self::$instances[$post_type];
    }

    private function __construct($post_type)
    {
        $this->post_type = esc_sql($post_type);
        $this->set_fields();
    }

    public function get_fields()
    {
        return $this->fields;
    }
    public function get_grouped_fields()
    {
        return $this->grouped_fields;
    }

    private function set_fields()
    {
        if (!function_exists('acf_get_field_groups')) {
            $this->fields = [];
            return false;
        }

        $grouped_fields = [];
        $fields = [];
        $acf_groups = acf_get_field_groups(array('post_type' => $this->post_type));
        if ($this->post_type === 'product') {
            $variation_groups = acf_get_field_groups(array('post_type' => 'product_variation'));
            if (is_array($variation_groups)) $acf_groups = array_merge((array) $acf_groups, $variation_groups);
        }
        if ($acf_groups) $acf_groups = array_column($acf_groups, null, 'key');
        if (!empty($acf_groups) && is_array($acf_groups)) {
            foreach ($acf_groups as $acf_group) {
                if (isset($acf_group['key'])) {
                    $group_fields = acf_get_fields($acf_group['key']);
                    if (!empty($group_fields) && is_array($group_fields)) {
                        $grouped_fields[$acf_group['key']] = $acf_group;
                        $this->flatten_fields($group_fields, $fields, $grouped_fields[$acf_group['key']]['fields']);
                    }
                }
            }
        }

        $this->grouped_fields = $grouped_fields;
        $this->fields = $fields;
        return true;
    }

    private function flatten_fields(array $source, array &$fields, array &$group_fields, $parent = '', $path = '')
    {
        foreach ($source as $field) {
            if (empty($field['name']) || empty($field['type'])) continue;
            $field_path = $path === '' ? $field['name'] : $path . '.' . $field['name'];
            $field = \wcbel\classes\helpers\ACF_Field::normalize_definition($field, $parent, $field_path);
            if (!$field['has_value']) continue;
            $group_fields[] = $field;
            $fields[$field['name']] = $field;
            if (!empty($field['key'])) $fields[$field['key']] = $field;
            if (!empty($field['sub_fields']) && is_array($field['sub_fields'])) {
                $this->flatten_fields($field['sub_fields'], $fields, $group_fields, $field['name'], $field_path);
            }
            if (!empty($field['layouts']) && is_array($field['layouts'])) {
                foreach ($field['layouts'] as $layout) {
                    if (!empty($layout['sub_fields']) && is_array($layout['sub_fields'])) {
                        $this->flatten_fields($layout['sub_fields'], $fields, $group_fields, $field['name'], $field_path . '.' . sanitize_key($layout['name']));
                    }
                }
            }
        }
    }

    public function __wakeup() {}

    public function __clone() {}
}
