<?php
if (!defined('ABSPATH')) exit;

$field_id = 'wcbe-bulk-new-form-' . esc_attr($item['name']);
?>
<input <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>
    type="number"
    id="<?php echo esc_attr($field_id); ?>"
    data-field="value"
    placeholder="<?php echo isset($item['placeholder']) ? esc_attr($item['placeholder']) : ''; ?>">

<?php if (isset($item['round_items']) && is_array($item['round_items'])): ?>
    <select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> data-field="round" id="<?php echo esc_attr($field_id); ?>-round-item" title="<?php esc_attr_e('Select round item', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <?php foreach ($item['round_items'] as $value => $label): ?>
            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>