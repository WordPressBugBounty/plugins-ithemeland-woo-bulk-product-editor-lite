<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<select id="<?php echo esc_attr($field_id); ?>" class="wcbe-input-md" data-field="value" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>
    <?php if (isset($item['first_option'])): ?>
        <option value=""><?php echo esc_html($item['first_option']); ?></option>
    <?php endif; ?>
    <?php if (isset($item['options']) && is_array($item['options'])): ?>
        <?php foreach ($item['options'] as $key => $value): ?>
            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>