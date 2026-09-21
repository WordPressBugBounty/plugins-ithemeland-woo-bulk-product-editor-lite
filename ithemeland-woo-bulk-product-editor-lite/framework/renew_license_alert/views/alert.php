<?php
if (! defined('ABSPATH')) exit;
?>

<div class="wrap wcbel-wp-notice">
    <div class="wcbel-license-notice-lite" id="wcbel-renew-license-alert" data-name="renew_license_alert">
        <button class="wcbel-license-notice-lite-close" type="button" aria-label="Dismiss notice"
            onclick="document.getElementById('wcbel-renew-license-alert').style.display='none'"><span class="dashicons dashicons-no-alt"></span></button>
        <div>⚠️</div>
        <div>
            <strong><?php esc_html_e('PBULKiT Pro update required to continue receiving updates', 'ithemeland-woo-bulk-product-editor-lite') ?></strong>
            <p><?php esc_html_e('Your current version of PBULKiT Pro is no longer receiving security, compatibility, and maintenance updates. Continuing to use an outdated Pro version may cause compatibility issues with future versions of WordPress and WooCommerce. Renew your license now to restore access to the latest PBULKiT Pro updates, security fixes, improvements, and support.', 'ithemeland-woo-bulk-product-editor-lite') ?></p>
        </div>
        <div class="wcbel-license-notice-lite-actions">
            <a class="wcbel-renew-license-alert-btn-lite wcbel-renew-license-alert-btn-lite-green wcbel-renew-license-alert-btn-lite-sm" href="https://ithemelandco.com/cart/?add-to-cart=18638/?utm_source=<?php echo esc_url(get_site_url()); ?>&utm_medium=web_links&utm_campaign=renewals-user-buy"><?php esc_html_e('Renew PBULKiT Pro', 'ithemeland-woo-bulk-product-editor-lite') ?></a>
        </div>
    </div>
</div>