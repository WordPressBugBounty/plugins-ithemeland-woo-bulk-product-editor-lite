<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<strong>By Yith</strong>
<hr>
<div class="wcbe-form-group" data-name="_ywmmq_product_exclusion" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-exclude-product"><?php esc_html_e('Exclude product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-exclude-product" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_quantity_limit_override" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-override-product"><?php esc_html_e('Override product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-override-product" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_minimum_quantity" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-minimum-quantity-restriction"><?php esc_html_e('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-minimum-quantity-restriction-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select>
    <input type="number" data-field="value" id="wcbe-bulk-edit-form-minimum-quantity-restriction" placeholder="Minimum quantity ...">
    <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_maximum_quantity" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-maximum-quantity-restriction"><?php esc_html_e('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-maximum-quantity-restriction-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select><input type="number" data-field="value" id="wcbe-bulk-edit-form-maximum-quantity-restriction" placeholder="Maximum quantity ...">
    <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_step_quantity" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-product-step-quantity"><?php esc_html_e('Group of quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-product-step-quantity-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select><input type="number" data-field="value" id="wcbe-bulk-edit-form-product-step-quantity" placeholder="Group of quantity ...">
    <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_quantity_limit_variations_override" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-enable-variation"><?php esc_html_e('Enable variation', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-enable-variation" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Using just for variable products', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>