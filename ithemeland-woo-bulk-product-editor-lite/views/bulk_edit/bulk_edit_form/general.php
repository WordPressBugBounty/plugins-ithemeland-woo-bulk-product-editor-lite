<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $edit_form_items['general'];

foreach ($items as $name => $item):
    $field_id_key = isset($item['id']) ? $item['id'] : '';
    $field_id = 'wcbe-bulk-edit-form-product-' . esc_attr($field_id_key);
    $field_type = isset($item['field_type']) ? $item['field_type'] : 'text';
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-type="<?php echo esc_attr($item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($item['label']); ?></label>

            <?php if (!empty($item['operators'])): ?>
                <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($field_id); ?>-operator" data-field="operator" title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                    <?php foreach ($item['operators'] as $operator_name => $operator_label): ?>
                        <option value="<?php echo esc_attr($operator_name); ?>"><?php echo esc_html($operator_label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php
            $field_type_path = WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/fields_type/' . $field_type . '.php';
            if (file_exists($field_type_path)) {
                include $field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/fields_type/text.php';
            }
            ?>

            <?php if (isset($item['variable']) && $item['variable'] && !(isset($item['disabled']) && $item['disabled'])): ?>
                <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/variable.php"; ?>
            <?php endif; ?>
        </div>

        <?php if (isset($item['disabled']) && $item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>