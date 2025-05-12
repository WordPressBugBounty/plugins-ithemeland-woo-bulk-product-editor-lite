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

<strong>By Yith</strong>
<hr>
<div class="wcbe-form-group" data-name="_product_commission" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-yith-product-commission"><?php esc_html_e('Product commission', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-yith-product-commission-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select>
    <input type="number" data-field="value" id="wcbe-bulk-edit-form-yith-product-commission" placeholder="Product commission ...">
    <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>
<div class="wcbe-form-group" data-name="yith_shop_vendor" data-type="taxonomy">
    <label for="wcbe-bulk-edit-form-yith-vendor"><?php esc_html_e('Vendor', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-yith-vendor" class="wcbe-select2" data-field="value" class="wcbe-select2" multiple="">
        <?php echo wp_kses($yith_vendors_options, Sanitizer::allowed_html()); ?>
    </select>
</div>