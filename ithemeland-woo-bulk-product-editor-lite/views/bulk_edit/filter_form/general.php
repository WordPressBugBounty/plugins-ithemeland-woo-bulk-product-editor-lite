<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_items = $filter_form_items['general'];

foreach ($wcbel_items as $wcbel_name => $wcbel_item):
    $wcbel_field_id = 'wcbe-filter-form-product-' . $wcbel_item['id'];
    $wcbel_field_type = isset($wcbel_item['field_type']) ? $wcbel_item['field_type'] : 'text';
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($wcbel_name); ?>" data-filter-type="<?php echo esc_attr($wcbel_item['filter_type']); ?>" data-field-type="<?php echo esc_attr($wcbel_field_type); ?>">
        <label for="<?php echo esc_attr($wcbel_field_id); ?>"><?php echo esc_html($wcbel_item['label']); ?></label>

        <?php if (!empty($wcbel_item['operators'])): ?>
            <select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($wcbel_field_id); ?>-operator" data-field="operator" title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                <?php foreach ($wcbel_item['operators'] as $wcbel_operator_name => $wcbel_operator_label): ?>
                    <option value="<?php echo esc_attr($wcbel_operator_name); ?>"><?php echo esc_html($wcbel_operator_label); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <?php if ($wcbel_field_type === 'from_to_date'): ?>
            <input <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-ft wcbe-datepicker wcbe-date-from" type="text" id="<?php echo esc_attr($wcbel_field_id); ?>-from" data-field="from" data-to-id="<?php echo esc_attr($wcbel_field_id); ?>-to" placeholder="<?php echo esc_attr($wcbel_item['placeholder_from']); ?>">
            <input <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-ft wcbe-datepicker" type="text" id="<?php echo esc_attr($wcbel_field_id); ?>-to" data-field="to" placeholder="<?php echo esc_attr($wcbel_item['placeholder_to']); ?>">
        <?php else: ?>
            <?php
            $wcbel_field_type_path = WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/' . $wcbel_field_type . '.php';
            if (file_exists($wcbel_field_type_path)) {
                include $wcbel_field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/text.php';
            }
            ?>
        <?php endif; ?>

        <?php if (isset($wcbel_item['disabled']) && $wcbel_item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>