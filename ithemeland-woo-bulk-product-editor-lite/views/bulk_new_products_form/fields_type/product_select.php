<?php
if (!defined('ABSPATH')) exit;

$field_id = 'wcbe-bulk-new-form-' . esc_attr($item['name']);
$multiple = isset($item['multiple']) && $item['multiple'] ? 'multiple' : '';
$select2_class = isset($item['select2']) && $item['select2'] ? 'wcbe-select2' : '';
$ajax = isset($item['ajax']) && $item['ajax'] ? 'data-ajax=""' : '';
?>
<select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>
    id="<?php echo esc_attr($field_id); ?>"
    data-field="value"
    class="wcbe-get-products-ajax <?php echo esc_attr($select2_class); ?>"
    <?php echo esc_attr($multiple); ?>
    <?php echo esc_attr($ajax); ?>>
</select>