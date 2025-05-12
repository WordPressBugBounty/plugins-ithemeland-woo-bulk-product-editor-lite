<div id="wcbe-loading" class="wcbe-loading">
    <?php esc_html_e('Loading ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>
</div>

<?php

include WCBEL_VIEWS_DIR . 'background_process/loading.php';

if (!empty($flush_message)) {
    include WCBEL_VIEWS_DIR . "alerts/flush_message.php";
}
?>

<div id="wcbe-main">
    <div id="wcbe-header">
        <div class="wcbe-plugin-title">
            <span class="wcbe-plugin-name">
                <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'wcbe_icon_original.svg'); ?>" alt="">
                <?php echo (defined('WCBE_LABEL')) ? esc_html(WCBE_LABEL) : esc_html(WCBEL_LABEL); ?>
            </span>
        </div>
        <ul class="wcbe-header-left">
            <li title="Help">
                <a href="<?php echo (!empty($doc_link)) ? esc_url($doc_link) : '#'; ?>">
                    <i class="wcbe-icon-book"></i>
                </a>
            </li>
            <li id="wcbe-full-screen" title="Full screen">
                <i class="wcbe-icon-enlarge"></i>
            </li>
            <li id="wcbe-add-ons-button" data-toggle="modal" data-target="#wcbe-modal-add-ons" title="Add-Ons">
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M192 104.8c0-9.2-5.8-17.3-13.2-22.8C167.2 73.3 160 61.3 160 48c0-26.5 28.7-48 64-48s64 21.5 64 48c0 13.3-7.2 25.3-18.8 34c-7.4 5.5-13.2 13.6-13.2 22.8c0 12.8 10.4 23.2 23.2 23.2H336c26.5 0 48 21.5 48 48v56.8c0 12.8 10.4 23.2 23.2 23.2c9.2 0 17.3-5.8 22.8-13.2c8.7-11.6 20.7-18.8 34-18.8c26.5 0 48 28.7 48 64s-21.5 64-48 64c-13.3 0-25.3-7.2-34-18.8c-5.5-7.4-13.6-13.2-22.8-13.2c-12.8 0-23.2 10.4-23.2 23.2V464c0 26.5-21.5 48-48 48H279.2c-12.8 0-23.2-10.4-23.2-23.2c0-9.2 5.8-17.3 13.2-22.8c11.6-8.7 18.8-20.7 18.8-34c0-26.5-28.7-48-64-48s-64 21.5-64 48c0 13.3 7.2 25.3 18.8 34c7.4 5.5 13.2 13.6 13.2 22.8c0 12.8-10.4 23.2-23.2 23.2H48c-26.5 0-48-21.5-48-48V343.2C0 330.4 10.4 320 23.2 320c9.2 0 17.3 5.8 22.8 13.2C54.7 344.8 66.7 352 80 352c26.5 0 48-28.7 48-64s-21.5-64-48-64c-13.3 0-25.3 7.2-34 18.8C40.5 250.2 32.4 256 23.2 256C10.4 256 0 245.6 0 232.8V176c0-26.5 21.5-48 48-48H168.8c12.8 0 23.2-10.4 23.2-23.2z" />
                    </svg>
                </a>
            </li>
            <?php if (!defined('WCBE_ACTIVE')):  ?>
                <a href="<?php echo esc_url(WCBEL_PRO_LINK); ?>" class="header-pro-version">
                    <?php echo esc_html__('Pro version', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                </a>
            <?php endif; ?>
        </ul>
    </div>