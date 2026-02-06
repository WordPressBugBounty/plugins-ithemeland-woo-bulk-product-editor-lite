<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-custom-field-files">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-lg">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Select Files', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-custom-field-files-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <button type="button" id="wcbe-modal-custom-field-files-add-file-item" class="wcbe-button wcbe-button-green wcbe-mb10"><?php esc_html_e('Add File', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . "loading-2.gif"); ?>" class="wcbe-files-loading" id="wcbe-modal-custom-field-files-loading">
                        <div class="wcbe-inline-custom-field-files"></div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-custom-field-files-apply" data-item-id="" data-field="" data-content-type="custom_field_files" class="wcbe-button wcbe-button-blue wcbe-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>