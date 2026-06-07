<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\Product;

$wcbel_product_repository = Product::get_instance();
$wcbel_wc_vendors = $wcbel_product_repository->get_wc_product_vendors();
$wcbel_wc_vendors_options = '';
if (!empty($wcbel_wc_vendors)) {
    foreach ($wcbel_wc_vendors as $wcbel_wc_vendor) {
        if ($wcbel_wc_vendor instanceof \WP_Term) {
            $wcbel_wc_vendors_options .= '<option value="' . esc_attr($wcbel_wc_vendor->slug) . '">' . esc_html($wcbel_wc_vendor->name) . '</option>';
        }
    }
}
?>

<strong>By WooCommerce</strong>
<hr>
<div class="wcbe-form-group" data-name="_wcpv_product_commission" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-wc-product-commission"><?php esc_html_e('Product commission', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-wc-product-commission-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select>
    <input type="number" data-field="value" id="wcbe-bulk-edit-form-wc-product-commission" placeholder="<?php esc_attr_e('Product commission', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="_wcpv_product_taxes" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-wc-tax-handling"><?php esc_html_e('Tax handling', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-wc-tax-handling" data-field="value" class="wcbe-input-ft">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="keep-tax"><?php esc_html_e('Keep taxes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="pass-tax"><?php esc_html_e('Pass taxes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="split-tax"><?php esc_html_e('Split taxes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="_wcpv_product_pass_shipping" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-wc-pass-shipping"><?php esc_html_e('Pass shipping', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-wc-pass-shipping" data-field="value" class="wcbe-input-ft">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="wcpv_product_vendors" data-type="taxonomy">
    <label for="wcbe-bulk-edit-form-wc-vendor"><?php esc_html_e('Vendor', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select class="wcbe-select2" id="wcbe-bulk-edit-form-wc-vendor" multiple data-field="value">
        <?php echo wp_kses($wcbel_wc_vendors_options, Sanitizer::allowed_html()); ?>
    </select>
</div>