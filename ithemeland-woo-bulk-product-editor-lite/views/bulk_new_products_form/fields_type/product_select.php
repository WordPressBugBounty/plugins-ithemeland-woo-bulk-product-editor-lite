<?php
if (!defined('ABSPATH')) exit;

$wcbel_field_id = 'wcbe-bulk-new-form-' . esc_attr($wcbel_item['name']);
$wcbel_multiple = isset($wcbel_item['multiple']) && $wcbel_item['multiple'] ? 'multiple' : '';
$wcbel_select2_class = isset($wcbel_item['select2']) && $wcbel_item['select2'] ? 'wcbe-select2' : '';
$wcbel_ajax = isset($wcbel_item['ajax']) && $wcbel_item['ajax'] ? 'data-ajax=""' : '';
?>
<select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>
    id="<?php echo esc_attr($wcbel_field_id); ?>"
    data-field="value"
    class="wcbe-get-products-ajax <?php echo esc_attr($wcbel_select2_class); ?>"
    <?php echo esc_attr($wcbel_multiple); ?>
    <?php echo esc_attr($wcbel_ajax); ?>>
</select>