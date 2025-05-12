<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-filter">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <input type="hidden" id="filter-form-changed" value="">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Filter Form', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-tabs">
                            <div class="wcbe-tabs-navigation">
                                <nav class="wcbe-tabs-navbar">
                                    <ul class="wcbe-tabs-list" data-content-id="wcbe-bulk-edit-filter-tabs-contents">
                                        <li><a class="wcbe-tab-item selected" data-content="filter_general" href="#"><?php esc_html_e('General', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="filter_taxonomies" href="#"><?php esc_html_e('Categories/Tags/Taxonomies', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="filter_pricing" href="#"><?php esc_html_e('Pricing', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="filter_shipping" href="#"><?php esc_html_e('Shipping', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="filter_stock" href="#"><?php esc_html_e('Stock', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="filter_type" href="#"><?php esc_html_e('Type', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <?php if (!empty($has_compatible_fields) && $has_compatible_fields === true) : ?>
                                            <li><a class="wcbe-tab-item" data-content="filter_compatibles" href="#"><?php esc_html_e('Compatibles', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <?php endif; ?>
                                        <li><a class="wcbe-tab-item" data-content="filter_custom_fields" href="#"><?php esc_html_e('Custom Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <?php do_action('wcbe_filter_form_after_tab_title'); ?>
                                    </ul>
                                </nav>
                            </div>
                            <div class="wcbe-tabs-contents wcbe-mt15" id="wcbe-bulk-edit-filter-tabs-contents">
                                <div class="wcbe-tab-content-item selected" data-content="filter_general">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_taxonomies">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_pricing">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_shipping">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_stock">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_type">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_compatibles">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="filter_custom_fields">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <?php do_action('wcbe_filter_form_after_tab_content'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-float-side-modal-footer">
                    <div class="wcbe-tab-footer-left">
                        <button type="button" id="wcbe-filter-form-get-products" class="wcbe-button wcbe-button-blue wcbe-filter-form-action" data-search-action="pro_search">
                            <?php esc_html_e('Get products', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                        </button>
                        <button type="button" class="wcbe-button wcbe-button-white" id="wcbe-filter-form-reset">
                            <?php esc_html_e('Reset Filters', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                        </button>
                    </div>
                    <div class="wcbe-tab-footer-right">
                        <input type="text" name="save_filter" id="wcbe-filter-form-save-preset-name" placeholder="Filter Name ..." class="" title="Filter Name">
                        <button type="button" id="wcbe-filter-form-save-preset" class="wcbe-button wcbe-button-blue">
                            <?php esc_html_e('Save Profile', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                        </button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>