<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wrap wcbel-wp-notice">
    <div class="wcbel-license-notice free" id="wcbel-pro-version-alert">
        <button class="wcbel-license-notice-close wcbel-pro-version-alert-dismiss-button" type="button" aria-label="Dismiss notice" onclick="document.getElementById('wcbel-pro-version-alert').style.display='none'"><span class="dashicons dashicons-no-alt"></span></button>
        <div>👑</div>
        <div>
            <strong><?php esc_html_e('Unlock the full power of PBULKiT Pro', 'ithemeland-woo-bulk-product-editor-lite') ?></strong>
            <p><?php esc_html_e('Schedule and undo bulk edits, restore edit history, manage custom fields and taxonomies, and process large product catalogs faster with Pro.', 'ithemeland-woo-bulk-product-editor-lite') ?></p>
        </div>
        <div class="wcbel-license-notice-actions">
            <a class="wcbel-license-btn wcbel-license-btn-outline wcbel-license-btn-sm" href="https://demo.ithemelandco.com/woocommerce-bulk-product-editing-pro/?utm_source=<?php echo esc_url(get_site_url()); ?>&utm_medium=web_links&utm_campaign=user-lite-buy"><?php esc_html_e('View Live Demo', 'ithemeland-woo-bulk-product-editor-lite') ?></a>
            <a class="wcbel-license-btn wcbel-license-btn-primary wcbel-license-btn-sm" href="https://ithemelandco.com/cart/?add-to-cart=18638/?utm_source=<?php echo esc_url(get_site_url()); ?>&utm_medium=web_links&utm_campaign=user-lite-buy"><?php esc_html_e('Upgrade to Pro', 'ithemeland-woo-bulk-product-editor-lite') ?></a>
        </div>
    </div>
</div>