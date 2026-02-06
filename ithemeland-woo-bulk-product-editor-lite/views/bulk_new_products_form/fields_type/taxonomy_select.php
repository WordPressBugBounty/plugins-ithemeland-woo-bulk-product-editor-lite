<?php
if (!defined('ABSPATH')) exit;

$select_classes = 'wcbe-select2-taxonomies wcbe-select2-item';
if (isset($item['select2']) && $item['select2']) {
    $select_classes .= ' wcbe-select2';
}
?>
<select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($field_id); ?>"
    data-output="<?php echo isset($item['output_type']) ? esc_attr($item['output_type']) : 'term_id'; ?>"
    data-field="value"
    <?php echo isset($item['multiple']) && $item['multiple'] ? 'multiple="multiple"' : ''; ?>
    class="<?php echo esc_attr($select_classes); ?>">
</select>