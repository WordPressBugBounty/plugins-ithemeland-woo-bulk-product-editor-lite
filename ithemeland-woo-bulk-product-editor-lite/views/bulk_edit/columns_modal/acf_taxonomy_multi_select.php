<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-acf-multi-select">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-inline-multi-select">
                            <select class="wcbe-select2 wcbe-w100p wcbe-modal-acf-taxonomy-multi-select-value" style="max-width: 100% !important;" data-placeholder="<?php esc_attr_e('Select ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" multiple></select>
                        </div>
                        <div class="wcbe-modal-acf-taxonomy-loading">
                            <img src="<?php echo esc_url(WCBEL_IMAGES_URL . "loading-2.gif"); ?>" width="20" height="20">
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-item-id="" data-name="" data-update-type="meta_field" data-content-type="multi_select" class="wcbe-button wcbe-button-blue wcbe-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>