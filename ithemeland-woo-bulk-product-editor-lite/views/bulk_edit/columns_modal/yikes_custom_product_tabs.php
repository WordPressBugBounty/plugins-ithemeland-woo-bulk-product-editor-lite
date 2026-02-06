<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-modal" id="wcbe-modal-yikes-custom-product-tabs">
    <div class="wcbe-modal-container">
        <div class="wcbe-modal-box wcbe-modal-box-lg">
            <div class="wcbe-modal-content">
                <div class="wcbe-modal-title">
                    <h2><?php esc_html_e('Yikes Custom tabs', 'ithemeland-woo-bulk-product-editor-lite'); ?> - <span id="wcbe-modal-yikes-custom-product-tabs-item-title" class="wcbe-modal-item-title"></span></h2>
                    <button type="button" class="wcbe-modal-close" data-toggle="modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-modal-body">
                    <div class="wcbe-wrap">
                        <form id="yikes-custom-product-tabs-form">
                            <div id="wcbe-modal-yikes-custom-tabs"></div>
                        </form>
                        <div class="wcbe-modal-yikes-buttons">
                            <button type="button" class="wcbe-button wcbe-button-blue wcbe-float-right" id="wcbe-yikes-add-saved-tab"><?php esc_html_e('Add a saved tab', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                            <button type="button" class="wcbe-button wcbe-button-blue wcbe-float-right" id="wcbe-yikes-add-tab"><?php esc_html_e('Add a tab', 'ithemeland-woo-bulk-product-editor-lite'); ?></button>
                        </div>
                        <div class="wcbe-hide">
                            <?php
                            if (file_exists(WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/yikes_custom_tab_item.php')) {
                                $duplicate_item = 'yes';
                                include WCBEL_VIEWS_DIR . 'bulk_edit/columns_modal/yikes_custom_tab_item.php';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="wcbe-modal-footer">
                    <button type="button" id="wcbe-modal-yikes-custom-product-tabs-apply" data-item-id="" data-field="" data-content-type="select_files" class="wcbe-button wcbe-button-blue" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                    <button type="button" class="wcbe-button wcbe-button-gray wcbe-float-right" data-toggle="modal-close">
                        <?php esc_html_e('Close', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
    $saved_tabs = get_option('yikes_woo_reusable_products_tabs');
    if (!empty($saved_tabs)) :
    ?>
        <div class="wcbe-yikes-saved-tabs">
            <div class="wcbe-yikes-saved-tabs-box">
                <div class="wcbe-yikes-saved-tabs-header">
                    <strong><?php esc_html_e('Choose a tab', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong>
                    <button type="button" class="wcbe-yikes-saved-tabs-close-button"><i class="wcbe-icon-x"></i></button>
                    <hr>
                </div>
                <ul>
                    <?php foreach ($saved_tabs as $saved_tab) :
                        if (!empty($saved_tab['tab_slug'])) :
                    ?>
                            <li>
                                <?php echo esc_html($saved_tab['tab_slug']); ?>
                                <button type="button" data-id="<?php echo esc_attr($saved_tab['tab_id']); ?>" class="wcbe-yikes-saved-tab-add"><i class="wcbe-icon-plus1"></i></button>
                            </li>
                    <?php
                        endif;
                    endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>