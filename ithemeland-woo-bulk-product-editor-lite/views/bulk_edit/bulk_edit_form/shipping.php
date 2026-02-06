<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-form-group" data-name="shipping_class" data-type="woocommerce_field">
    <div>
        <label for="wcbe-bulk-edit-form-shipping-class"><?php esc_html_e('Shipping class', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <select id="wcbe-bulk-edit-form-shipping-class" data-field="value" class="wcbe-input-md">
            <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
            <?php
            if (!empty($shipping_classes)) :
                foreach ($shipping_classes as $key => $value) :
            ?>
                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
            <?php
                endforeach;
            endif;
            ?>
        </select>
    </div>
</div>
<div class="wcbe-form-group" data-name="width" data-type="woocommerce_field">
    <div>
        <label for="wcbe-bulk-edit-form-width"><?php esc_html_e('Width', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <select id="wcbe-bulk-edit-form-width-operator" data-field="operator">
            <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
        </select>
        <input type="number" id="wcbe-bulk-edit-form-width" data-field="value">
        <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    </div>
</div>
<div class="wcbe-form-group" data-name="height" data-type="woocommerce_field">
    <div>
        <label for="wcbe-bulk-edit-form-height"><?php esc_html_e('Height', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <select id="wcbe-bulk-edit-form-height-operator" data-field="operator">
            <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
        </select>
        <input type="number" id="wcbe-bulk-edit-form-height" data-field="value">
        <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    </div>
</div>
<div class="wcbe-form-group" data-name="length" data-type="woocommerce_field">
    <div>
        <label for="wcbe-bulk-edit-form-length"><?php esc_html_e('Length', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <select id="wcbe-bulk-edit-form-length-operator" data-field="operator">
            <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
        </select>
        <input type="number" id="wcbe-bulk-edit-form-length" data-field="value">
        <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    </div>
</div>
<div class="wcbe-form-group" data-name="weight" data-type="woocommerce_field">
    <div>
        <label for="wcbe-bulk-edit-form-weight"><?php esc_html_e('Weight', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <select id="wcbe-bulk-edit-form-weight-operator" data-field="operator">
            <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
        </select>
        <input type="number" id="wcbe-bulk-edit-form-weight" data-field="value">
        <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    </div>
</div>