<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-select-author">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Select Author', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <label class="wcbe-modal-select-author-label">
                            Author
                            <img class="wcbe-modal-select-author-loading" src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="Loading ..." width="16" height="16">
                        </label>
                        <select class="wcbe-select2-users" style="width: 100%;" id="wcbe-modal-select-author-input"></select>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-select-author-apply" data-item-id="" data-field="" data-content-type="author" data-update-type="wp_posts_field" class="wcbe-button wcbe-button-blue wcbe-modal-select-author-apply-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>