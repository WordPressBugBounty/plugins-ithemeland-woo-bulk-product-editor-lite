<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-bulk-new-products">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Bulk New Products Form', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-form-group wcbe-quantity" data-name="quantity" data-type="woocommerce_field">
                            <div>
                                <label for="wcbe-bulk-new-form-product-quantity"><?php esc_html_e('Product Quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <input type="number" id="wcbe-bulk-new-form-product-quantity" data-field="value" placeholder="<?php esc_attr_e('Product Quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?>" value="1">
                                <span class="wcbe-description-full-width"><?php esc_html_e('If you want to create a raw product, leave the following fields empty. Otherwise, new product(s) will be created with the entered values.
                        ', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                            </div>
                        </div>
                        <div class="wcbe-tabs">
                            <div class="wcbe-tabs-navigation">
                                <nav class="wcbe-tabs-navbar">
                                    <ul class="wcbe-tabs-list" data-content-id="wcbe-bulk-new-tabs">
                                        <li><a class="selected wcbe-tab-item" data-content="general" href="#"><?php esc_html_e('General', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="taxonomies" href="#"><?php esc_html_e('Categories/Tags/Taxonomies', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="pricing" href="#"><?php esc_html_e('Pricing', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="shipping" href="#"><?php esc_html_e('Shipping', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="stock" href="#"><?php esc_html_e('Stock', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <li><a class="wcbe-tab-item" data-content="type" href="#"><?php esc_html_e('Type', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <?php if (!empty($has_compatible_fields) && $has_compatible_fields === true) : ?>
                                            <li><a class="wcbe-tab-item" data-content="compatibles" href="#"><?php esc_html_e('Compatibles', 'ithemeland-woo-bulk-product-editor-lite'); ?></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                            <div class="wcbe-tabs-contents wcbe-mt15" id="wcbe-bulk-new-tabs">
                                <div class="selected wcbe-tab-content-item" data-content="general">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="taxonomies">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="pricing">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="shipping">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="stock">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="type">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                                <div class="wcbe-tab-content-item" data-content="compatibles">
                                    <div style="width: 100%; text-align: center; padding-top: 10px;">
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="<?php esc_attr_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" width="25" height="25">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-float-side-modal-footer">
                    <button type="button" id="wcbe-bulk-new-form-do-bulk-new" class="wcbe-button wcbe-button-blue wcbe-bulk-new-form-do-bulk-new" data-action="do">
                        <?php esc_html_e('Create Bulk New Products', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <?php do_action('wcbe_bulk_new_form_after_bulk_new_button'); ?>
                    <button type="button" class="wcbe-button wcbe-button-white" id="wcbe-bulk-new-form-reset">
                        <?php esc_html_e('Reset Form', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>