<?php
if (!defined('ABSPATH')) exit;

$field_id = 'wcbe-bulk-edit-form-' . esc_attr($item['name']);
$attributes = '';
?>

<div>
    <select id="<?php echo esc_attr($item['id']); ?>" data-field="value" multiple="" class="<?php echo esc_attr($item['class']); ?>"></select>

</div>