<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-form-group" data-name="by">
    <strong>By Yith</strong>
    <hr>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_exclusion">
    <label for="wcbe-filter-form-exclude-product"><?php esc_html_e('Exclude product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-exclude-product" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_quantity_limit_override">
    <label for="wcbe-filter-form-override-product"><?php esc_html_e('Override product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-override-product" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_minimum_quantity">
    <label for="wcbe-filter-form-minimum-quantity-restriction-from"><?php esc_html_e('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-minimum-quantity-restriction-from" data-id-id="wcbe-filter-form-minimum-quantity-restriction-to" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-minimum-quantity-restriction-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_maximum_quantity">
    <label for="wcbe-filter-form-maximum-quantity-restriction-from"><?php esc_html_e('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-maximum-quantity-restriction-from" data-id-id="wcbe-filter-form-maximum-quantity-restriction-to" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-maximum-quantity-restriction-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_step_quantity">
    <label for="wcbe-filter-form-product-step-quantity-from"><?php esc_html_e('Group of quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-product-step-quantity-from" data-id-id="wcbe-filter-form-product-step-quantity-to" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-product-step-quantity-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="_ywmmq_product_quantity_limit_variations_override">
    <label for="wcbe-filter-form-enable-variation"><?php esc_html_e('Enable variation', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-enable-variation" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Using just for variable products', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>