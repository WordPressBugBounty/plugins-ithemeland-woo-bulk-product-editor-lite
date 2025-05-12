<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-column-profiles">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Column Profiles', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" style="float: left; width: 100%; height: 100%;">
                    <?php wp_nonce_field('wcbe_post_nonce'); ?>
                    <input type="hidden" name="action" value="wcbe_load_column_profile">
                    <div class="wcbe-float-side-modal-body">
                        <div class="wcbe-wrap">
                            <div class="wcbe-alert wcbe-alert-default">
                                <span><?php esc_html_e('You can load saved column profile presets through Column Manager. You can change the columns and save your changes too.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                            </div>
                            <div class="wcbe-column-profiles-choose">
                                <label for="wcbe-column-profiles-choose"><?php esc_html_e('Choose Preset', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select id="wcbe-column-profiles-choose" name="preset_key">

                                </select>
                                <label class="wcbe-column-profile-select-all">
                                    <input type="checkbox" id="wcbe-column-profile-select-all" data-profile-name="<?php echo (!empty($active_columns_key)) ? esc_attr($active_columns_key) : ''; ?>">
                                    <span><?php esc_html_e('Select All', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                </label>
                            </div>
                            <div class="wcbe-column-profile-search">
                                <label for="wcbe-column-profile-search"><?php esc_html_e('Search', 'ithemeland-woo-bulk-product-editor-lite'); ?> </label>
                                <input type="text" id="wcbe-column-profile-search" placeholder="<?php esc_attr_e('Search Column ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                            </div>
                            <div class="wcbe-column-profiles-fields">
                                <div style="width:100%; text-align: center;">
                                    <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="Loading ..." width="22" height="22">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wcbe-float-side-modal-footer">
                        <button type="submit" class="wcbe-button wcbe-button-blue wcbe-float-left" id="wcbe-column-profiles-apply" data-preset-key="default">
                            <?php esc_html_e('Apply To Table', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                        </button>
                        <div class="wcbe-column-profile-save-dropdown" style="display: none">
                            <span>
                                <?php esc_html_e('Save Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                <i class="wcbe-icon-chevron-down"></i>
                            </span>
                            <div class="wcbe-column-profile-save-dropdown-buttons">
                                <ul>
                                    <li id="wcbe-column-profiles-update-changes" <?php echo (!empty($active_columns_key) && !empty($default_columns_name) && in_array($active_columns_key, $default_columns_name)) ? 'style="display:none;"' : ''; ?>>
                                        <?php esc_html_e('Update selected preset', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </li>
                                    <li id="wcbe-column-profiles-save-as-new-preset">
                                        <?php esc_html_e('Save as new preset', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>