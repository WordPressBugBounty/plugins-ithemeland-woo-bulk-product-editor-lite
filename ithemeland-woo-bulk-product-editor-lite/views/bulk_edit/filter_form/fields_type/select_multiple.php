<?php
if (!defined('ABSPATH')) exit;
?>
<select class="wcbe-select2-taxonomies wcbe-select2-item wcbe-filter-form-select2-option-values"
    data-output="<?php echo isset($item['output_type']) ? esc_attr($item['output_type']) : 'term_id'; ?>"
    data-option-name="<?php echo esc_attr($name); ?>"
    data-field="value"
    id="<?php echo esc_attr($field_id); ?>"
    multiple
    <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>
</select>