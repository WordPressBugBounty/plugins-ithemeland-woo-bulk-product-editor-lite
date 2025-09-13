<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-item-duplicate">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Duplicate', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-modal-body-content">
                        <div class="wcbe-wrap">
                            <div class="wcbe-form-group">
                                <label class="wcbe-label-big" for="wcbe-bulk-edit-duplicate-number">
                                    <?php esc_html_e('Enter how many item(s) to Duplicate!', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                </label>
                                <input type="number" class="wcbe-input-numeric-sm" id="wcbe-bulk-edit-duplicate-number" value="1" placeholder="<?php esc_html_e('Number ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" class="wcbe-button wcbe-button-blue" id="wcbe-bulk-edit-duplicate-start">
                        <?php esc_html_e('Start Duplicate', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>