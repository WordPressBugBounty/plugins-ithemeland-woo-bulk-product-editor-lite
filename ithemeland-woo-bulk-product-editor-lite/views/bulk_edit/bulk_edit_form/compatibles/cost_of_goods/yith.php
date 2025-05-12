<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<strong>By Yith</strong>
<hr>
<div class="wcbe-form-group" data-name="yith_cog_cost" data-type="meta_field">
    <label for="wcbe-bulk-edit-form-yith-cost-of-goods"><?php esc_html_e('Cost of goods', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <select id="wcbe-bulk-edit-form-yith-cost-of-goods-operator" data-field="operator">
        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
    </select><input type="number" data-field="value" id="wcbe-bulk-edit-form-yith-cost-of-goods" placeholder="<?php esc_attr_e('Cost of goods', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <span class="wcbe-description-full-width"><?php esc_html_e('Note: In formula, the current value known as X. Ex: (X+10)*10% :: (The current value+10) * 10%', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
</div>