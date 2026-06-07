<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-warning-pro-alert">
    <i class="wcbe-icon-warning"></i> <!-- Warning icon -->
    <span class="warning-message"><?php esc_html_e('This option is not available in Free Version, Please upgrade to Pro Version.', 'ithemeland-woo-bulk-product-editor-lite') ?></span>
    <a href="<?php echo esc_url(WCBEL_PRO_LINK); ?>"><?php esc_html_e('Download Pro Version', 'ithemeland-woo-bulk-product-editor-lite') ?></a>
</div>