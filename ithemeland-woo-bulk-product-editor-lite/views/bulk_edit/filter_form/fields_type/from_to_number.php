<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input class="wcbe-input-ft" type="number" data-field="from" id="<?php echo esc_attr($item['id']); ?>-from" placeholder="<?php echo esc_attr($item['placeholder_from']); ?>">
<input class="wcbe-input-ft" type="number" data-field="to" id="<?php echo esc_attr($item['id']); ?>-to" placeholder="<?php echo esc_attr($item['placeholder_to']); ?>">