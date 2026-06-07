<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-md <?php echo isset($wcbel_item['select2']) && $wcbel_item['select2'] ? 'wcbe-select2-users' : ''; ?>" id="<?php echo esc_attr($wcbel_field_id); ?>" placeholder="<?php echo isset($wcbel_item['placeholder']) ? esc_attr($wcbel_item['placeholder']) : ''; ?>" data-field="value">
    <?php if (!empty($wcbel_item['options'])): ?>
        <?php foreach ($wcbel_item['options'] as $wcbel_key => $wcbel_value): ?>
            <option value="<?php echo esc_attr($wcbel_key); ?>"><?php echo esc_html($wcbel_value); ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>