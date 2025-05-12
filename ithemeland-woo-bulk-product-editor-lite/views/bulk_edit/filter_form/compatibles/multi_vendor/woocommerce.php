<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\Product;

$product_repository = Product::get_instance();
$wc_vendors = $product_repository->get_wc_product_vendors();
$wc_vendors_options = '';
if (!empty($wc_vendors)) {
    foreach ($wc_vendors as $wc_vendor) {
        if ($wc_vendor instanceof \WP_Term) {
            $wc_vendors_options .= '<option value="' . esc_attr($wc_vendor->slug) . '">' . esc_html($wc_vendor->name) . '</option>';
        }
    }
}
?>

<div class="wcbe-form-group" data-name="by">
    <strong>By WooCommerce</strong>
    <hr>
</div>
<div class="wcbe-form-group" data-name="_wcpv_product_commission">
    <label for="wcbe-filter-form-wc-product-commission-from"><?php esc_html_e('Product commission', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input class="wcbe-input-ft" type="number" data-field="from" id="wcbe-filter-form-wc-product-commission-from" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input class="wcbe-input-ft" type="number" data-field="to" id="wcbe-filter-form-wc-product-commission-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="_wcpv_product_taxes">
    <label for="wcbe-filter-form-wc-product-taxes"><?php esc_html_e('Tax handling', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-wc-product-taxes" data-field="value" class="wcbe-input-ft">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="keep-tax"><?php esc_html_e('Keep taxes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="pass-tax"><?php esc_html_e('Pass taxes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="split-tax"><?php esc_html_e('Split taxes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="_wcpv_product_pass_shipping">
    <label for="wcbe-filter-form-wc-pass-shipping"><?php esc_html_e('Pass shipping', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-wc-pass-shipping" data-field="value" class="wcbe-input-ft">
        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="yes"><?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="no"><?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
</div>
<div class="wcbe-form-group" data-name="wcpv_product_vendors">
    <label for="wcbe-filter-form-wc-vendor"><?php esc_html_e('Vendor', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-wc-vendor-operator" data-field="operator">
        <option value="or"><?php esc_html_e('OR', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="not_in"><?php esc_html_e('NOT IN', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <select class="wcbe-select2" id="wcbe-filter-form-wc-vendor" data-field="value" multiple="" data-placeholder="<?php esc_attr_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <?php echo wp_kses($wc_vendors_options, Sanitizer::allowed_html()); ?>
    </select>
</div>