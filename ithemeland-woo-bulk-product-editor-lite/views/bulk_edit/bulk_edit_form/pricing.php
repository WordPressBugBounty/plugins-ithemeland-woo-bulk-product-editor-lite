<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_items = $edit_form_items['pricing'];

foreach ($wcbel_items as $wcbel_name => $wcbel_item):
    $wcbel_field_id = 'wcbe-bulk-edit-form-' . $wcbel_item['id'];
    $wcbel_field_type = isset($wcbel_item['field_type']) ? $wcbel_item['field_type'] : 'text';
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-type="<?php echo esc_attr($wcbel_item['update_type']); ?>">
        <div>
            <label for="<?php echo esc_attr($wcbel_field_id); ?>"><?php echo esc_html($wcbel_item['label']); ?></label>

            <?php if (!empty($wcbel_item['operators'])): ?>
                <select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($wcbel_field_id); ?>-operator" data-field="operator">
                    <?php foreach ($wcbel_item['operators'] as $wcbel_operator_name => $wcbel_operator_label): ?>
                        <option value="<?php echo esc_attr($wcbel_operator_name); ?>"><?php echo esc_html($wcbel_operator_label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php
            $wcbel_field_type_path = WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/fields_type/' . $wcbel_field_type . '.php';
            if (file_exists($wcbel_field_type_path)) {
                include $wcbel_field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_edit/bulk_edit_form/fields_type/text.php';
            }
            ?>

            <?php if (isset($wcbel_item['has_rounding']) && $wcbel_item['has_rounding']): ?>
                <select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> data-field="round" id="<?php echo esc_attr($wcbel_field_id); ?>-round-item" title="<?php esc_attr_e('Select round item', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                    <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/round_items.php"; ?>
                </select>
            <?php endif; ?>

            <?php if (isset($wcbel_item['formula_note']) && $wcbel_item['formula_note']): ?>
                <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            <?php endif; ?>
        </div>

        <?php if (isset($wcbel_item['disabled']) && $wcbel_item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>