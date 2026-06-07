<?php
if (!defined('ABSPATH')) exit;

?>
<?php if (!empty($wcbel_item['operators'])): ?>
    <select id="<?php echo esc_attr($wcbel_field_id); ?>-operator" data-field="operator">
        <?php foreach ($wcbel_item['operators'] as $wcbel_operator_name => $wcbel_operator_label): ?>
            <option value="<?php echo esc_attr($wcbel_operator_name); ?>"><?php echo esc_html($wcbel_operator_label); ?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>

<div>
    <select id="<?php echo esc_attr($wcbel_item['id']); ?>" data-field="value" multiple="" class="<?php echo esc_attr($wcbel_item['class']); ?>"></select>

</div>