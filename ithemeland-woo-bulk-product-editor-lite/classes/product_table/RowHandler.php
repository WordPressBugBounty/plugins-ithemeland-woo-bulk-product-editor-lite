<?php

namespace wcbel\classes\product_table;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wcbel\classes\helpers\Meta_Field as Meta_Field_Helper;
use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\meta_field\ACF_Plugin_Fields;
use wcbel\classes\repositories\Column;
use wcbel\classes\repositories\Product;
use wcbel\classes\repositories\Setting;

class RowHandler
{
    const CELL_CONTENT_LIMIT = 30;

    private static $instance;
    private $sticky_first_columns;
    private $product_object;
    private $value;
    private $product_repository;
    private $column_key;
    private $decoded_column_key;
    private $column_data;
    private $field_type;
    private $fields_method;
    private $meta_fields;
    private $acf_fields;
    private $acf_fields_name;
    private $acf_taxonomy_name;
    private $enable_thumbnail_popup;
    private $display_cell_content;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $setting_repository = Setting::get_instance();
        $settings = $setting_repository->get_settings();
        $this->sticky_first_columns = isset($settings['sticky_first_columns']) ? $settings['sticky_first_columns'] : 'yes';
        $this->enable_thumbnail_popup = isset($settings['enable_thumbnail_popup']) ? $settings['enable_thumbnail_popup'] : 'yes';
        $this->display_cell_content = (!empty($settings['display_cell_content']) && $settings['display_cell_content'] == 'short') ? 'short' : 'long';
        $this->field_type = "";
        $this->fields_method = $this->get_fields_method();

