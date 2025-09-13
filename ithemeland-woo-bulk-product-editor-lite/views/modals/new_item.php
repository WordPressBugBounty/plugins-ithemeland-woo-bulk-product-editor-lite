<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

use wcbel\classes\helpers\Sanitizer;

?>

<div class="wcbe-modal" id="wcbe-modal-new-item">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2 id="wcbe-new-item-title"></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-form-group">
                            <label class="wcbe-label-big" for="wcbe-new-item-count" id="wcbe-new-item-description"></label>
                            <input type="number" class="wcbe-input-numeric-sm wcbe-m0" id="wcbe-new-item-count" value="1" placeholder="<?php esc_html_e('Number ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                        </div>
                        <div id="wcbe-new-item-extra-fields">
                            <?php if (!empty($new_item_extra_fields)) : ?>
                                <?php foreach ($new_item_extra_fields as $extra_field) : ?>
                                    <div class="wcbe-form-group">
                                        <?php echo wp_kses($extra_field['label'], Sanitizer::allowed_html()); ?>
                                        <?php echo wp_kses($extra_field['field'], Sanitizer::allowed_html()); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" class="wcbe-button wcbe-button-blue" id="wcbe-create-new-item"><?php esc_html_e('Create', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>