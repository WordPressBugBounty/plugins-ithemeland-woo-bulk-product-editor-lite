<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<textarea <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($wcbel_field_id); ?>" type="text" data-field="value" placeholder="<?php echo esc_attr($wcbel_item['label']); ?>" class="wcbe-textarea"><?php echo isset($wcbel_item['value']) ? esc_textarea($wcbel_item['value']) : ''; ?></textarea>