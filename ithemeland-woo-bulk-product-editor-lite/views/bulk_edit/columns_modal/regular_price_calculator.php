<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-regular-price">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Calculator', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <select id="wcbe-regular-price-calculator-operator" title="<?php esc_attr_e('Select Operator', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            <option value="+">+</option>
                            <option value="-">-</option>
                            <option value="sp+">sp+</option>
                        </select>
                        <input type="number" placeholder="<?php esc_attr_e('Enter Value ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" id="wcbe-regular-price-calculator-value" title="<?php esc_attr_e('Value', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                        <select id="wcbe-regular-price-calculator-type" title="<?php esc_attr_e('Select Type', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            <option value="n">n</option>
                            <option value="%">%</option>
                        </select>
                        <select id="wcbe-regular-price-calculator-round" title="<?php esc_attr_e('Rounding', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            <option value=""><?php esc_html_e('no rounding', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="9">9</option>
                            <option value="19">19</option>
                            <option value="29">29</option>
                            <option value="39">39</option>
                            <option value="49">49</option>
                            <option value="59">59</option>
                            <option value="69">69</option>
                            <option value="79">79</option>
                            <option value="89">89</option>
                            <option value="99">99</option>
                        </select>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" data-item-id="" data-field="regular_price" data-update-type="woocommerce_field" data-toggle="modal-close" class="wcbe-button wcbe-button-blue wcbe-modal-regular-price-apply-button">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>