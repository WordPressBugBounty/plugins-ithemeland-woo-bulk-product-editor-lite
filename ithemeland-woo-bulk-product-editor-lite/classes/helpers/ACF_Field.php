<?php

namespace wcbel\classes\helpers;

defined('ABSPATH') || exit();

/** Backwards-compatible ACF definition and value adapter. */
class ACF_Field
{
    const NON_VALUE_TYPES = ['accordion', 'tab', 'message'];
    const STRUCTURED_TYPES = ['group', 'repeater', 'flexible_content', 'clone'];
    const MULTIPLE_TYPES = ['checkbox', 'gallery', 'relationship', 'repeater', 'flexible_content'];

    public static function is_available()
    {
        return function_exists('acf_get_field') && function_exists('update_field');
    }

    public static function normalize_definition(array $field, $parent = '', $path = '')
    {
        $type = isset($field['type']) ? sanitize_key($field['type']) : 'text';
        $name = isset($field['name']) ? sanitize_key($field['name']) : '';
        $field['acf_key'] = isset($field['key']) ? sanitize_text_field($field['key']) : '';
        $field['acf_type'] = $type;
        $field['parent_name'] = sanitize_key($parent);
        $field['path'] = $path !== '' ? $path : $name;
        $field['multiple'] = self::is_multiple($field);
        $field['has_value'] = !in_array($type, self::NON_VALUE_TYPES, true);
        $field['structured'] = in_array($type, self::STRUCTURED_TYPES, true);
        $field['choices'] = !empty($field['choices']) && is_array($field['choices']) ? $field['choices'] : [];
        $field['return_format'] = isset($field['return_format']) ? sanitize_key($field['return_format']) : '';
        return $field;
    }

    public static function is_multiple(array $field)
    {
        $type = isset($field['type']) ? $field['type'] : '';
        return !empty($field['multiple'])
            || in_array($type, self::MULTIPLE_TYPES, true)
            || ($type === 'taxonomy' && in_array(isset($field['field_type']) ? $field['field_type'] : '', ['checkbox', 'multi_select'], true));
    }

    public static function normalize_input(array $field, $value)
    {
        $type = isset($field['type']) ? $field['type'] : '';
        if (is_string($value) && in_array($type, self::STRUCTURED_TYPES, true)) {
            $decoded = json_decode(wp_unslash($value), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }
        if ($type === 'true_false') {
            return in_array($value, [1, '1', true, 'true', 'yes', 'on'], true) ? 1 : 0;
        }
        if (self::is_multiple($field)) {
            if ($value === '' || $value === null) {
                return [];
            }
            $value = is_array($value) ? $value : array_map('trim', explode(',', (string) $value));
            return array_values(array_filter($value, static function ($item) {
                return $item !== '' && $item !== null;
            }));
        }
        if (in_array($type, ['date_picker', 'date_time_picker', 'time_picker'], true) && $value !== '') {
            $timestamp = strtotime((string) $value);
            if ($timestamp !== false) {
                $formats = ['date_picker' => 'Ymd', 'date_time_picker' => 'Y-m-d H:i:s', 'time_picker' => 'H:i:s'];
                return gmdate($formats[$type], $timestamp);
            }
        }
        if (in_array($type, ['image', 'file', 'post_object', 'page_link', 'user'], true) && is_array($value)) {
            if (isset($value['ID'])) return absint($value['ID']);
            if (isset($value['id'])) return absint($value['id']);
            if ($type === 'file' && isset($value[0]['url'])) {
                return function_exists('attachment_url_to_postid') ? attachment_url_to_postid($value[0]['url']) : $value[0]['url'];
            }
        }
        if (in_array($type, ['number', 'range'], true)) {
            return $value === '' ? '' : (float) $value;
        }
        return $value;
    }

    public static function update($product_id, array $field, $value)
    {
        $value = self::normalize_input($field, $value);
        $selector = !empty($field['key']) ? $field['key'] : (!empty($field['acf_key']) ? $field['acf_key'] : $field['name']);
        if (self::is_available() && $selector) {
            return update_field($selector, $value, absint($product_id));
        }
        return update_post_meta(absint($product_id), $field['name'], $value);
    }

    public static function display_value($value, array $field)
    {
        if ($value === '' || $value === null || $value === []) return '';
        if (is_scalar($value)) return (string) $value;
        return wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
