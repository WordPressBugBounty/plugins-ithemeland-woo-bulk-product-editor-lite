<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-file">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-lg">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Select File', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-select-file-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-inline-select-files">
                            <div class="wcbe-modal-select-files-file-item">
                                <input type="text" class="wcbe-inline-edit-file-url wcbe-w60p" id="wcbe-file-url" placeholder="File Url ..." value="">
                                <button type="button" class="wcbe-button wcbe-button-white wcbe-open-uploader wcbe-inline-edit-choose-file" data-type="single" data-target="inline-file-custom-field"><?php esc_html_e('Choose File', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                                <input type="hidden" id="wcbe-file-id" value="">
                                <button type="button" class="wcbe-button wcbe-button-white" id="wcbe-modal-file-clear"><?php esc_html_e('Clear', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-file-apply" data-item-id="" data-field="" data-content-type="file" class="wcbe-button wcbe-button-blue wcbe-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>