<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-image">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Image Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-image-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-modal-body-content">
                        <div class="wcbe-wrap">
                            <div class="wcbe-inline-image-edit">
                                <button type="button" class="wcbe-inline-uploader wcbe-open-uploader" data-target="inline-edit" data-type="single" data-id="" data-item-id="">
                                    <i class="wcbe-icon-pencil"></i>
                                </button>
                                <div class="wcbe-inline-image-preview" data-image-preview-id=""></div>
                                <input type="hidden" id="" class="wcbe-image-preview-hidden-input">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-item-id="" data-field="" data-button-type="save" data-content-type="image" class="wcbe-button wcbe-button-blue wcbe-edit-action-with-button" data-toggle="modal-close" data-image-url="" data-image-id="">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="wcbe-button wcbe-button-red wcbe-edit-action-with-button" data-button-type="remove" data-item-id="" data-image-url="<?php echo esc_url(WCBEL_IMAGES_URL . "no-image.png"); ?>" data-field="" data-image-id="0" data-toggle="modal-close">
                        <?php esc_html_e('Remove Image', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>