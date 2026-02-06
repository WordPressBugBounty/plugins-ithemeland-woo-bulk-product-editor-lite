<?php

namespace wcbel\classes\repositories\meta_field;

defined('ABSPATH') || exit();

class Meta_Field_Main
{
    protected $meta_fields_option_name;

    const TEXTINPUT = "textinput";
    const TEXT = "text";
    const TEXTAREA = "textarea";
    const CHECKBOX = "checkbox";
    const RADIO = "radio";
    const ARRAY_TYPE = "array";
    const CALENDAR = "calendar";
    const EMAIL = "email";
    const PASSWORD = "password";
    const URL = "url";
    const IMAGE = "image";
    const FILE = "file";
    const EDITOR = "editor";
    const SELECT = "select";
    const MULTI_SELECT = "multi_select";
    const TAXONOMY = "taxonomy";
    const COLOR = "color_picker";
    const DATE = "date_picker";
    const DATE_TIME = "date_time_picker";
    const TIME = "time_picker";

    const STRING_TYPE = "string";
    const NUMBER = "number";

    public static function get_fields_name_have_operator()
    {
        return [
            // self::TEXTAREA,
            self::EDITOR,
            self::EMAIL,
            self::PASSWORD,
            self::URL,
            self::ARRAY_TYPE,
        ];
    }

    public static function get_main_types()
    {
        return [
            self::TEXTINPUT => esc_html__('TextInput', 'ithemeland-woo-bulk-product-editor-lite'),
            self::TEXTAREA => esc_html__('TextArea', 'ithemeland-woo-bulk-product-editor-lite'),
            self::CHECKBOX => esc_html__('Checkbox', 'ithemeland-woo-bulk-product-editor-lite'),
            self::RADIO => esc_html__('Radio', 'ithemeland-woo-bulk-product-editor-lite'),
            self::ARRAY_TYPE => esc_html__('Array', 'ithemeland-woo-bulk-product-editor-lite'),
            self::CALENDAR => esc_html__('Calendar', 'ithemeland-woo-bulk-product-editor-lite'),
            self::EMAIL => esc_html__('Email', 'ithemeland-woo-bulk-product-editor-lite'),
            self::PASSWORD => esc_html__('Password', 'ithemeland-woo-bulk-product-editor-lite'),
            self::URL => esc_html__('Url', 'ithemeland-woo-bulk-product-editor-lite'),
            self::IMAGE => esc_html__('Image', 'ithemeland-woo-bulk-product-editor-lite'),
            self::FILE => esc_html__('File', 'ithemeland-woo-bulk-product-editor-lite'),
            self::EDITOR => esc_html__('Editor', 'ithemeland-woo-bulk-product-editor-lite'),
            self::SELECT => esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function get_supported_acf_field_types()
    {
        return [
            'text',
            'textarea',
            'number',
            'checkbox',
            'radio',
            'email',
            'image',
            'file',
            'select',
            'multi_select',
            'wysiwyg',
            'password',
            'url',
            'taxonomy',
            'date_picker',
            'date_time_picker',
            'time_picker',
            'color_picker',
        ];
    }

    public static function get_sub_types()
    {
        return [
            self::STRING_TYPE => esc_html__('String', 'ithemeland-woo-bulk-product-editor-lite'),
            self::NUMBER => esc_html__('Number', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public function update(array $meta_fields)
    {
        return update_option($this->meta_fields_option_name, $meta_fields);
    }

    public function get()
    {
        $meta_fields = get_option($this->meta_fields_option_name);
        return !empty($meta_fields) ? $meta_fields : [];
    }
}
