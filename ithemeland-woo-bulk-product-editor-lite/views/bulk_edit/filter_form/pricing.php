<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$items = $filter_form_items['pricing'];

foreach ($items as $name => $item):
    $field_id = 'wcbe-filter-form-product-' . $item['id'];
    $field_type = isset($item['field_type']) ? $item['field_type'] : 'text';
?>
    <div class="wcbe-form-group" data-name="<?php echo esc_attr($name); ?>" data-filter-type="<?php echo esc_attr($item['filter_type']); ?>" data-field-type="<?php echo esc_attr($field_type); ?>">
        <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($item['label']); ?></label>

        <?php if ($field_type === 'from_to_number'): ?>
            <input class="wcbe-input-ft" type="number" data-field="from" id="<?php echo esc_attr($field_id); ?>-from" placeholder="<?php echo esc_attr($item['placeholder_from']); ?>" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>
            <input class="wcbe-input-ft" type="number" data-field="to" id="<?php echo esc_attr($field_id); ?>-to" placeholder="<?php echo esc_attr($item['placeholder_to']); ?>" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>
        <?php elseif ($field_type === 'date'): ?>
            <input type="text" class="wcbe-input-md wcbe-datepicker <?php echo isset($item['date_range']) && $item['date_range'] ? 'wcbe-date-from' : ''; ?>"
                data-field="value" id="<?php echo esc_attr($field_id); ?>"
                <?php if (isset($item['date_range']) && $item['date_range']): ?>
                data-to-id="<?php echo esc_attr($field_id); ?>-to"
                <?php endif; ?>
                placeholder="<?php echo esc_attr($item['placeholder']); ?>" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>
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