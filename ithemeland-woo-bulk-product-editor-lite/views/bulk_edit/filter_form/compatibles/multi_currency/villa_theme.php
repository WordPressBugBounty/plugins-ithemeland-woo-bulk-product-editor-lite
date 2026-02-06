<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$woo_multi_currency_params = get_option('woo_multi_currency_params', []);
if (!empty($woo_multi_currency_params) && isset($woo_multi_currency_params['enable_fixed_price']) && intval($woo_multi_currency_params['enable_fixed_price']) === 1) :
    // delete default currency
    if (!empty($woo_multi_currency_params['currency'][0])) {
        unset($woo_multi_currency_params['currency'][0]);
    }

    // get active currencies
    if (!empty($woo_multi_currency_params['currency'])) :
        if (!empty($woo_multi_currency_params['currency']) && is_array($woo_multi_currency_params['currency'])) :
?>
            <strong>By Villa Theme</strong>
            <hr>

            <?php foreach ($woo_multi_currency_params['currency'] as $currency) : ?>
                <div class="wcbe-form-group" data-name="_regular_price_wmcp_-_<?php echo esc_attr($currency); ?>">
                    <label for="wcbe-filter-form-woo-multi-currency-regular-price-<?php echo esc_attr($currency); ?>-from"><?php echo esc_html('Regular price (' . $currency . ')'); ?></label>
                    <input type="number" data-field="from" data-field-type="regular" data-field-name="_regular_price_wmcp_-_<?php echo esc_attr($currency); ?>" class="wcbe-input-ft" id="wcbe-filter-form-woo-multi-currency-regular-price-<?php echo esc_attr($currency); ?>-from" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                    <input type="number" data-field="to" class="wcbe-input-ft" id="wcbe-filter-form-woo-multi-currency-regular-price-<?php echo esc_attr($currency); ?>-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                </div>
                <div class="wcbe-form-group" data-name="_sale_price_wmcp_-_<?php echo esc_attr($currency); ?>">
                    <label for="wcbe-filter-form-woo-multi-currency-sale-price-<?php echo esc_attr($currency); ?>-from"><?php echo esc_html('Sale price (' . $currency . ')'); ?></label>
                    <input type="number" data-field="from" data-field-type="sale" data-field-name="_regular_price_wmcp_-_<?php echo esc_attr($currency); ?>" class="wcbe-input-ft" id="wcbe-filter-form-woo-multi-currency-sale-price-<?php echo esc_attr($currency); ?>-from" placeholder="<?php esc_attr_e('From', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                    <input type="number" data-field="to" class="wcbe-input-ft" id="wcbe-filter-form-woo-multi-currency-sale-price-<?php echo esc_attr($currency); ?>-to" placeholder="<?php esc_attr_e('To', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                </div>
<?php
            endforeach;
        endif;
    endif;
endif;
