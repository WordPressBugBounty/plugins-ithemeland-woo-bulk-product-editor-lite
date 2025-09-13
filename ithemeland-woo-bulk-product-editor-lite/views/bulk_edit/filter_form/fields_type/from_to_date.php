<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input class="wcbe-input-ft wcbe-datepicker wcbe-date-from" type="text" id="<?php echo esc_attr($field_id); ?>-from" data-field="from" data-to-id="<?php echo esc_attr($field_id); ?>-to" placeholder="<?php echo isset($item['placeholder_from']) ? esc_attr($item['placeholder_from']) : esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>
<input class="wcbe-input-ft wcbe-datepicker" type="text" id="<?php echo esc_attr($field_id); ?>-to" data-field="to" placeholder="<?php echo isset($item['placeholder_to']) ? esc_attr($item['placeholder_to']) : esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>>