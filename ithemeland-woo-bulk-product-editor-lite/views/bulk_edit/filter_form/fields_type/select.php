<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<select id="<?php echo esc_attr($wcbel_field_id); ?>" class="wcbe-input-md" data-field="value" <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>>
    <?php if (isset($wcbel_item['first_option'])): ?>
        <option value=""><?php echo esc_html($wcbel_item['first_option']); ?></option>
    <?php endif; ?>
    <?php if (isset($wcbel_item['options']) && is_array($wcbel_item['options'])): ?>
        <?php foreach ($wcbel_item['options'] as $wcbel_key => $wcbel_value): ?>
            <option value="<?php echo esc_attr($wcbel_key); ?>"><?php echo esc_html($wcbel_value); ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>