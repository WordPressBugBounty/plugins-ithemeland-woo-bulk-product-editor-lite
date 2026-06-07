<?php
if (!defined('ABSPATH')) exit;

$wcbel_select_classes = 'wcbe-select2-taxonomies wcbe-select2-item';
if (isset($wcbel_item['select2']) && $wcbel_item['select2']) {
    $wcbel_select_classes .= ' wcbe-select2';
}
?>
<select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($wcbel_field_id); ?>"
    data-output="term_id"
    data-field="value"
    <?php echo isset($wcbel_item['multiple']) && $wcbel_item['multiple'] ? 'multiple="multiple"' : ''; ?>
    class="<?php echo esc_attr($wcbel_select_classes); ?>">
</select>