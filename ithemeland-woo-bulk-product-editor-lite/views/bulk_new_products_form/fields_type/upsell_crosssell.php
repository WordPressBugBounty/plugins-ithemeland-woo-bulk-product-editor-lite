<?php
if (!defined('ABSPATH')) exit;

$wcbel_field_id = 'wcbe-bulk-edit-form-' . esc_attr($wcbel_item['name']);
$wcbel_attributes = '';
?>

<div>
    <select id="<?php echo esc_attr($wcbel_item['id']); ?>" data-field="value" multiple="" class="<?php echo esc_attr($wcbel_item['class']); ?>"></select>

</div>