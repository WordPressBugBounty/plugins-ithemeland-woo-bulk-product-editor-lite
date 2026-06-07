<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<textarea id="<?php echo esc_attr($wcbel_field_id); ?>" data-field="value" placeholder="<?php echo isset($wcbel_item['placeholder']) ? esc_attr($wcbel_item['placeholder']) : ''; ?>" <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?>></textarea>