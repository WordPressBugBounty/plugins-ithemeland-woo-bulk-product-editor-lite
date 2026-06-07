<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input class="wcbe-input-ft wcbe-datepicker wcbe-date-from" type="text" id="<?php echo esc_attr($wcbel_field_id); ?>-from" data-field="from" data-to-id="<?php echo esc_attr($wcbel_field_id); ?>-to" placeholder="<?php echo isset($wcbel_item['placeholder_from']) ? esc_attr($wcbel_item['placeholder_from']) : esc_html__('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>" <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>>
<input class="wcbe-input-ft wcbe-datepicker" type="text" id="<?php echo esc_attr($wcbel_field_id); ?>-to" data-field="to" placeholder="<?php echo isset($wcbel_item['placeholder_to']) ? esc_attr($wcbel_item['placeholder_to']) : esc_html__('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>" <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>>