<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<textarea <?php echo (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled"' : ''; ?> id="<?php echo esc_attr($field_id); ?>" type="text" data-field="value"
    placeholder="<?php echo esc_attr($item['label']); ?>"
    class="wcbe-textarea"><?php
                            echo isset($item['value']) ? esc_textarea($item['value']) : '';
                            ?></textarea>