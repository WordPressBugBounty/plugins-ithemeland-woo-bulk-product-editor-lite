<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<textarea id="<?php echo esc_attr($field_id); ?>" data-field="value" placeholder="<?php echo isset($item['placeholder']) ? esc_attr($item['placeholder']) : ''; ?>" <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?>></textarea>