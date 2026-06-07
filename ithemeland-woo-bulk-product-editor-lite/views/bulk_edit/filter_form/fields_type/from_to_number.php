<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input class="wcbe-input-ft" type="number" data-field="from" id="<?php echo esc_attr($wcbel_item['id']); ?>-from" placeholder="<?php echo esc_attr($wcbel_item['placeholder_from']); ?>">
<input class="wcbe-input-ft" type="number" data-field="to" id="<?php echo esc_attr($wcbel_item['id']); ?>-to" placeholder="<?php echo esc_attr($wcbel_item['placeholder_to']); ?>">