<?php
if (!defined('ABSPATH')) exit;

?>
<?php if (!empty($item['operators'])): ?>
    <select id="<?php echo esc_attr($field_id); ?>-operator" data-field="operator">
        <?php foreach ($item['operators'] as $operator_name => $operator_label): ?>
            <option value="<?php echo esc_attr($operator_name); ?>"><?php echo esc_html($operator_label); ?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>

<div>
    <select id="<?php echo esc_attr($item['id']); ?>" data-field="value" multiple="" class="<?php echo esc_attr($item['class']); ?>"></select>

</div>