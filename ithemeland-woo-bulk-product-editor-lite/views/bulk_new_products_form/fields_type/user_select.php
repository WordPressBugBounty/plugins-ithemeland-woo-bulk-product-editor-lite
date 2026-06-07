<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<select <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>
    class="wcbe-input-md wcbe-select2-users"
    id="<?php echo esc_attr($wcbel_field_id); ?>"
    data-field="value">
    <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
</select>