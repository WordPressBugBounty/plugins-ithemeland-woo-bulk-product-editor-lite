<?php
if (!defined('ABSPATH')) exit;
?>
<select class="wcbe-select2-taxonomies wcbe-select2-item wcbe-filter-form-select2-option-values"
    data-output="<?php echo isset($wcbel_item['output_type']) ? esc_attr($wcbel_item['output_type']) : 'term_id'; ?>"
    data-option-name="<?php echo esc_attr($wcbel_name); ?>"
    data-field="value"
    id="<?php echo esc_attr($wcbel_field_id); ?>"
    multiple
    <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>>
</select>