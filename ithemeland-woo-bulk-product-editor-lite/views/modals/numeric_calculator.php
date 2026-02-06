<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-numeric-calculator">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Calculator', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-numeric-calculator-item-title" class="wcbe-modal-product-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <select id="wcbe-numeric-calculator-operator" title="<?php esc_html_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            <option value="+">+</option>
                            <option value="-">-</option>
                            <option value="replace"><?php esc_html_e('replace', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                        </select>
                        <input type="number" placeholder="<?php esc_html_e('Enter Value ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" id="wcbe-numeric-calculator-value" title="<?php esc_html_e('Value', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-item-id="" data-field="" data-field-type="" data-toggle="modal-close" class="wcbe-button wcbe-button-blue wcbe-edit-action-numeric-calculator">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>