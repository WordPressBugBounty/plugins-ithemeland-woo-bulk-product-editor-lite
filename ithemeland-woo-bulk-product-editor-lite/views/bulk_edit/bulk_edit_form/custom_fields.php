<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!empty($meta_fields)) :
    foreach ($meta_fields as $wcbel_custom_field) :
        if (!empty($acf_fields)) {
            if (!empty($acf_fields[$wcbel_custom_field['key']])) {
                $wcbel_custom_field['main_type'] = (!empty($acf_fields[$wcbel_custom_field['key']]['field_type'])) ? $acf_fields[$wcbel_custom_field['key']]['field_type'] : $acf_fields[$wcbel_custom_field['key']]['type'];
                if (in_array($wcbel_custom_field['main_type'], ['multi_select', 'image', 'file'])) {
                    continue;
                }
            } else {
                $wcbel_custom_field['main_type'] = $wcbel_custom_field['main_type'];
            }
        }

        if (!in_array($wcbel_custom_field['main_type'], [
            wcbel\classes\repositories\Meta_Field::TEXTINPUT,
            wcbel\classes\repositories\Meta_Field::TEXT,
            wcbel\classes\repositories\Meta_Field::NUMBER,
            wcbel\classes\repositories\Meta_Field::TEXTAREA,
            wcbel\classes\repositories\Meta_Field::PASSWORD,
            wcbel\classes\repositories\Meta_Field::EDITOR,
            wcbel\classes\repositories\Meta_Field::URL,
            wcbel\classes\repositories\Meta_Field::EMAIL,
            wcbel\classes\repositories\Meta_Field::CHECKBOX,
            wcbel\classes\repositories\Meta_Field::SELECT,
            wcbel\classes\repositories\Meta_Field::RADIO,
            wcbel\classes\repositories\Meta_Field::ARRAY_TYPE,
            wcbel\classes\repositories\Meta_Field::CALENDAR,
            wcbel\classes\repositories\Meta_Field::DATE_TIME,
            wcbel\classes\repositories\Meta_Field::TIME,
            wcbel\classes\repositories\Meta_Field::IMAGE,
            wcbel\classes\repositories\Meta_Field::FILE,
        ])) {
            continue;
        }

        $wcbel_field_id = "wcbe-bulk-edit-form-custom-field-" . esc_attr($wcbel_custom_field['key']);
