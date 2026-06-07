<?php

namespace wcbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Lang_Helper
{
    public static function get_js_strings()
    {
        return [
            'selectProduct' => esc_html__('Please Select Product !', 'ithemeland-woo-bulk-product-editor-lite'),
            'areYouSure' => esc_html__('Are you sure?', 'ithemeland-woo-bulk-product-editor-lite'),
            'iAmSure' => esc_html__("Yes, I'm sure !", 'ithemeland-woo-bulk-product-editor-lite'),
            'areYouSureForEditAllFilteredProducts' => esc_html__("Your changes will be applied to all of filtered products. Are you sure?", 'ithemeland-woo-bulk-product-editor-lite'),
            'duplicateVariationsDisabled' => esc_html__("Duplicate for variations product is disabled!", 'ithemeland-woo-bulk-product-editor-lite'),
            'setAsDefault' => esc_html__("Set as default", 'ithemeland-woo-bulk-product-editor-lite'),
            'selectedProductsIsNotVariable' => esc_html__("Some of selected products are not 'Variable' product. Do you want to change the product type?", 'ithemeland-woo-bulk-product-editor-lite'),
            'drag' => esc_html__("Drag", 'ithemeland-woo-bulk-product-editor-lite'),
            'variationRequired' => esc_html__("variation is required !", 'ithemeland-woo-bulk-product-editor-lite'),
            'productRequired' => esc_html__("Select product is required !", 'ithemeland-woo-bulk-product-editor-lite'),
            'variableProductRequired' => esc_html__("Select variable product is required !", 'ithemeland-woo-bulk-product-editor-lite'),
            'taxonomyNameRequired' => esc_html__("Taxonomy Name is required !", 'ithemeland-woo-bulk-product-editor-lite'),
            'attributeNameRequired' => esc_html__("Attribute Name is required !", 'ithemeland-woo-bulk-product-editor-lite'),
            'sameCase' => esc_html__("Same Case", 'ithemeland-woo-bulk-product-editor-lite'),
            'ignoreCase' => esc_html__("Ignore Case", 'ithemeland-woo-bulk-product-editor-lite'),
            'enterText' => esc_html__("Text ...", 'ithemeland-woo-bulk-product-editor-lite'),
            'selectVariable' => esc_html__("Select Variable", 'ithemeland-woo-bulk-product-editor-lite'),
            'variable' => esc_html__("Variable", 'ithemeland-woo-bulk-product-editor-lite'),
            'title' => esc_html__("Title", 'ithemeland-woo-bulk-product-editor-lite'),
            'id' => esc_html__("ID", 'ithemeland-woo-bulk-product-editor-lite'),
            'sku' => esc_html__("SKU", 'ithemeland-woo-bulk-product-editor-lite'),
            'parentId' => esc_html__("Parent ID", 'ithemeland-woo-bulk-product-editor-lite'),
            'parentTitle' => esc_html__("Parent Title", 'ithemeland-woo-bulk-product-editor-lite'),
            'parentSku' => esc_html__("Parent SKU", 'ithemeland-woo-bulk-product-editor-lite'),
            'regularPrice' => esc_html__("Regular Price", 'ithemeland-woo-bulk-product-editor-lite'),
            'salePrice' => esc_html__("Sale Price", 'ithemeland-woo-bulk-product-editor-lite'),
            'plzAddColumns' => esc_html__("Please Add Columns !", 'ithemeland-woo-bulk-product-editor-lite'),
            'presetNameRequired' => esc_html__("Preset name is required !", 'ithemeland-woo-bulk-product-editor-lite'),
            'newProduct' => esc_html__("New Product", 'ithemeland-woo-bulk-product-editor-lite'),
            'newProductNumber' => esc_html__("Enter how many new product(s) to create!", 'ithemeland-woo-bulk-product-editor-lite'),
            'enterProductName' => esc_html__("Product Name ...", 'ithemeland-woo-bulk-product-editor-lite'),
            'loading' => esc_html__("Loading", 'ithemeland-woo-bulk-product-editor-lite'),
            'success' => esc_html__("Success !", 'ithemeland-woo-bulk-product-editor-lite'),
            'productsFound' => esc_html__("Products Found", 'ithemeland-woo-bulk-product-editor-lite'),
            'productHasNoVariations' => esc_html__("The product has no variations !", 'ithemeland-woo-bulk-product-editor-lite'),
            'selectAttribute' => esc_html__("Select attribute", 'ithemeland-woo-bulk-product-editor-lite'),
            'notFound' => esc_html__("Not Found!", 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }
}
