<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$wcbel_woo_multi_currency_params = get_option('woo_multi_currency_params', []);
if (!empty($wcbel_woo_multi_currency_params) && isset($wcbel_woo_multi_currency_params['enable_fixed_price']) && intval($wcbel_woo_multi_currency_params['enable_fixed_price']) === 1) :
    // delete default currency
    if (!empty($wcbel_woo_multi_currency_params['currency'][0])) {
        unset($wcbel_woo_multi_currency_params['currency'][0]);
    }

    // get active currencies
    if (!empty($wcbel_woo_multi_currency_params['currency'])) :
        if (!empty($wcbel_woo_multi_currency_params['currency']) && is_array($wcbel_woo_multi_currency_params['currency'])) :
?>
            <strong>By Villa Theme</strong>
            <hr>

            <?php foreach ($wcbel_woo_multi_currency_params['currency'] as $wcbel_currency) : ?>
                <div class="wcbe-form-group" data-name="_regular_price_wmcp" data-sub-name="<?php echo esc_attr($wcbel_currency); ?>" data-type="meta_field">
                    <label for="wcbe-bulk-edit-form-woo-multi-currency-regular-price-<?php echo esc_attr($wcbel_currency); ?>"><?php echo esc_html('Regular price (' . $wcbel_currency . ')'); ?></label>
                    <select data-field="operator" id="wcbe-bulk-edit-form-woo-multi-currency-regular-price-operator-<?php echo esc_attr($wcbel_currency); ?>">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
                    </select>
                    <input type="number" data-field="value" id="wcbe-bulk-edit-form-woo-multi-currency-regular-price-<?php echo esc_attr($wcbel_currency); ?>" placeholder="Regular price (<?php echo esc_attr($wcbel_currency); ?>)">
                </div>
                <div class="wcbe-form-group" data-name="_sale_price_wmcp" data-sub-name="<?php echo esc_attr($wcbel_currency); ?>" data-type="meta_field">
                    <label for="wcbe-bulk-edit-form-woo-multi-currency-sale-price-<?php echo esc_attr($wcbel_currency); ?>"><?php echo esc_html('Sale price (' . $wcbel_currency . ')'); ?></label>
                    <select data-field="operator" id="wcbe-bulk-edit-form-woo-multi-currency-sale-price-operator-<?php echo esc_attr($wcbel_currency); ?>">
                        <?php include WCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form/operators/number.php"; ?>
                    </select>
                    <input type="number" data-field="value" id="wcbe-bulk-edit-form-woo-multi-currency-sale-price-<?php echo esc_attr($wcbel_currency); ?>" placeholder="Sale price (<?php echo esc_attr($wcbel_currency); ?>)">
                </div>
<?php
            endforeach;
        endif;
    endif;
endif;
