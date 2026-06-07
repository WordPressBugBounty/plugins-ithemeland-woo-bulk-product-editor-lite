<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> class="<?php echo esc_attr($wcbel_item['class']); ?>" type="text" id="<?php echo esc_attr($wcbel_field_id); ?>" data-field="value" data-to-id="<?php echo esc_attr($wcbel_item['data_to_id']); ?>" placeholder="<?php echo esc_html($wcbel_item['label']); ?>">