<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> type="text" class="<?php echo isset($wcbel_item['class']) ? esc_attr($wcbel_item['class']) : ''; ?>" id="<?php echo esc_attr($wcbel_field_id); ?>" data-field="value" placeholder="<?php echo esc_html($wcbel_item['label']); ?>">