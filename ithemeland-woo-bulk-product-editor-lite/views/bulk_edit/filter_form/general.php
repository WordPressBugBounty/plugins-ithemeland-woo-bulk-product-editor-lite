<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $filter_form_items['general'];

foreach ($items as $name => $item):
    $field_id = 'wcbe-filter-form-product-' . $item['id'];
    $field_type = isset($item['field_type']) ? $item['field_type'] : 'text';
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-filter-type="<?php echo esc_attr($item['filter_type']); ?>" data-field-type="<?php echo esc_attr($field_type); ?>">
        <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($item['label']); ?></label>

        <?php if (!empty($item['operators'])): ?>
            <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($field_id); ?>-operator" data-field="operator" title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                <?php foreach ($item['operators'] as $operator_name => $operator_label): ?>
                    <option value="<?php echo esc_attr($operator_name); ?>"><?php echo esc_html($operator_label); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <?php if ($field_type === 'from_to_date'): ?>
            <input <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-ft wcbe-datepicker wcbe-date-from" type="text" id="<?php echo esc_attr($field_id); ?>-from" data-field="from" data-to-id="<?php echo esc_attr($field_id); ?>-to" placeholder="<?php echo esc_attr($item['placeholder_from']); ?>">
            <input <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-ft wcbe-datepicker" type="text" id="<?php echo esc_attr($field_id); ?>-to" data-field="to" placeholder="<?php echo esc_attr($item['placeholder_to']); ?>">
        <?php else: ?>
            <?php
            $field_type_path = WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/' . $field_type . '.php';
            if (file_exists($field_type_path)) {
                include $field_type_path;
            } else {
                include WCBEL_VIEWS_DIR . 'bulk_edit/filter_form/fields_type/text.php';
            }
            ?>
        <?php endif; ?>

        <?php if (isset($item['disabled']) && $item['disabled']): ?>
            <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>