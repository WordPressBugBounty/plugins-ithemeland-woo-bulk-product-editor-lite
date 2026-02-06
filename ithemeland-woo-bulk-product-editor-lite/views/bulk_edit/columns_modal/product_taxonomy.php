<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wcbe-modal" id="wcbe-modal-product-taxonomy">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Taxonomy Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-top-search">
                    <input class="wcbe-search-in-list" title="Type for search" data-target=".wcbe-modal-product-taxonomy-terms-list label" type="text" placeholder="<?php esc_attr_e('Type for search', 'ithemeland-woo-bulk-product-editor-lite'); ?> ...">
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-modal-body-content">
                        <div style="width: 100%; float: left; text-align: center;">
                            <img src="<?php echo esc_url(WCBEL_IMAGES_URL . "loading-2.gif"); ?>" width="22" height="22" class="wcbe-modal-product-taxonomy-loading" alt="loading...">
                        </div>
                        <div class="wcbe-wrap">
                            <div class="wcbe-modal-product-taxonomy-terms-list">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-item-id="" data-update-type="taxonomy" data-name="" data-toggle="modal-close" class="wcbe-button wcbe-button-blue wcbe-inline-edit-taxonomy-save">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="wcbe-button wcbe-button-white wcbe-inline-edit-add-new-taxonomy" data-item-id="" data-item-name="" data-field="" data-toggle="modal" data-target="#wcbe-modal-new-product-taxonomy">
                        <?php esc_html_e('Add New', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>