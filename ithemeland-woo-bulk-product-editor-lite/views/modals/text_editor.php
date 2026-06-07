<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-text-editor">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-lg">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Content Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-text-editor-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <?php wp_editor("", 'wcbe-text-editor'); ?>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-field="" data-item-id="" data-content-type="textarea" id="wcbe-text-editor-apply" class="wcbe-button wcbe-button-blue wcbe-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>