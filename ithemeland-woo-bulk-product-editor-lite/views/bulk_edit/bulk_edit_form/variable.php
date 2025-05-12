<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<select class="wcbe-bulk-edit-form-variable" title="<?php esc_attr_e('Select Variable', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-field="variable">
    <option value=""><?php esc_html_e('Variable', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="title"><?php esc_html_e('Title', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="id"><?php esc_html_e('ID', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="sku"><?php esc_html_e('SKU', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="menu_order"><?php esc_html_e('Menu Order', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="parent_id"><?php esc_html_e('Parent ID', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="parent_title"><?php esc_html_e('Parent Title', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="parent_sku"><?php esc_html_e('Parent SKU', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="regular_price"><?php esc_html_e('Regular Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
    <option value="sale_price"><?php esc_html_e('Sale Price', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
</select>