<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-meta-fields">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Meta Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-alert wcbe-alert-default">
                            <span><?php esc_html_e('You can add new products meta fields in two ways: 1- Individually 2- Get from other product.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                        </div>
                        <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                            <?php include WCBEL_VIEWS_DIR . 'alerts/warning-active-pro.php' ?>
                        <?php endif; ?>
                        <div class="wcbe-meta-fields-left">
                            <div class="wcbe-meta-fields-manual">
                                <label for="wcbe-meta-fields-manual_key_name"><?php esc_html_e('Manually', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <div class="wcbe-meta-fields-manual-field">
                                    <input <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?> type="text" id="wcbe-meta-fields-manual_key_name" placeholder="<?php esc_attr_e('Enter Meta Key ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                    <button <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-button wcbe-button-square wcbe-button-blue" id="wcbe-add-meta-field-manual" disabled>
                                        <i class="wcbe-icon-plus1 wcbe-m0"></i>
                                    </button>
                                    <div class="wcbe-add-meta-field-message"></div>
                                </div>
                            </div>
                            <div class="wcbe-meta-fields-automatic">
                                <label for="wcbe-add-meta-fields-product-id"><?php esc_html_e('Automatically From product', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <div class="wcbe-meta-fields-automatic-field">
                                    <input <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?> type="text" id="wcbe-add-meta-fields-product-id" placeholder="<?php esc_attr_e('Enter Product ID ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                    <button <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-button wcbe-button-square wcbe-button-blue" id="wcbe-get-meta-fields-by-product-id">
                                        <i class="wcbe-icon-plus1 wcbe-m0"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if (class_exists('ACF')) : ?>
                                <div class="wcbe-meta-fields-acf">
                                    <label for="wcbe-add-meta-fields-acf"><?php esc_html_e('ACF PLugin Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                    <div class="wcbe-meta-fields-acf-field">
                                        <select <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?> id="wcbe-add-meta-fields-acf" class="wcbe-select2"></select>
                                        <button <?php echo !defined('WCBE_ACTIVE') || !WCBE_ACTIVE ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-button wcbe-button-square wcbe-button-blue" id="wcbe-add-acf-meta-field">
                                            <i class="wcbe-icon-plus1 wcbe-m0"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                            <?php wp_nonce_field('wcbe_post_nonce'); ?>
                            <input type="hidden" name="action" value="wcbe_meta_fields">
                            <div class="wcbe-meta-fields-right" id="wcbe-meta-fields-items">
                                <div id="wcbe-meta-fields-loading">
                                    <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" width="20" height="20" alt="Loading ...">
                                </div>

                                <p class="wcbe-meta-fields-empty-text" style="display: none;">
                                    <?php esc_html_e("Please add your meta key manually", 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    <br>
                                    <?php esc_html_e("OR", 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    <br>
                                    <?php esc_html_e("From another product", 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                </p>

                                <div class="wcbe-meta-fields-items"></div>

                                <div class="droppable-helper"></div>
                            </div>
                            <div class="wcbe-meta-fields-buttons">
                                <div class="wcbe-meta-fields-buttons-left">
                                    <button <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled="disabled"' : ''; ?> type="submit" value="1" name="save_meta_fields" class="wcbe-button wcbe-button-lg wcbe-button-blue">
                                        <?php $img = WCBEL_IMAGES_URL . 'save.svg'; ?>
                                        <img src="<?php echo esc_url($img); ?>" alt="">
                                        <span><?php esc_html_e('Save Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>