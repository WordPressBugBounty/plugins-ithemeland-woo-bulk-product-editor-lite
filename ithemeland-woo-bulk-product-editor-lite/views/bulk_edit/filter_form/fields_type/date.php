<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="<?php echo esc_attr($item['class']); ?>" type="text" id="<?php echo esc_attr($field_id); ?>" data-field="value" data-to-id="<?php echo esc_attr($item['data-to-id']); ?>" placeholder="<?php echo esc_html($item['label']); ?>">