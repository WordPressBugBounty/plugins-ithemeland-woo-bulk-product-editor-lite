<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input <?php echo (isset($wcbel_item['disabled']) && $wcbel_item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-md wcbe-datepicker" type="text" id="<?php echo esc_attr($wcbel_field_id); ?>" data-field="value" placeholder="<?php echo esc_html($wcbel_item['label']); ?>">