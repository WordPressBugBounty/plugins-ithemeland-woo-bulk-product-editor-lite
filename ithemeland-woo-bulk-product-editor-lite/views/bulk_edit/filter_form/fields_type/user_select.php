<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<select class="wcbe-input-md wcbe-select2-users wcbe-filter-form-select2-option-values"
    data-option-name="<?php echo esc_attr($item['name']); ?>"
    id="wcbe-filter-form-<?php echo esc_attr($item['name']); ?>"
    data-field="value">
</select>