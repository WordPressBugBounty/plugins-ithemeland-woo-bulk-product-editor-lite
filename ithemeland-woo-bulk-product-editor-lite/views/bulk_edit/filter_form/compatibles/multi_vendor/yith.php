<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\repositories\Product;

$product_repository = Product::get_instance();
$yith_vendors = $product_repository->get_yith_vendors();
$yith_vendors_options = '';
if (!empty($yith_vendors)) {
    foreach ($yith_vendors as $yith_vendor) {
        if ($yith_vendor instanceof \WP_Term) {
            $yith_vendors_options .= '<option value="' . esc_attr($yith_vendor->slug) . '">' . esc_html($yith_vendor->name) . '</option>';
        }
    }
}
?>


<div class="wcbe-form-group" data-name="by">
    <strong>By Yith</strong>
    <hr>
</div>
<div class="wcbe-form-group" data-name="_product_commission">
    <label for="wcbe-filter-form-yith-product-commission-from"><?php esc_html_e('Product commission', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input type="number" data-field="from" class="wcbe-input-ft" id="wcbe-filter-form-yith-product-commission-from" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input type="number" data-field="to" class="wcbe-input-ft" id="wcbe-filter-form-yith-product-commission-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>
<div class="wcbe-form-group" data-name="yith_shop_vendor">
    <label for="wcbe-filter-form-yith-vendor"><?php esc_html_e('Product vendor', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-filter-form-yith-vendor-operator" data-field="operator">
        <option value="or"><?php esc_html_e('OR', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        <option value="not_in"><?php esc_html_e('NOT IN', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    </select>
    <select class="wcbe-select2" id="wcbe-filter-form-yith-vendor" data-field="value" multiple="" data-placeholder="<?php esc_attr_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
        <?php echo wp_kses($yith_vendors_options, Sanitizer::allowed_html()); ?>
    </select>
</div>