?>
        <div class="wcbe-form-group" data-type="meta_field" data-name="<?php echo esc_attr($wcbel_custom_field['key']); ?>">
            <div>
                <label><?php echo esc_html($wcbel_custom_field['title']); ?></label>
                <?php
                if (($wcbel_custom_field['main_type'] == wcbel\classes\repositories\Meta_Field::TEXTINPUT && $wcbel_custom_field['sub_type'] == wcbel\classes\repositories\Meta_Field::STRING_TYPE)
                    || in_array($wcbel_custom_field['main_type'], [
                        wcbel\classes\repositories\Meta_Field::EMAIL,
                        wcbel\classes\repositories\Meta_Field::TEXT,
                        wcbel\classes\repositories\Meta_Field::PASSWORD,
                        wcbel\classes\repositories\Meta_Field::EDITOR,
                        wcbel\classes\repositories\Meta_Field::URL
                    ])
                ) : ?>
                    <select title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-field="operator">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/text.php"; ?>
                    </select>
                    <input type="text" data-field="value" id="<?php echo esc_attr($wcbel_field_id); ?>" placeholder="<?php echo esc_attr($wcbel_custom_field['title']); ?> ..." title="<?php echo esc_attr($wcbel_custom_field['title']); ?>">
                    <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/variable.php"; ?>
                <?php elseif (
                    ($wcbel_custom_field['main_type'] == wcbel\classes\repositories\Meta_Field::TEXTINPUT && $wcbel_custom_field['sub_type'] == wcbel\classes\repositories\Meta_Field::NUMBER)
                    ||
                    $wcbel_custom_field['main_type'] == wcbel\classes\repositories\Meta_Field::NUMBER
                ) : ?>
                    <select title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-field="operator">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
                    </select>
                    <input type="number" class="wcbe-input-md" data-field="value" id="<?php echo esc_attr($wcbel_field_id); ?>" title="<?php echo esc_attr($wcbel_custom_field['title']); ?>" placeholder="<?php echo esc_attr($wcbel_custom_field['title']); ?> ...">
                <?php elseif ($wcbel_custom_field['main_type'] == wcbel\classes\repositories\Meta_Field::TEXTAREA) : ?>
                    <select title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-field="operator">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/text.php"; ?>
                    </select>
                    <textarea data-field="value" id="<?php echo esc_attr($wcbel_field_id); ?>" placeholder="<?php echo esc_attr($wcbel_custom_field['title']); ?> ..."></textarea>
                <?php elseif ($wcbel_custom_field['main_type'] == wcbel\classes\repositories\Meta_Field::CHECKBOX) : ?>
                    <select id="<?php echo esc_attr($wcbel_field_id); ?>" data-field="value" class="wcbe-input-md" title="<?php esc_attr_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?> <?php echo esc_attr($wcbel_custom_field['title']); ?>">
                        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    </select>
                <?php elseif (
                    in_array($wcbel_custom_field['main_type'], [
                        wcbel\classes\repositories\Meta_Field::SELECT,
                        wcbel\classes\repositories\Meta_Field::RADIO,
                        wcbel\classes\repositories\Meta_Field::ARRAY_TYPE
                    ])
                ) : ?>
                    <select id="<?php echo esc_attr($wcbel_field_id); ?>" class="wcbe-input-md" data-field="value" title="<?php esc_attr_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?> <?php echo esc_attr($wcbel_custom_field['title']); ?>">
                        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                        <?php
                        if (!empty($wcbel_custom_field['key_value'])) :
                            $wcbel_options = wcbel\classes\helpers\Meta_Field::key_value_field_to_array($wcbel_custom_field['key_value']);
                            if (!empty($wcbel_options) && is_array($wcbel_options)) :
                                foreach ($wcbel_options as $wcbel_option_key => $wcbel_option_value) :
                        ?>
                                    <option value="<?php echo esc_attr($wcbel_option_key) ?>"><?php echo esc_html($wcbel_option_value); ?></option>
                        <?php
                                endforeach;
                            endif;
                        endif;
                        ?>
                    </select>
                <?php elseif (in_array($wcbel_custom_field['main_type'], [
                    wcbel\classes\repositories\Meta_Field::CALENDAR,
                    wcbel\classes\repositories\Meta_Field::DATE
                ])) : ?>
                    <input type="text" class="wcbe-input-md wcbe-datepicker" data-field="value" id="<?php echo esc_attr($wcbel_field_id); ?>" title="<?php echo esc_attr($wcbel_custom_field['title']); ?>" placeholder="<?php echo esc_attr($wcbel_custom_field['title']); ?> ...">
                <?php elseif (in_array($wcbel_custom_field['main_type'], [
                    wcbel\classes\repositories\Meta_Field::DATE_TIME
                ])) : ?>
                    <input type="text" class="wcbe-input-md wcbe-datepicker" data-field="value" id="<?php echo esc_attr($wcbel_field_id); ?>" title="<?php echo esc_attr($wcbel_custom_field['title']); ?>" placeholder="<?php echo esc_attr($wcbel_custom_field['title']); ?> ...">
                <?php elseif (in_array($wcbel_custom_field['main_type'], [
                    wcbel\classes\repositories\Meta_Field::TIME
                ])) : ?>
                    <input type="text" class="wcbe-input-md wcbe-datepicker" data-field="value" id="<?php echo esc_attr($wcbel_field_id); ?>" title="<?php echo esc_attr($wcbel_custom_field['title']); ?>" placeholder="<?php echo esc_attr($wcbel_custom_field['title']); ?> ...">
                <?php elseif (in_array($wcbel_custom_field['main_type'], [
                    wcbel\classes\repositories\Meta_Field::IMAGE
                ])) : ?>
                    <button type="button" data-type="single" class="wcbe-button wcbe-button-blue wcbe-float-left wcbe-open-uploader" data-target="#wcbe-bulk-edit-form-custom-field-<?php echo esc_attr($wcbel_custom_field['key']); ?>">
                        <?php esc_html_e('Choose image', 'ithemeland-woo-bulk-product-editor-lite') ?>
                    </button>
                    <input type="hidden" data-field="value" class="wcbe-bulk-edit-form-custom-field-<?php echo esc_attr($wcbel_custom_field['key']); ?>" id="wcbe-bulk-edit-form-custom-field-<?php echo esc_attr($wcbel_custom_field['key']); ?>">
                    <div id="wcbe-bulk-edit-form-custom-field-<?php echo esc_attr($wcbel_custom_field['key']); ?>-preview" class="wcbe-bulk-edit-form-item-image-preview"></div>
                <?php elseif (in_array($wcbel_custom_field['main_type'], [
                    wcbel\classes\repositories\Meta_Field::FILE
                ])) :
                    include WCBEL_VIEWS_DIR . 'bulk_edit/custom_field_files.php';
                endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="wcbe-alert wcbe-alert-warning">
        <span><?php esc_html_e('There is not any added Meta Fields, You can add new Meta Fields trough "Meta Fields" tab.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    </div>
<?php endif; ?>