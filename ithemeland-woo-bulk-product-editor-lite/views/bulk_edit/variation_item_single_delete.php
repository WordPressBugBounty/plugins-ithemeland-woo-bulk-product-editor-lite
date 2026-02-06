<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wcbe-variation-bulk-edit-current-item">
    <label class="wcbe-variation-bulk-edit-current-item-name">
        <input type="checkbox" name="variation_item[]" value="<?php echo (!empty($variation_id)) ? esc_attr($variation_id) : ''; ?>">
        <span><?php echo (!empty($variation_attributes)) ? wp_kses($variation_attributes, Sanitizer::allowed_html()) : ""; ?></span>
    </label>
</div>