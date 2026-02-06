<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-form-group" data-name="by">
    <strong>By WooCommerce</strong>
    <hr>
</div>
<div class="wcbe-form-group" data-name="minimum_allowed_quantity">
    <label for="wcbe-filter-form-minimum-quantity-from"><?php esc_html_e('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-minimum-quantity-from" data-id-id="wcbe-filter-form-minimum-quantity-to" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-minimum-quantity-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="maximum_allowed_quantity">
    <label for="wcbe-filter-form-maximum-quantity-from"><?php esc_html_e('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-maximum-quantity-from" data-id-id="wcbe-filter-form-maximum-quantity-to" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-maximum-quantity-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="group_of_quantity">
    <label for="wcbe-filter-form-group-of-quantity-from"><?php esc_html_e('Group of quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-group-of-quantity-from" data-id-id="wcbe-filter-form-group-of-quantity-to" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-group-of-quantity-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="minmax_do_not_count">
    <label for="wcbe-filter-form-do-not-count"><?php esc_html_e('Order rules: Do not count', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-do-not-count" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="minmax_cart_exclude">
    <label for="wcbe-filter-form-cart-exclude"><?php esc_html_e('Order rules: Exclude', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-cart-exclude" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="minmax_category_group_of_exclude">
    <label for="wcbe-filter-form-category-exclude"><?php esc_html_e('Category rules: Exclude', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-category-exclude" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>