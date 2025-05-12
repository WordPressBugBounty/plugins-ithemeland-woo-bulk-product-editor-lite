<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-settings">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Settings', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" style="float: left; width: 100%; height: 100%;">
                    <?php wp_nonce_field('wcbe_post_nonce'); ?>
                    <div class="wcbe-float-side-modal-body">
                        <div class="wcbe-wrap">
                            <input type="hidden" name="action" value="wcbe_settings">
                            <div class="wcbe-alert wcbe-alert-default">
                                <span><?php esc_html_e('You can set bulk editor settings', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-count-per-page"><?php esc_html_e('Count Per Page', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[count_per_page]" id="wcbe-settings-count-per-page" title="The number of products per page">
                                    <?php
                                    if (!empty($count_per_page_items)) :
                                        foreach ($count_per_page_items as $count_per_page_item) :
                                    ?>
                                            <option value="<?php echo intval(esc_attr($count_per_page_item)); ?>" <?php if (isset($settings['count_per_page']) && $settings['count_per_page'] == intval($count_per_page_item)) : ?> selected <?php endif; ?>>
                                                <?php echo esc_html($count_per_page_item); ?>
                                            </option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-default-sort-by"><?php esc_html_e('Default Sort By', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select id="wcbe-settings-default-sort-by" class="wcbe-input-md" name="settings[default_sort_by]">
                                    <option value="ID" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'id') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('ID', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="title" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'title') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Title', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="regular_price" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'regular_price') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Regular price', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="sale_price" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'sale_price') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Sale price', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="sku" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'sku') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('SKU', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="manage_stock" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'manage_stock') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Manage Stock', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="stock_quantity" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'stock_quantity') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Stock Quantity', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="stock_status" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'stock_status') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Stock Status', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-default-sort"><?php esc_html_e('Default Sort', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[default_sort]" id="wcbe-settings-default-sort" class="wcbe-input-md">
                                    <option value="ASC" <?php echo (isset($settings['default_sort']) && $settings['default_sort'] == 'ASC') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('ASC', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="DESC" <?php echo (isset($settings['default_sort']) && $settings['default_sort'] == 'DESC') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('DESC', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-close-popup-after-applying"><?php esc_html_e('Close popup after applying', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[close_popup_after_applying]" id="wcbe-settings-close-popup-after-applying" class="wcbe-input-md">
                                    <option value="yes" <?php echo (isset($settings['close_popup_after_applying']) && $settings['close_popup_after_applying'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="no" <?php echo (isset($settings['close_popup_after_applying']) && $settings['close_popup_after_applying'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-sticky-first-columns"><?php esc_html_e("Sticky 'ID' & 'Title' Columns", 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[sticky_first_columns]" id="wcbe-settings-sticky-first-columns" class="wcbe-input-md">
                                    <option value="yes" <?php echo (isset($settings['sticky_first_columns']) && $settings['sticky_first_columns'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="no" <?php echo (isset($settings['sticky_first_columns']) && $settings['sticky_first_columns'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-display-full-columns-title"><?php esc_html_e('Display Columns Label', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[display_full_columns_title]" id="wcbe-settings-display-full-columns-title" class="wcbe-input-md">
                                    <option value="yes" <?php echo (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Completely', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="no" <?php echo (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('In short', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-display-cell-content"><?php esc_html_e('Display cell content', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[display_cell_content]" id="wcbe-settings-display-cell-content" class="wcbe-input-md">
                                    <option value="long" <?php echo (isset($settings['display_cell_content']) && $settings['display_cell_content'] == 'long') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Long text', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="short" <?php echo (isset($settings['display_cell_content']) && $settings['display_cell_content'] == 'short') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Short text', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                                <p class="wcbe-settings-description"><?php esc_html_e("If choose 'Short text' the cell content will be trimmed", 'ithemeland-woo-bulk-product-editor-lite'); ?></p>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-enable-thumbnail-popup"><?php esc_html_e('Enable Thumbnail Popup', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[enable_thumbnail_popup]" id="wcbe-settings-enable-thumbnail-popup" class="wcbe-input-md">
                                    <option value="yes" <?php echo (isset($settings['enable_thumbnail_popup']) && $settings['enable_thumbnail_popup'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="no" <?php echo (isset($settings['enable_thumbnail_popup']) && $settings['enable_thumbnail_popup'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-keep-filled-data-in-bulk-edit-form"><?php esc_html_e('Keep filled data in bulk edit form', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[keep_filled_data_in_bulk_edit_form]" id="wcbe-settings-keep-filled-data-in-bulk-edit-form" class="wcbe-input-md">
                                    <option value="no" <?php echo (isset($settings['keep_filled_data_in_bulk_edit_form']) && $settings['keep_filled_data_in_bulk_edit_form'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="yes" <?php echo (isset($settings['keep_filled_data_in_bulk_edit_form']) && $settings['keep_filled_data_in_bulk_edit_form'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-show-only-filtered-variations"><?php esc_html_e('Show Only Filtered Variations', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[show_only_filtered_variations]" id="wcbe-settings-show-only-filtered-variations" class="wcbe-input-md">
                                    <option value="no" <?php echo (isset($settings['show_only_filtered_variations']) && $settings['show_only_filtered_variations'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="yes" <?php echo (isset($settings['show_only_filtered_variations']) && $settings['show_only_filtered_variations'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-enable-load-more-variations"><?php esc_html_e('Enable "Load  More" for variations', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[enable_load_more_variations]" id="wcbe-settings-enable-load-more-variations" class="wcbe-input-md">
                                    <option value="yes" <?php echo (isset($settings['enable_load_more_variations']) && $settings['enable_load_more_variations'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="no" <?php echo (isset($settings['enable_load_more_variations']) && $settings['enable_load_more_variations'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                                <p class="wcbe-settings-description"><?php esc_html_e('Show all of variations on table at same time', 'ithemeland-woo-bulk-product-editor-lite'); ?></p>
                            </div>
                            <div class="wcbe-form-group">
                                <label for="wcbe-settings-enable-background-processing"><?php esc_html_e('Enable Background Processing', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                <select name="settings[enable_background_processing]" id="wcbe-settings-enable-background-processing" class="wcbe-input-md">
                                    <option value="yes" <?php echo (isset($settings['enable_background_processing']) && $settings['enable_background_processing'] == 'yes') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Yes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                    <option value="no" <?php echo (isset($settings['enable_background_processing']) && $settings['enable_background_processing'] == 'no') ? 'selected' : ''; ?>>
                                        <?php esc_html_e('No', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                    </option>
                                </select>
                                <p class="wcbe-settings-description">If you enable this option, all the heavy and time-consuming operations are executed as Background Processing until the end, and you are safe from the <strong>"Error 524: A Timeout Occurred"</strong> message.
                                    <br>
                                    Note) You will not be able to access other parts of the plugin while the operation is running.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="wcbe-float-side-modal-footer">
                        <button type="submit" class="wcbe-button wcbe-button-blue">
                            <img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'save.svg'); ?>" alt="">
                            <span><?php esc_html_e('Save Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>