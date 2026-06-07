<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<strong>By WooCommerce</strong>
<hr>
<div class="wcbe-form-group" data-name="minimum_allowed_quantity" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-minimum-quantity"><?php esc_html_e('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-minimum-quantity-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select>
    <input type="number" data-field="value" id="wcbe-bulk-edit-form-minimum-quantity" placeholder="<?php esc_attr_e('Minimum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <span class="wcbe-description-full-width"><?php esc_html_e('Enter a quantity to prevent the user buying this product if they have fewer than the allowed quantity in their cart', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="maximum_allowed_quantity" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-maximum-quantity"><?php esc_html_e('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-maximum-quantity-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select>
    <input type="number" data-field="value" id="wcbe-bulk-edit-form-maximum-quantity" placeholder="<?php esc_attr_e('Maximum quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <span class="wcbe-description-full-width"><?php esc_html_e('Enter a quantity to prevent the user buying this product if they have more than the allowed quantity in their cart', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="group_of_quantity" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-group-of-quantity"><?php esc_html_e('Group of quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-group-of-quantity-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select>
    <input type="number" data-field="value" id="wcbe-bulk-edit-form-group-of-quantity" placeholder="<?php esc_attr_e('Group of quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <span class="wcbe-description-full-width"><?php esc_html_e('Enter a quantity to only allow this product to be purchased in groups of X', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="minmax_do_not_count" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-do-not-count"><?php esc_html_e('Order rules: Do not count', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-do-not-count" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Don\'t count this product against your minimum order quantity/value rules.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="minmax_cart_exclude" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-cart-exclude"><?php esc_html_e('Order rules: Exclude', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-cart-exclude" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Exclude this product from minimum order quantity/value rules. If this is the only item in the cart, rules will not apply.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="minmax_category_group_of_exclude" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-category-exclude"><?php esc_html_e('Category rules: Exclude', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-category-exclude" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Exclude this product from category group-of-quantity rules. This product will not be counted towards category groups.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="allow_combination" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-allow-combination"><?php esc_html_e('Allow Combination', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-allow-combination" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Allow combination of variations to satisfy the min/max rules above.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="min_max_rules" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-min-max-rules"><?php esc_html_e('Min/Max Rules', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-min-max-rules" data-field="value" class="wcbe-input-md">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <span class="wcbe-description-full-width"><?php esc_html_e('Enable this option to override min/max settings at variation level', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>