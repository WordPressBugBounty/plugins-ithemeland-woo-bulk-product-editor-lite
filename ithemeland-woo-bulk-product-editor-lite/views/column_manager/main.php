<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-column-manager">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Column Manager', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-alert wcbe-alert-default">
                            <span><?php esc_html_e('Mange columns of table. You can Create your customize presets and use them in column profile section.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                        </div>
                        <div class="wcbe-column-manager-items">
                            <h3><?php esc_html_e('Column Profiles', 'ithemeland-woo-bulk-product-editor-lite'); ?></h3>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wcbe-column-manager-delete-preset-form">
                                <?php wp_nonce_field('wcbe_post_nonce'); ?>
                                <input type="hidden" name="action" value="wcbe_column_manager_delete_preset">
                                <input type="hidden" name="delete_key" id="wcbe_column_manager_delete_preset_key">
                                <div class="wcbe-table-border-radius">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php esc_html_e('Profile Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                                <th><?php esc_html_e('Date Modified', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                                <th><?php esc_html_e('Actions', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center;" colspan="4"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="Loading ..." width="20" height="20"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                        <div class="wcbe-column-manager-new-profile">
                            <h3 class="wcbe-column-manager-section-title"><?php esc_html_e('Create New Profile', 'ithemeland-woo-bulk-product-editor-lite'); ?></h3>
                            <div class="wcbe-column-manager-new-profile-left">
                                <input type="text" title="<?php esc_attr_e('Search Field', 'ithemeland-woo-bulk-product-editor-lite'); ?>" data-action="new" placeholder="<?php esc_attr_e('Search Field ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>" class="wcbe-column-manager-search-field">
                                <div class="wcbe-column-manager-available-fields" data-action="new">
                                    <label class="wcbe-column-manager-check-all-fields-btn" data-action="new">
                                        <input type="checkbox" class="wcbe-column-manager-check-all-fields">
                                        <span><?php esc_html_e('Select All', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </label>
                                    <ul>
                                        <li style="text-align: center;"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="Loading ..." width="20" height="20"></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="wcbe-column-manager-new-profile-middle">
                                <div class="wcbe-column-manager-middle-buttons">
                                    <div>
                                        <button type="button" data-action="new" data-type="checked" class="wcbe-button wcbe-button-lg wcbe-button-square-lg wcbe-button-blue wcbe-column-manager-add-field">
                                            <i class="wcbe-icon-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wcbe-column-manager-add-new-preset">
                                <?php wp_nonce_field('wcbe_post_nonce'); ?>
                                <input type="hidden" name="action" value="wcbe_column_manager_new_preset">
                                <div class="wcbe-column-manager-new-profile-right">
                                    <div class="wcbe-column-manager-right-top">
                                        <input type="text" title="Profile Name" id="wcbe-column-manager-new-preset-name" name="preset_name" placeholder="Profile name ..." required>
                                        <button type="submit" name="save_preset" id="wcbe-column-manager-new-preset-btn" class="wcbe-button wcbe-button-lg wcbe-button-blue">
                                            <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'save.svg'); ?>" alt="">
                                            <?php esc_html_e('Save Preset', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </button>
                                    </div>
                                    <div class="wcbe-column-manager-added-fields-wrapper">
                                        <p class="wcbe-column-manager-empty-text"><?php esc_html_e('Please add your columns here', 'ithemeland-woo-bulk-product-editor-lite'); ?></p>
                                        <div class="wcbe-column-manager-added-fields" data-action="new">
                                            <div class="items"></div>
                                            <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading.gif'); ?>" alt="" class="wcbe-box-loading wcbe-hide">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>