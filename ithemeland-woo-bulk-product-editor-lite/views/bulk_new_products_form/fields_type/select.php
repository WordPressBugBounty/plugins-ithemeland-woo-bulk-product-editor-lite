<select <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-md <?php echo isset($item['select2']) && $item['select2'] ? 'wcbe-select2-users' : ''; ?>" id="<?php echo esc_attr($field_id); ?>" data-field="value">

    <?php if (!empty($item['options'])): ?>
        <option value="">select</option>
        <?php foreach ($item['options'] as $key => $value): ?>
            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>