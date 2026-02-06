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
                <div class="wcbe-form-group" data-name="_regular_price_wmcp" data-sub-name="<?php echo esc_attr($currency); ?>" data-type="meta_field">
                    <label for="wcbe-bulk-edit-form-woo-multi-currency-regular-price-<?php echo esc_attr($currency); ?>"><?php echo esc_html('Regular price (' . $currency . ')'); ?></label>
                    <select data-field="operator" id="wcbe-bulk-edit-form-woo-multi-currency-regular-price-operator-<?php echo esc_attr($currency); ?>">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
                    </select>
                    <input type="number" data-field="value" id="wcbe-bulk-edit-form-woo-multi-currency-regular-price-<?php echo esc_attr($currency); ?>" placeholder="Regular price (<?php echo esc_attr($currency); ?>)">
                </div>
                <div class="wcbe-form-group" data-name="_sale_price_wmcp" data-sub-name="<?php echo esc_attr($currency); ?>" data-type="meta_field">
                    <label for="wcbe-bulk-edit-form-woo-multi-currency-sale-price-<?php echo esc_attr($currency); ?>"><?php echo esc_html('Sale price (' . $currency . ')'); ?></label>
                    <select data-field="operator" id="wcbe-bulk-edit-form-woo-multi-currency-sale-price-operator-<?php echo esc_attr($currency); ?>">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
                    </select>
                    <input type="number" data-field="value" id="wcbe-bulk-edit-form-woo-multi-currency-sale-price-<?php echo esc_attr($currency); ?>" placeholder="Sale price (<?php echo esc_attr($currency); ?>)">
                </div>
<?php
            endforeach;
        endif;
    endif;
endif;
