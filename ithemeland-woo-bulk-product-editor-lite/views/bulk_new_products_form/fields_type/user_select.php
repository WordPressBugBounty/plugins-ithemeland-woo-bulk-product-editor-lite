<select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>
    class="wcbe-input-md wcbe-select2-users"
    id="<?php echo esc_attr($field_id); ?>"
    data-field="value">
    <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
</select>