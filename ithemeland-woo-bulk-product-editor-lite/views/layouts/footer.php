<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<input type="hidden" id="wcbe-last-modal-opened" value="">
<?php

if (defined("YITH_WCBM_INIT")) {
    // "yith woocommerce badge management premium" plugin is active 
    // include product badges modal
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/product_badges.php";
}

if (class_exists("iThemeland_Woocommerce_Advanced_Product_Labels_Pro")) {
    // "woocommerce advanced product labels" plugin is active 
    // include ithemeland badge modal
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/ithemeland_badge.php";
}

if (class_exists("YIKES_Custom_Product_Tabs")) {
    // "Custom Product Tabs for WooCommerce" plugin is active 
    // include yikes custom product tabs modal
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/yikes_custom_product_tabs.php";
}

if (class_exists("it_WC_Dynamic_Pricing")) {
    // "iThemeland WooCommerce Dynamic Prices By User Role Plugin" plugin is active 
    // include modals
    global $wp_roles;
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/all_it_wc_dynamic_pricing_fields.php";
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/it_wc_dynamic_pricing.php";
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/it_wc_dynamic_pricing_select_roles_select_roles.php";
}

if (function_exists('acf_get_field_groups')) {
    include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/acf_taxonomy_multi_select.php";
}

include_once WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/bulk_edit_form.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/filter_form/filter_form.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/select_products.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/gallery.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/product_taxonomy.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/product_attribute.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/new_product_taxonomy.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/new_product_attribute.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/select_author.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/select_files.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/custom_field_files.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/regular_price_calculator.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/columns_modal/sale_price_calculator.php";
include_once WCBEL_VIEWS_DIR . "bulk_edit/variations.php";
include_once WCBEL_VIEWS_DIR . "column_manager/main.php";
include_once WCBEL_VIEWS_DIR . "column_manager/edit_preset.php";
include_once WCBEL_VIEWS_DIR . "import_export/main.php";
include_once WCBEL_VIEWS_DIR . "meta_field/main.php";
include_once WCBEL_VIEWS_DIR . "settings/main.php";
include_once WCBEL_VIEWS_DIR . "history/main.php";
include_once WCBEL_VIEWS_DIR . "column_profile/column_profiles.php";

include_once WCBEL_VIEWS_DIR . "modals/text_editor.php";
include_once WCBEL_VIEWS_DIR . "modals/image.php";
include_once WCBEL_VIEWS_DIR . "modals/file.php";
include_once WCBEL_VIEWS_DIR . "modals/numeric_calculator.php";
include_once WCBEL_VIEWS_DIR . "modals/duplicate_item.php";
include_once WCBEL_VIEWS_DIR . "modals/new_item.php";
include_once WCBEL_VIEWS_DIR . "modals/filter_profiles.php";
include_once WCBEL_VIEWS_DIR . "bulk_new_products_form/new_products.php";

do_action('wcbe_layout_footer');

// if (!defined('WCBE_IS_BUNDLE')) {
//     include_once WCBE_FW_DIR . "license/information.php";
// }
