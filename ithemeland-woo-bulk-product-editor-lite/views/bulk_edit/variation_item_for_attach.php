<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wcbe-variation-bulk-edit-current-item">
    <div>
        <span><?php echo (!empty($variation_attributes)) ? wp_kses($variation_attributes, Sanitizer::allowed_html()) : ""; ?></span>
        <input type="hidden" name="variation_id[]" value="<?php echo (!empty($variation_id)) ? esc_attr($variation_id) : ''; ?>">
        <?php if (!empty($attribute_items)) : ?>
            |
            <select title="Select item" name="attribute_item[]" class="wcbe-variations-attaching-variation-attribute-item">
                <?php foreach ($attribute_items as $item) : ?>
                    <option value="<?php echo esc_attr($item->term_id); ?>" <?php echo (!empty($attribute_item) && $attribute_item == urldecode($item->term_id)) ? 'selected' : ''; ?>><?php echo esc_html($item->name); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
</div>