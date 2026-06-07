<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-product-attribute">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Attribute Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-top-search">
                    <div class="wcbe-wrap">
                        <input class="wcbe-search-in-list" title="<?php esc_attr_e('Type for search', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-id="#wcbe-modal-product-attribute" type="text" placeholder="<?php esc_attr_e('Type for search ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                    </div>
                </div>
                <div class="wcbe-modal-body wcbe-pt0">
                    <div class="wcbe-modal-body-content">
                        <div class="wcbe-wrap">
                            <div class="wcbe-product-attribute-checkboxes">
                                <label>
                                    <input type="hidden" class="is-visible-prev" value="">
                                    <input type="checkbox" class="is-visible" value="">
                                    <?php esc_html_e('Visible on the product page', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                </label>
                                <label>
                                    <input type="hidden" class="is-variation-prev" value="">
                                    <input type="checkbox" class="is-variation" value="">
                                    <?php esc_html_e('Used for variations', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                </label>
                            </div>
                            <div style="width: 100%; float: left; text-align: center;">
                                <img src="<?php echo esc_url(WCBEL_IMAGES_URL . "loading-2.gif"); ?>" width="22" height="22" class="wcbe-modal-product-attribute-loading" alt="loading...">
                            </div>
                            <div class="wcbe-modal-product-attribute-terms-list">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-item-id="" data-name="" data-update-type="taxonomy" data-field="" data-toggle="modal-close" class="wcbe-button wcbe-button-blue wcbe-inline-edit-attribute-save">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="wcbe-button wcbe-button-white wcbe-inline-edit-add-new-attribute" data-item-id="" data-field="" data-item-name="" data-toggle="modal" data-target="#wcbe-modal-new-product-attribute">
                        <?php esc_html_e('Add New', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>