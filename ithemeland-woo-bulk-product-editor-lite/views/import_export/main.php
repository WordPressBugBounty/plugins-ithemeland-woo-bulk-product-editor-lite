<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-import-export">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Import/Export', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-alert wcbe-alert-default">
                            <span><?php esc_html_e('Import/Export products as CSV files', 'ithemeland-woo-bulk-product-editor-lite'); ?>.</span>
                        </div>
                        <div class="wcbe-export">
                            <form action="<?php echo esc_url(admin_url("admin-post.php")); ?>" method="post">
                                <?php wp_nonce_field('wcbe_post_nonce'); ?>
                                <input type="hidden" name="action" value="wcbe_export_products">
                                <div id="wcbe-export-items-selected"></div>
                                <div class="wcbe-export-fields">
                                    <div class="wcbe-export-field-item">
                                        <strong class="label"><?php esc_html_e('Products', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong>
                                        <label class="wcbe-export-radio">
                                            <input type="radio" name="products" value="all" checked="checked" id="wcbe-export-all-items-in-table">
                                            <?php esc_html_e('All Products In Table', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </label>
                                        <label class="wcbe-export-radio">
                                            <input type="radio" name="products" id="wcbe-export-only-selected-items" value="selected" disabled="disabled">
                                            <?php esc_html_e('Only Selected products', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </label>
                                    </div>
                                    <div class="wcbe-export-field-item">
                                        <strong class="label"><?php esc_html_e('Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong>
                                        <label class="wcbe-export-radio">
                                            <input type="radio" name="fields" value="all" checked="checked">
                                            <?php esc_html_e('All Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </label>
                                        <label class="wcbe-export-radio">
                                            <input type="radio" name="fields" value="visible">
                                            <?php esc_html_e('Only Visible Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                                        </label>
                                    </div>
                                    <div class="wcbe-export-field-item">
                                        <label class="label" for="wcbe-export-delimiter"><?php esc_html_e('Delimiter', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                        <select name="wcbe_export_delimiter" id="wcbe-export-delimiter">
                                            <option value=",">,</option>
                                            <option value=";">;</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="wcbe-export-buttons">
                                    <div class="wcbe-export-buttons-left">
                                        <button type="submit" class="wcbe-button wcbe-button-lg wcbe-button-blue" id="wcbe-export-products">
                                            <i class="wcbe-icon-filter1"></i>
                                            <span><?php esc_html_e('Export Now', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="wcbe-import">
                            <div class="wcbe-import-content">
                                <p><?php esc_html_e("If you have products in another system, you can import those into this site. ", 'ithemeland-woo-bulk-product-editor-lite'); ?></p>
                            </div>
                            <div class="wcbe-import-buttons">
                                <div class="wcbe-import-buttons-left">
                                    <a href="<?php echo esc_url(admin_url("edit.php?post_type=product&page=product_importer")); ?>" target="_blank" class="wcbe-button wcbe-button-lg wcbe-button-blue">
                                        <i class="wcbe-icon-filter1"></i>
                                        <span><?php esc_html_e('Import Now', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>