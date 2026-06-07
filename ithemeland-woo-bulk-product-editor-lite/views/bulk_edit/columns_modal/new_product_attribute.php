<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-new-product-attribute">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('New Product Attribute', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-new-product-attribute-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-form-group">
                            <div class="wcbe-new-product-attribute-form-group">
                                <label for="wcbe-new-product-attribute-name"><?php esc_html_e('Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <input type="text" id="wcbe-new-product-attribute-name" placeholder="<?php esc_html_e('Attribute Name ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            </div>
                            <div class="wcbe-new-product-attribute-form-group">
                                <label for="wcbe-new-product-attribute-slug"><?php esc_html_e('Slug', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <input type="text" id="wcbe-new-product-attribute-slug" placeholder="<?php esc_html_e('Attribute Slug ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            </div>
                            <div class="wcbe-new-product-attribute-form-group">
                                <label for="wcbe-new-product-attribute-description"><?php esc_html_e('Description', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <textarea id="wcbe-new-product-attribute-description" rows="8" placeholder="<?php esc_html_e('Description ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" class="wcbe-button wcbe-button-blue" id="wcbe-create-new-product-attribute" data-field="">
                        <?php esc_html_e('Create', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>