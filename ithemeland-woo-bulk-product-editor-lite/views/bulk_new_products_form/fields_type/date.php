<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> class="wcbe-input-md wcbe-datepicker" type="text" id="<?php echo esc_attr($field_id); ?>" data-field="value" placeholder="<?php echo esc_html($item['label']); ?>">