<?php

namespace wcbel\classes\helpers;

class Generator
{
    public static function license_hash($license_data, $product_id)
    {
        return (empty($license_data) || !isset($license_data['license_key']) || !isset($license_data['email']) || empty($_SERVER['SERVER_NAME']))
            ? md5(wp_rand(100000, 999999))
            : md5($license_data['license_key'] . sanitize_text_field($product_id) . sanitize_email($license_data['email']) . sanitize_text_field($_SERVER['SERVER_NAME'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    }

    public static function div_field_start($attributes = [])
    {
        $output = "<div";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function div_field_end()
    {
        return "</div>";
    }

    public static function label_field($attributes, $label_text)
    {
        $output = "<label";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        if (!empty($label_text)) {
            $output .= esc_html($label_text);
        }
        $output .= "</label>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function label_with_input_field($label_attributes, $label_text, $input_attributes, $input_position = 'before')
    {
        $input_field = self::input_field($input_attributes);
        $output = "<label";
        $output .= self::get_field_attributes($label_attributes);
        $output .= ">";
        if ($input_position == 'before') {
            $output .= $input_field;
        }
        if (!empty($label_text)) {
            $output .= esc_html($label_text);
        }
        if ($input_position == 'after') {
            $output .= $input_field;
        }
        $output .= "</label>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function help_icon($text)
    {
        $output = "";
        if (!empty($text)) {
            $output = "<span class='wcbe-field-help dashicons dashicons-info' title='{$text}'></span>";
        }
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function select_field($attributes, $options, $first_select_option = false)
    {
        $output = "<select";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        if ($first_select_option) {
            $output .= "<option value=''>" . esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite') . "</option>";
        }
        if (!empty($options) && is_array($options)) {
            foreach ($options as $key => $value) {
                $output .= '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
            }
        }
        $output .= "</select>";

        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function textarea_field($attributes, $value = "")
    {
        $output = "<textarea";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        if (!empty($value)) {
            $output .= $value;
        }
        $output .= "</textarea>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function input_field($attributes)
    {
        $output = "<input";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function span_field($text, $attributes = [])
    {
        $output = "<span";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        $output .= esc_html($text);
        $output .= "</span>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function strong_field($text, $attributes = [])
    {
        $output = "<strong";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        $output .= esc_html($text);
        $output .= "</strong>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function hr($attributes = [])
    {
        $output = "<hr";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        $output .= "</hr>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    public static function button($text, $attributes = [])
    {
        $output = "<button";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        $output .= esc_html($text);
        $output .= "</button>";
        return wp_kses($output, Sanitizer::allowed_html());
    }

    private static function get_field_attributes($attributes = [])
    {
        $output = "";
        if (!empty($attributes) && is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $output .= " " . esc_attr($key) . '="' . esc_attr($value) . '"';
            }
        }
        return $output;
    }
}
