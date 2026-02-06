<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-gallery">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Gallery Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-gallery-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-inline-gallery-edit">
                            <div class="wcbe-inline-image-preview">
                                <div class="wcbe-inline-edit-gallery-item">
                                    <button type="button" class="wcbe-open-uploader wcbe-inline-edit-gallery-add-image" data-item-id="" data-target="inline-edit-gallery" data-type="multiple">
                                        <i class="wcbe-icon-plus1"></i>
                                    </button>
                                </div>
                                <div id="wcbe-modal-gallery-items"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-gallery-apply" data-item-id="" data-content-type="gallery" class="wcbe-button wcbe-button-blue wcbe-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>