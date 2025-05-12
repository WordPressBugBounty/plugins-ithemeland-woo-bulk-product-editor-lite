<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-form-group" data-name="by">
    <strong>By Yith</strong>
    <hr>
</div>
<div class="wcbe-form-group" data-name="yith_cog_cost">
    <label for="wcbe-filter-form-yith-cost-of-goods-from"><?php esc_html_e('Cost of goods', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
    <input type="number" data-field="from" class="wcbe-input-ft" id="wcbe-filter-form-yith-cost-of-goods-from" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
    <input type="number" data-field="to" class="wcbe-input-ft" id="wcbe-filter-form-yith-cost-of-goods-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
</div>