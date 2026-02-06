<?php

use wcbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

global $wp_roles;
$user_roles = $wp_roles->roles;
$roles_option_html = '';

if (!empty($user_roles)) {
    foreach ($user_roles as $key => $role) {
        $roles_option_html .= '<option value="' . esc_attr($key) . '">' . esc_html($role['name']) . '</option>';
    }
}
?>

<strong>By iThemeland</strong>
<hr>

<div class="wcbe-form-group">
    <div class="wcbe-alert wcbe-alert-warning wcbe-mb0"><span><?php esc_html_e('These fields don\'t apply to \'Variations\'', 'ithemeland-woo-bulk-product-editor-lite'); ?></span></div>
</div>

<div class="wcbe-form-group" data-name="it_product_hide_price_unregistered" data-sub-name="" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-price-unregistered"><?php esc_html_e('Hide Price (unregistered)', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select class="wcbe-input-md" data-field="value" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-price-unregistered">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="it_pricing_product_price_user_role" data-sub-name="" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-price"><?php esc_html_e('Hide Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select data-field="operator" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-price-operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/taxonomy.php"; ?>
    </select>
    <select multiple="" class="wcbe-select2" data-field="value" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-price" data-placeholder="Select roles ...">
        <?php echo wp_kses($roles_option_html, Sanitizer::allowed_html()); ?>
    </select>
</div>
<div class="wcbe-form-group" data-name="it_pricing_product_add_to_cart_user_role" data-sub-name="" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-add-to-cart"><?php esc_html_e('Hide Add to cart', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select data-field="operator" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-add-to-cart-operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/taxonomy.php"; ?>
    </select>
    <select multiple="" class="wcbe-select2" data-field="value" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-add-to-cart" data-placeholder="Select roles ...">
        <?php echo wp_kses($roles_option_html, Sanitizer::allowed_html()); ?>
    </select>
</div>
<div class="wcbe-form-group" data-name="it_pricing_product_hide_user_role" data-sub-name="" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-product"><?php esc_html_e('Hide Product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select data-field="operator" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-product-operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/taxonomy.php"; ?>
    </select>
    <select multiple="" class="wcbe-select2" data-field="value" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-hide-product" data-placeholder="Select roles ...">
        <?php echo wp_kses($roles_option_html, Sanitizer::allowed_html()); ?>
    </select>
</div>
<div class="wcbe-form-group">
    <div class="wcbe-alert wcbe-alert-warning wcbe-mt20 wcbe-mb0">
        <span><?php esc_html_e('If you want to apply these fields to \'Variations\', you should select theme in table.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
    </div>
</div>
<div class="wcbe-form-group" data-name="it_product_disable_discount" data-sub-name="" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-it-wc-dynamic-pricing-disable-discount"><?php esc_html_e('Disable Discount', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select class="wcbe-input-md" data-field="value" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-disable-discount">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<?php
if (!empty($wp_roles->roles)) :
    foreach ($wp_roles->roles as $key => $role) :
?>
        <div class="wcbe-form-group" data-name="pricing_rules_product" data-sub-name="<?php echo esc_attr($key); ?>" data-type="meta_field">
            <label for="wcbe-bulk-edit-form-it-wc-dynamic-pricing-role-<?php echo esc_attr($key); ?>"><?php echo esc_html($role['name']); ?></label>
            <input type="number" class="wcbe-input-md" data-field="value" data-name="<?php echo esc_attr($key); ?>" id="wcbe-bulk-edit-form-it-wc-dynamic-pricing-role-<?php echo esc_attr($key); ?>" placeholder="<?php esc_attr_e('Amount ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        </div>
<?php
    endforeach;
endif;
?>