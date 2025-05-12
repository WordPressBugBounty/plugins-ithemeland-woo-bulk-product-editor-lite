<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wcbe-variation-bulk-edit-current-item">
    <label class="wcbe-variation-bulk-edit-current-item-name">
        <input type="checkbox" checked="checked" name="variation_item[]" data-id="<?php echo (!empty($variation_id)) ? esc_attr($variation_id) : ''; ?>" value="<?php echo (!empty($attribute_value)) ? esc_attr(urldecode($attribute_value)) : ''; ?>">
        <span><?php echo (!empty($variation_attributes)) ? wp_kses($variation_attributes, Sanitizer::allowed_html()) : ""; ?></span>
    </label>
    <button type="button" class="wcbe-button wcbe-button-flat wcbe-variation-bulk-edit-current-item-sortable-btn" title="Drag">
        <i class=" wcbe-icon-menu1"></i>
    </button>
    <div class="wcbe-variation-bulk-edit-current-item-radio">
        <input type="radio" name="default_variation" value="<?php echo (!empty($attribute_value)) ? esc_attr(urldecode($attribute_value)) : ''; ?>" title="<?php esc_html_e('Set as default', 'ithemeland-woo-bulk-product-editor-lite'); ?>" <?php echo (!empty($variation_attributes) && !empty($default_variation) && $default_variation == $variation_attributes) ? "checked='checked'" : ''; ?>>
    </div>
</div>