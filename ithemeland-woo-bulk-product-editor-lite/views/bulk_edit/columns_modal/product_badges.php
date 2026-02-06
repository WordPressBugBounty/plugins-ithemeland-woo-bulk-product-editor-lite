<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-product-badges">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-sm">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Product badges', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-product-badges-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-form-group">
                            <label for="wcbe-modal-product-badge-items"><?php esc_html_e('Product badges', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                            <select class="wcbe-select2" id="wcbe-modal-product-badge-items" multiple data-placeholder="<?php esc_html_e('Select ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                <?php
                                $badges = get_posts(['post_type' => 'yith-wcbm-badge', 'posts_per_page' => -1, 'order' => 'ASC']);
                                if (!empty($badges)) {
                                    foreach ($badges as $badge) {
                                        if ($badge instanceof \WP_Post) {
                                            echo '<option value="' . esc_attr($badge->ID) . '">' . esc_html($badge->post_title) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-product-badges-apply" data-item-id="" data-field="" data-content-type="select_files" class="wcbe-button wcbe-button-blue" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="wcbe-button wcbe-button-gray wcbe-float-right" data-toggle="modal-close">
                        <?php esc_html_e('Close', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>