        $acf = ACF_Plugin_Fields::get_instance('product');
        $this->acf_fields = $acf->get_fields();
        $this->acf_fields_name = (is_array($this->acf_fields)) ? array_keys($this->acf_fields) : [];
        $this->product_repository = Product::get_instance();
    }

    public function get_row($product_object, $columns)
    {
        if (!($product_object instanceof \WC_Product)) {
            return '';
        }

        $this->product_object = $product_object;
        $values = $this->product_repository->get_product_column_values($product_object, $columns);

        $output = '<tr data-item-id="' . esc_attr($product_object->get_id()) . '" data-item-type="' . esc_attr($product_object->get_type()) . '">';
        $output .= $this->get_static_columns();
        if (!empty($columns) && is_array($columns)) {
            foreach ($columns as $column_key => $column_data) {
                $this->column_key = $column_key;
                $this->column_data = $column_data;
                $this->value = $values[$column_data['name']];
                if (in_array($column_key, $this->acf_fields_name)) {
                    $this->column_data['content_type'] = Meta_Field_Helper::get_field_type_by_acf_type($this->acf_fields[$column_key])['column_type'];
                }
                $this->decoded_column_key = (substr($this->column_key, 0, 3) == 'pa_') ? strtolower(urlencode($this->column_key)) : urlencode($this->column_key);
                $output .= $this->get_field();
            }
        }

        $output .= "</tr>";
        return $output;
    }

    private function get_field()
    {
        $output = '';
        $this->field_type = '';

        $color = $this->get_column_colors_style();
        $sub_name = (!empty($this->column_data['sub_name'])) ? $this->column_data['sub_name'] : '';
        $update_type = (!empty($this->column_data['update_type'])) ? $this->column_data['update_type'] : '';
        $output .= '<td data-item-id="' . esc_attr($this->product_object->get_id()) . '" data-item-title="' . esc_attr($this->product_object->get_name()) . '" data-col-title="' . esc_attr($this->column_data['title']) . '" data-field="' . esc_attr($this->column_key) . '" data-field-type="' . esc_attr($this->field_type) . '" data-name="' . esc_attr($this->column_data['name']) . '" data-sub-name="' . esc_attr($sub_name) . '" data-update-type="' . esc_attr($update_type) . '" data-fetch-type="' . esc_attr($this->column_data['fetch_type']) . '" style="' . esc_attr($color['background']) . ' ' . esc_attr($color['text']) . '"';
        if ($this->column_data['editable'] === true && !in_array($this->column_data['content_type'], ['multi_select', 'multi_select_attribute'])) {
            $output .= ' data-content-type="' . esc_attr($this->column_data['content_type']) . '" data-action="inline-editable"';
        }
        $output .= '>';

        if (empty($this->column_data['allowed_type']) || (!empty($this->column_data['allowed_type']) && in_array($this->product_object->get_type(), $this->column_data['allowed_type']))) {
            if ($this->column_data['editable'] === true) {
                $generated = $this->generate_field();
                if (is_array($generated) && isset($generated['field']) && isset($generated['includes'])) {
                    $output .= $generated['field'];
                } else {
                    $output .= $generated;
                }
            } else {
                $output .= (is_array($this->value)) ? wp_kses(implode(',', $this->value), Sanitizer::allowed_html()) : wp_kses($this->value, Sanitizer::allowed_html());
            }
        } else {
            $value = '<i class="wcbe-icon-slash"></i>';
            $output .= $value;
        }

        $output .= '</td>';
        return $output;
    }

    private function get_column_colors_style()
    {
        $color['background'] = (!empty($this->column_data['background_color']) && $this->column_data['background_color'] != '#fff' && $this->column_data['background_color'] != '#ffffff') ? 'background:' . esc_attr($this->column_data['background_color']) . ';' : '';
        $color['text'] = (!empty($this->column_data['text_color'])) ? 'color:' . esc_attr($this->column_data['text_color']) . ';' : '';
        return $color;
    }

    private function generate_field()
    {
        if (isset($this->fields_method[$this->column_data['content_type']]) && method_exists($this, $this->fields_method[$this->column_data['content_type']])) {
            return $this->{$this->fields_method[$this->column_data['content_type']]}();
        } else {
            return (is_array($this->value)) ? implode(',', $this->value) : $this->value;
        }
    }

    private function get_id_column()
    {
        $output = '';
        if (Column::SHOW_ID_COLUMN === true) {
            $id_for_edit = ($this->product_object->get_type() == 'variation') ? $this->product_object->get_parent_id() : $this->product_object->get_id();
            $delete_type = 'trash';
            $delete_label = esc_html__('Delete product', 'ithemeland-woo-bulk-product-editor-lite');
            $restore_button = '';
            $view_button = '';
            $edit_button = '';

            if ($this->product_object->get_status() == 'trash') {
                $delete_type = 'permanently';
                $delete_label = esc_html__('Delete permanently', 'ithemeland-woo-bulk-product-editor-lite');
                $restore_button = '<button type="button" style="height: 28px;" class="wcbe-ml5 wcbe-button-flat wcbe-text-green wcbe-float-right wcbe-restore-item-btn" data-item-id="' . esc_attr($this->product_object->get_id()) . '" title="' . esc_html__('Restore', 'ithemeland-woo-bulk-product-editor-lite') . '"><span class="wcbe-icon-rotate-cw"></span></button>';
            } else {
                $view_button = '<a href="' . esc_url(get_the_permalink($this->product_object->get_id())) . '" target="_blank" title="' . esc_html__('View on site', 'ithemeland-woo-bulk-product-editor-lite') . '" style="height: 28px;" class="wcbe-item-view-icon wcbe-float-right wcbe-ml5"><span class="wcbe-icon-eye1" style="vertical-align: middle;"></span></a>';
                $edit_button = '<a href="' . admin_url("post.php?post=" . esc_attr($id_for_edit) . "&action=edit") . '" target="_blank" class="wcbe-ml5 wcbe-float-right" title="' . esc_html__('Edit product', 'ithemeland-woo-bulk-product-editor-lite') . '" style="height: 28px;"><span class="wcbe-icon-pencil" style="vertical-align: middle;"></span></a>';
            }

            $sticky_class = ($this->sticky_first_columns == 'yes') ? 'wcbe-td-sticky wcbe-td-sticky-id wcbe-gray-bg' : '';
            $output .= '<td data-item-id="' . esc_attr($this->product_object->get_id()) . '" data-item-title="' . esc_attr($this->product_object->get_name()) . '" data-col-title="ID" class="' . esc_attr($sticky_class) . '">';
            $output .= '<label class="wcbe-td140">';
            $output .= '<input type="checkbox" class="wcbe-check-item" data-item-type="' . esc_attr($this->product_object->get_type()) . '" value="' . esc_attr($this->product_object->get_id()) . '" title="Select Item">';
            $output .= esc_html($this->product_object->get_id());
            $output .= $restore_button;
            $output .= $view_button;
            $output .= '<button type="button" class="wcbe-ml5 wcbe-button-flat wcbe-float-right wcbe-text-red wcbe-delete-item-btn" data-delete-type="' . esc_attr($delete_type) . '" data-item-id="' . esc_attr($this->product_object->get_id()) . '" title="' . $delete_label . '"><span class="wcbe-icon-trash-2"></span></button>';
            $output .= $edit_button;
            $output .= "</label>";
            $output .= "</td>";
        }
        return $output;
    }

    private function get_static_columns()
    {
        $output = '';
        $output .= $this->get_id_column();
        $static_columns = Column::get_static_columns();
        if (!empty($static_columns)) {
            foreach ($static_columns as $static_column) {
                if (!isset($static_column['fetch_type']) || !isset($static_column['name'])) {
                    continue;
                }
                if ($static_column['name'] == 'title' && $this->product_object->get_type() == 'variation') {
                    $full_text = $value = $this->product_object->get_title() . ' - ' . wc_get_formatted_variation($this->product_object->get_variation_attributes(), true, false);
                } else {
                    $full_text = $value = $this->product_repository->get_product_field($this->product_object, $static_column['fetch_type'], $static_column['name']);
                }
                if ($this->display_cell_content == 'short' && strlen($value) > self::CELL_CONTENT_LIMIT) {
                    $value = mb_substr($value, 0, self::CELL_CONTENT_LIMIT) . '...';
                }
                $sticky_class = ($this->sticky_first_columns == 'yes') ? 'wcbe-td-sticky wcbe-td-sticky-title wcbe-gray-bg' : '';
                $output .= '<td class="' . esc_attr($sticky_class) . '" data-update-type="woocommerce_field" data-fetch-type="' . esc_attr($static_column['fetch_type']) . '" data-name="' . esc_attr($static_column['name']) . '" data-item-id="' . esc_attr($this->product_object->get_id()) . '" data-item-title="' . esc_attr($this->product_object->get_name()) . '" data-col-title="' . esc_attr($static_column['label']) . '" data-field="' . esc_attr($static_column['name']) . '" data-field-type="" data-content-type="text" data-action="inline-editable">';
                $output .= '<span data-action="inline-editable" data-full-text="' . esc_attr($full_text) . '" class="wcbe-td160">' . esc_html($value) . '</span>';
                $output .= '</td>';
            }
        }
        return $output;
    }

    private function get_fields_method()
    {
        return [
            'text' => 'text_field',
            'password' => 'text_field',
            'email' => 'text_field',
            'url' => 'text_field',
            'textarea' => 'textarea_field',
            'image' => 'image_field',
            'gallery' => 'gallery_field',
            'regular_price' => 'regular_price_field',
            'sale_price' => 'sale_price_field',
            'numeric' => 'numeric_field',
            'numeric_without_calculator' => 'numeric_without_calculator_field',
            'checkbox_dual_mode' => 'checkbox_dual_mode_field',
            'checkbox' => 'checkbox_dual_mode_field',
            'radio' => 'radio_field',
            'file' => 'select_custom_field_files_field',
            'select_files' => 'select_files_field',
            'select_author' => 'select_author_field',
            'select_products' => 'select_products_field',
            'select' => 'select_field',
            'yith_shop_vendor' => 'yith_shop_vendor_field',
            'wc_product_vendor' => 'wc_product_vendor_field',
            'date' => 'date_picker_field',
            'date_picker' => 'date_picker_field',
            'date_time_picker' => 'date_time_picker_field',
            'time_picker' => 'time_picker_field',
            'color_picker' => 'color_picker_field',
            'taxonomy' => 'multi_select_field',
            'multi_select' => 'multi_select_field',
            'yith_product_badge' => 'yith_product_badge_field',
            'ithemeland_badge' => 'ithemeland_badge_field',
            'yikes_custom_product_tabs' => 'yikes_custom_product_tabs_field',
            'it_wc_dynamic_pricing_select_roles' => 'it_wc_dynamic_pricing_select_roles_field',
            'it_pricing_rules_product' => 'it_pricing_rules_product_field',
            'it_wc_dynamic_pricing_all_fields' => 'it_wc_dynamic_pricing_all_fields_field',
        ];
    }

    private function text_field()
    {
        $full_text = $value = (is_array($this->value)) ? implode(',', $this->value) : $this->value;
        if ($this->display_cell_content == 'short' && strlen($value) > self::CELL_CONTENT_LIMIT) {
            $value = mb_substr($value, 0, self::CELL_CONTENT_LIMIT) . '...';
        }

        return "<span data-action='inline-editable' class='wcbe-td160' data-full-text='" . esc_attr($full_text) . "'>" . esc_html($value) . "</span>";
    }

    private function textarea_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-text-editor' class='wcbe-button wcbe-button-flat wcbe-load-text-editor' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function image_field()
    {
        $output = '';
        $image_id = 0;
        if (isset($this->value['id'])) {
            $image_id = intval($this->value['id']);
        }
        if (isset($this->value) && is_numeric($this->value)) {
            $image_id = intval($this->value);
        }

        $image_url = wp_get_attachment_image_src($image_id, [40, 40]);
        $large = wp_get_attachment_image_src($image_id, 'large');
        $large = (!empty($large[0])) ? $large[0] : false;
        $full_size = wp_get_attachment_image_src($image_id, 'full');
        $full_size = (!empty($full_size[0])) ? $full_size[0] : esc_url(WCBEL_IMAGES_URL . "/woocommerce-placeholder.png");
        $image = (!empty($image_url[0])) ? "<img src='" . esc_url($image_url[0]) . "' alt='' width='40' height='40' />" : '<button class="wcbe-button wcbe-button-flat"><i class="wcbe-icon-image"></i></button>';
        $hover_box_class = ($this->enable_thumbnail_popup == 'yes') ? 'wcbe-thumbnail' : '';
        $output .= "<span data-toggle='modal' class='{$hover_box_class}' data-target='#wcbe-modal-image' data-id='wcbe-" . esc_attr($this->column_key) . "-" . esc_attr($this->product_object->get_id()) . "' class='wcbe-image-inline-edit' data-full-image-src='" . esc_url($full_size) . "' data-image-id='" . esc_attr($image_id) . "'>";
        if ($this->enable_thumbnail_popup == 'yes' && !empty($large)) {
            $output .= '<div class="wcbe-original-thumbnail"><img src="' . esc_url($large) . '"></div>';
        }
        $output .= $image;
        $output .= "</span>";
        return $output;
    }

    private function gallery_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-gallery' data-item-name='" . esc_attr($this->product_object->get_name()) . "' class='wcbe-button wcbe-button-flat wcbe-button-gallery' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-field='" . esc_attr($this->column_key) . "'><i class='wcbe-icon-images'></i></button>";
    }

    private function regular_price_field()
    {
        $price = ($this->value != '') ? number_format(floatval($this->value), wc_get_price_decimals()) : '';
        $output = "<span data-action='inline-editable' class='wcbe-numeric-content wcbe-td120'>" . esc_html($price) . "</span><button type='button' data-toggle='modal' class='wcbe-calculator' data-target='#wcbe-modal-regular-price'></button>";
        return $output;
    }

    private function sale_price_field()
    {
        $price = ($this->value != '') ? number_format(floatval($this->value), wc_get_price_decimals()) : '';
        $output = "<span data-action='inline-editable' class='wcbe-numeric-content wcbe-td120'>" . esc_html($price) . "</span><button type='button' data-toggle='modal' class='wcbe-calculator' data-target='#wcbe-modal-sale-price'></button>";
        return $output;
    }

    private function numeric_field()
    {
        $field_arr = explode('_-_', $this->decoded_column_key);
        $field = (!empty($field_arr[0])) ? $field_arr[0] : $this->decoded_column_key;

        if (!empty($field_arr[1]) && is_array($this->value)) {
            if (!empty($this->value[0])) {
                $decoded = json_decode($this->value[0], true);
                $this->value = (is_array($decoded) && isset($decoded[$field_arr[1]])) ? $decoded[$field_arr[1]] : '';
            } else {
                $this->value = '';
            }
        }

        return "<span data-action='inline-editable' class='wcbe-numeric-content wcbe-td120'>" . esc_html($this->value) . "</span><button type='button' data-toggle='modal' class='wcbe-calculator' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field-type='" . esc_attr($this->field_type) . "' data-target='#wcbe-modal-numeric-calculator'></button>";
    }

    private function numeric_without_calculator_field()
    {
        return "<span data-action='inline-editable' class='wcbe-numeric-content wcbe-td120'>" . esc_html($this->value) . "</span>";
    }

    private function checkbox_dual_mode_field()
    {
        $checked =  ($this->value && $this->value !== 'no') ? 'checked="checked"' : '';
        return "<label><input type='checkbox' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' value='yes' class='wcbe-dual-mode-checkbox wcbe-inline-edit-action' " . esc_attr($checked) . "><span>" . esc_html__('Yes', 'ithemeland-woo-bulk-product-editor-lite') . "</span></label>";
    }

    private function checkbox_field()
    {
        $output = "";
        if (!empty($this->acf_fields[$this->decoded_column_key]['choices']) && is_array($this->acf_fields[$this->decoded_column_key]['choices'])) {
            foreach ($this->acf_fields[$this->decoded_column_key]['choices'] as $choice_key => $choice_value) {
                $selected = isset($this->value) ? unserialize($this->value) : null;
                $checked = !empty($selected) && is_array($selected) && in_array($choice_key, $selected) ? 'checked="checked"' : '';
                $output = "<label><input type='checkbox' name='" . esc_attr($this->decoded_column_key . '-' . $this->product_object->get_id()) . "' value='" . esc_attr($choice_key) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' class='wcbe-dual-mode-checkbox wcbe-inline-edit-action' " . esc_attr($checked) . ">" . esc_html($choice_value) . "</label>";
            }
        }
        return $output;
    }

    private function radio_field()
    {
        $output = '';
        if (!empty($this->acf_fields[$this->decoded_column_key]['choices']) && is_array($this->acf_fields[$this->decoded_column_key]['choices'])) {
            foreach ($this->acf_fields[$this->decoded_column_key]['choices'] as $choice_key => $choice_value) {
                $checked = isset($this->value) && $this->value == $choice_key ? 'checked="checked"' : '';
                $output .= "<label><input type='radio' name='" . esc_attr($this->decoded_column_key . '-' . $this->product_object->get_id()) . "' value='" . esc_attr($choice_key) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' class='wcbe-dual-mode-checkbox wcbe-inline-edit-action' " . esc_attr($checked) . ">" . esc_html($choice_value) . "</label>";
            }
        }

        if (empty($this->meta_fields)) {
            $this->meta_fields = $this->get_meta_fields();
        }

        if (!empty($this->meta_fields[$this->decoded_column_key]) && !empty($this->meta_fields[$this->decoded_column_key]['key_value'])) {
            $options = Meta_Field_Helper::key_value_field_to_array($this->meta_fields[$this->decoded_column_key]['key_value']);
            if (!empty($options) && is_array($options)) {
                $output .= "<select class='wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
                $output .= '<option value="">Select</option>';
                foreach ($options as $option_key => $option_value) {
                    $selected = isset($this->value) && $this->value == $option_key ? 'selected' : '';
                    $output .= "<option value='{$option_key}' $selected>{$option_value}</option>";
                }
                $output .= '</select>';
            }
        }

        return $output;
    }

    private function file_field()
    {
        $file_id = (isset($this->value)) ? intval($this->value) : null;
        $file_url = !empty($file_id) ? wp_get_attachment_url($file_id) : 0;
        $file_url = !empty($file_url) ? esc_url($file_url) : '';
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-file' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-file-id='" . $file_id . "' data-file-url='" . $file_url . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function select_custom_field_files_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-custom-field-files' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function select_files_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-select-files' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function select_author_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-select-author' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function select_products_field()
    {
        $children_ids = '';
        switch ($this->column_key) {
            case '_children':
                if (is_array($this->value)) {
                    $children_ids = implode(',', $this->value);
                } else {
                    $children_ids = (!empty(unserialize($this->value))) ? implode(',', unserialize($this->value)) : '';
                }
                break;
            case 'upsell_ids':
            case 'cross_sell_ids':
                if (!empty($this->value) && is_array($this->value)) {
                    $children_ids = implode(',', $this->value);
                }
                break;
        }
        return  "<button type='button' data-toggle='modal' data-target='#wcbe-modal-select-products' class='wcbe-button wcbe-button-flat' data-children-ids='" . esc_attr($children_ids) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function select_field()
    {
        $output = "<select class='wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
        if (isset($this->column_data['options']) && is_array($this->column_data['options'])) {
            $product_repository = Product::get_instance();
            switch ($this->column_key) {
                case 'status':
                    $this->column_data['options'] = $product_repository->get_product_statuses();
                    break;
                case 'stock_status':
                    $this->column_data['options'] = wc_get_product_stock_status_options();
                    break;
                case 'backorders':
                    $this->column_data['options'] = wc_get_product_backorder_options();
                    break;
                case 'product_type':
                    $this->column_data['options'] = wc_get_product_types();
                    break;
                case 'catalog_visibility':
                    $this->column_data['options'] = wc_get_product_visibility_options();
                    break;
                case 'tax_class':
                    $this->column_data['options'] = $product_repository->get_tax_classes();
                    if ($this->product_object->get_type() == 'variation') {
                        $this->column_data['options'] = ['parent' => esc_html__('Same as parent', 'ithemeland-woo-bulk-product-editor-lite')] + $this->column_data['options'];
                        $variation_tax_class = $this->product_object->get_tax_class();
                    }
                    break;
                case 'shipping_class':
                    $this->column_data['options'] = [
                        -1 => 'No Shipping Class',
                    ];
                    $shipping_items = wc()->shipping()->get_shipping_classes();
                    if (!empty($shipping_items)) {
                        foreach ($shipping_items as $shipping_class) {
                            $this->column_data['options'][$shipping_class->term_id] = $shipping_class->name;
                        }
                    }
                    break;
            }

            foreach ($this->column_data['options'] as $option_key => $option_value) {
                if (!empty($variation_tax_class)) {
                    $selected = ($option_key == $variation_tax_class) ? 'selected' : '';
                } else {
                    if (is_array($this->value)) {
                        $selected = (in_array($option_key, $this->value)) ? 'selected' : '';
                    } else {
                        $selected = ($option_key == $this->value) ? 'selected' : '';
                    }
                }
                $output .= "<option value='{$option_key}' $selected>{$option_value}</option>";
            }
        } else {
            if ($this->column_data['field_type'] == 'custom_field') {
                if (empty($this->meta_fields)) {
                    $this->meta_fields = $this->get_meta_fields();
                }

                if (!empty($this->meta_fields[$this->column_data['name']]) && !empty($this->meta_fields[$this->column_data['name']]['key_value'])) {
                    $options = Meta_Field_Helper::key_value_field_to_array($this->meta_fields[$this->column_data['name']]['key_value']);
                    if (!empty($options) && is_array($options)) {
                        foreach ($options as $option_key => $option_value) {
                            $selected = isset($this->value) && $this->value == $option_key ? 'selected' : '';
                            $output .= "<option value='{$option_key}' $selected>{$option_value}</option>";
                        }
                    }
                }
            }
        }

        if (!empty($this->acf_fields[$this->decoded_column_key]['choices']) && is_array($this->acf_fields[$this->decoded_column_key]['choices'])) {
            foreach ($this->acf_fields[$this->decoded_column_key]['choices'] as $choice_key => $choice_value) {
                $selected = isset($this->value) && $this->value == $choice_key ? 'selected' : '';
                $output .= "<option value='" . esc_attr($choice_key) . "' $selected>" . esc_html($choice_value) . "</option>";
            }
        }

        if (!empty($this->acf_fields[$this->decoded_column_key]['taxonomy'])) {
            $options = $this->get_taxonomy_terms($this->acf_fields[$this->decoded_column_key]['taxonomy']);
            if (!empty($options) && count($options)) {
                foreach ($options as $option_key => $option_value) {
                    $selected = isset($this->value) && $this->value == $option_key ? 'selected' : '';
                    $output .= "<option value='" . esc_attr($option_key) . "' $selected>" . esc_html($option_value) . "</option>";
                }
            }
        }

        $output .= '</select>';

        return $output;
    }

    private function yith_shop_vendor_field()
    {
        $output = "<select class='wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
        $output .= "<option value=''>" . esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite') . "</option>";
        $product_repository = Product::get_instance();
        $yith_shop_vendor_object = $product_repository->get_yith_vendors();
        if (!empty($yith_shop_vendor_object)) {
            foreach ($yith_shop_vendor_object as $vendor) {
                if ($vendor instanceof \WP_Term) {
                    $selected = (in_array($vendor->slug, $this->value)) ? 'selected' : '';
                    $output .= "<option value='{$vendor->slug}' $selected>{$vendor->name}</option>";
                }
            }
        }
        $output .= '</select>';
        return $output;
    }

    private function wc_product_vendor_field()
    {
        $output = "<select class='wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
        $output .= "<option value=''>" . esc_html__('Select', 'ithemeland-woo-bulk-product-editor-lite') . "</option>";
        $product_repository = Product::get_instance();
        $wc_product_vendor_object = $product_repository->get_wc_product_vendors();
        if (!empty($wc_product_vendor_object)) {
            foreach ($wc_product_vendor_object as $vendor) {
                if ($vendor instanceof \WP_Term) {
                    $selected = (in_array($vendor->slug, $this->value)) ? 'selected' : '';
                    $output .= "<option value='{$vendor->slug}' $selected>{$vendor->name}</option>";
                }
            }
        }
        $output .= '</select>';
        return $output;
    }

    private function date_picker_field()
    {
        $date = (!empty($this->value)) ? gmdate('Y/m/d', strtotime($this->value)) : '';
        $clear_button = ($this->decoded_column_key != 'date_created') ? "<button type='button' class='wcbe-clear-date-btn wcbe-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' value=''><img src='" . esc_url(WCBEL_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>" : '';
        return "<input type='text' class='wcbe-datepicker wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($date) . "'>" . wp_kses($clear_button, Sanitizer::allowed_html());
    }

    private function date_time_picker_field()
    {
        $date = (!empty($this->value)) ? gmdate('Y/m/d H:i', strtotime($this->value)) : '';
        $clear_button = "<button type='button' class='wcbe-clear-date-btn wcbe-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' value=''><img src='" . esc_url(WCBEL_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>";
        return "<input type='text' class='wcbe-datetimepicker wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($date) . "'>" . wp_kses($clear_button, Sanitizer::allowed_html());
    }

    private function time_picker_field()
    {
        $date = (!empty($this->value)) ? gmdate('H:i', strtotime($this->value)) : '';
        $clear_button = "<button type='button' class='wcbe-clear-date-btn wcbe-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' value=''><img src='" . esc_url(WCBEL_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>";
        return "<input type='text' class='wcbe-timepicker wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($date) . "'>" . wp_kses($clear_button, Sanitizer::allowed_html());
    }

    private function color_picker_field()
    {
        return "<input type='text' class='wcbe-color-picker-field wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($this->value) . "'><button type='button' class='wcbe-inline-edit-color-action'>" . esc_html__('Apply', 'ithemeland-woo-bulk-product-editor-lite') . "</button>";
    }

    private function multi_select_field()
    {
        $values = '';

        if (isset($this->acf_fields[$this->column_key]['taxonomy'])) {
            $taxonomy = esc_attr($this->acf_fields[$this->column_key]['taxonomy']);
            $checked_ids = !is_array($this->value) ? unserialize($this->value) : $this->value;
            if (!empty($checked_ids)) {
                $checked = get_terms([
                    'taxonomy' => $this->acf_taxonomy_name,
                    'hide_empty' => false,
                    'include' => $checked_ids,
                    'fields' => 'id=>name'
                ]);
            }
        } else {
            $taxonomy = $this->column_key;
            $checked = wp_get_post_terms($this->product_object->get_id(), $taxonomy, ['fields' => 'names']);
        }

        if (!empty($checked) && is_array($checked)) {
            $checked_iteration = 1;
            foreach ($checked as $id => $name) {
                $separate = '';
                if ($this->display_cell_content == 'short') {
                    if ($checked_iteration < 2 && count($checked) > 1) {
                        $separate = ", ";
                    }
                    $values .= '<span class="wcbe-category-item">' . esc_html($name) . $separate . ' </span>';
                    if ($checked_iteration >= 2 && count($checked) > 2) {
                        $values .= '...';
                        break;
                    }
                } else {
                    if ($checked_iteration < count($checked)) {
                        $separate = ", ";
                    }
                    $values .= '<span class="wcbe-category-item">' . esc_html($name) . $separate . ' </span>';
                }

                $checked_iteration++;
            }
        }

        $list_html = strip_tags(wp_kses($values, Sanitizer::allowed_html()), '<span><ul><label><li>');
        if (mb_substr($this->decoded_column_key, 0, 3) == 'pa_') {
            $output = "<span data-toggle='modal' class='wcbe-is-attribute-modal wcbe-product-attribute' data-target='#wcbe-modal-product-attribute' data-item-id='" . esc_attr($this->product_object->get_id()) . "'>";
            $output .= (!empty($list_html)) ? $list_html : 'No items';
            $output .= "</span>";
        } else {
            if (isset($this->acf_fields[$this->column_key]['taxonomy'])) {
                $output = "<span data-toggle='modal' class='wcbe-is-taxonomy-modal wcbe-acf-taxonomy-multi-select' data-target='#wcbe-modal-acf-multi-select' data-item-id='" . esc_attr($this->product_object->get_id()) . "'>";
                $output .= (!empty($list_html)) ? $list_html : 'No items';
                $output .= "</span>";
            } else {
                $output = "<span data-toggle='modal' class='wcbe-is-taxonomy-modal' data-target='#wcbe-modal-product-taxonomy' data-item-id='" . esc_attr($this->product_object->get_id()) . "'>";
                $output .= (!empty($list_html)) ? $list_html : 'No items';
                $output .= "</span>";
            }
        }

        return $output;
    }

    private function yith_product_badge_field()
    {
        $output = "";
        if (defined("YITH_WCBM_INIT")) {
            // is premium plugin - multiple
            $output = "<button type='button' data-toggle='modal' data-target='#wcbe-modal-product-badges' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
        } else {
            // is free plugin - single
            $product_badges = get_posts(['post_type' => 'yith-wcbm-badge', 'posts_per_page' => -1, 'order' => 'ASC']);
            $output = "<select class='wcbe-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->product_object->get_id()) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
            $output .= "<option value=''>" . esc_html__('No badge', 'ithemeland-woo-bulk-product-editor-lite') . "</option>";
            if (!empty($product_badges)) {
                foreach ($product_badges as $badge) {
                    if ($badge instanceof \WP_Post) {
                        if (is_array($this->value)) {
                            $selected = (in_array($badge->ID, $this->value)) ? 'selected' : '';
                        } else {
                            $selected = ($badge->ID == $this->value) ? 'selected' : '';
                        }
                        $output .= "<option value='{$badge->ID}' $selected>{$badge->post_title}</option>";
                    }
                }
            }
            $output .= '</select>';
        }

        return $output;
    }

    private function ithemeland_badge_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-ithemeland-badge' class='wcbe-button wcbe-button-flat wcbe-ithemeland-badge-button' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function yikes_custom_product_tabs_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-yikes-custom-product-tabs' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function it_wc_dynamic_pricing_all_fields_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-it-wc-dynamic-pricing-all-fields' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "'data-item-type='" . esc_attr($this->product_object->get_type()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function it_pricing_rules_product_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-it-wc-dynamic-pricing' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function it_wc_dynamic_pricing_select_roles_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wcbe-modal-it-wc-dynamic-pricing-select-roles' class='wcbe-button wcbe-button-flat' data-item-id='" . esc_attr($this->product_object->get_id()) . "' data-item-name='" . esc_attr($this->product_object->get_name()) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'><i class='wcbe-icon-edit'></i></button>";
    }

    private function get_taxonomy_terms($taxonomy)
    {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        $options = [];
        if (!empty($terms) && count($terms)) {
            foreach ($terms as $term) {
                if ($term instanceof \WP_Term) {
                    $options[$term->term_id] = $term->name;
                }
            }
        }

        return $options;
    }

    private function get_meta_fields()
    {
        return get_option('wcbe_meta_fields', []);
    }
}
