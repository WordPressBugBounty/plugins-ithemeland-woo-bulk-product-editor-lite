<?php
if (!defined('ABSPATH')) exit;

$wcbel_field_id = 'wcbe-bulk-new-form-' . esc_attr($wcbel_item['name']);
?>
<input <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>
    type="number"
    id="<?php echo esc_attr($wcbel_field_id); ?>"
    data-field="value"
    placeholder="<?php echo isset($wcbel_item['placeholder']) ? esc_attr($wcbel_item['placeholder']) : ''; ?>">

<?php if (isset($wcbel_item['round_items']) && is_array($wcbel_item['round_items'])): ?>
    <select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> data-field="round" id="<?php echo esc_attr($wcbel_field_id); ?>-round-item" title="<?php esc_attr_e('Select round item', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <?php foreach ($wcbel_item['round_items'] as $wcbel_value => $wcbel_label): ?>
            <option value="<?php echo esc_attr($wcbel_value); ?>"><?php echo esc_html($wcbel_label); ?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>