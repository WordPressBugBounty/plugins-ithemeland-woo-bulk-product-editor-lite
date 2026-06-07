<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <?php wp_nonce_field('wcbe_post_nonce'); ?>
    <input type="hidden" name="action" value="wcbe_column_manager_edit_preset">
    <input type="hidden" name="preset_key" id="wcbe-column-manager-edit-preset-key" value="">
    <div class="wcbe-modal" id="wcbe-modal-column-manager-edit-preset">
        <div class="wcbe-modal-container">
            <div class="wcbe-modal-box wcbe-modal-box-lg">
                <div class="wcbe-modal-content">
                    <div class="wcbe-modal-title">
                        <h2><?php esc_html_e('Edit Column Preset', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                        <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                            <i class="wcbe-icon-x"></i>
                        </button>
                    </div>
                    <div class="wcbe-modal-body">
                        <div class="wcbe-wrap">
                            <div class="wcbe-column-manager-new-profile wcbe-mt0">
                                <div class="wcbe-column-manager-new-profile-left">
                                    <label class="wcbe-column-manager-check-all-fields-btn" data-action="edit">
                                        <input type="checkbox" class="wcbe-column-manager-check-all-fields">
                                        <span><?php esc_html_e('Select All', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </label>
                                    <input type="text" title="Search Field" data-action="edit" placeholder="<?php esc_attr_e('Search Field ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" class="wcbe-column-manager-search-field">
                                    <div class="wcbe-column-manager-available-fields" data-action="edit">
                                        <ul>
                                            <li style="text-align: center;"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="Loading ..." width="20" height="20"></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="wcbe-column-manager-new-profile-middle">
                                    <div class="wcbe-column-manager-middle-buttons">
                                        <div>
                                            <button type="button" data-action="edit" class="wcbe-button wcbe-button-lg wcbe-button-square-lg wcbe-button-blue wcbe-column-manager-add-field">
                                                <i class="wcbe-icon-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="wcbe-column-manager-new-profile-right">
                                    <div class="wcbe-column-manager-right-top">
                                        <input type="text" title="Profile Name" class="wcbe-w100p" id="wcbe-column-manager-edit-preset-name" name="preset_name" placeholder="<?php esc_attr_e('Profile name ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                    </div>
                                    <div class="wcbe-column-manager-added-fields wcbe-table-border-radius wcbe-mt10" data-action="edit">
                                        <div class="items"></div>
                                        <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading.gif'); ?>" alt="" class="wcbe-box-loading wcbe-hide">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wcbe-modal-footer">
                        <button type="submit" name="edit_preset" class="wcbe-button wcbe-button-blue"><?php esc_html_e('Save Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>