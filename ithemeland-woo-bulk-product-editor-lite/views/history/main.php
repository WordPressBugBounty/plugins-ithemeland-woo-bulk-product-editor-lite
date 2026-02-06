<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-history">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('History', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap">
                        <div class="wcbe-alert wcbe-alert-default">
                            <span><?php esc_html_e('List of your changes and possible to roll back to the previous data', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                        </div>
                        <?php if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) : ?>
                            <?php include WCBEL_VIEWS_DIR . 'alerts/warning-active-pro.php' ?>
                        <?php endif; ?>
                        <div class="wcbe-history-filter">
                            <div class="wcbe-history-filter-fields">
                                <div class="wcbe-history-filter-field-item">
                                    <label for="wcbe-history-filter-operation"><?php esc_html_e('Operation', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                    <select <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled="disabled"' : ''; ?> id="wcbe-history-filter-operation">
                                        <option value=""><?php esc_html_e('Select', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                                        <?php
                                        if (!empty($history_types = \wcbel\classes\repositories\history\History_Main::get_operation_types())) :
                                            foreach ($history_types as $history_type_key => $history_type_label) :
                                        ?>
                                                <option value="<?php echo esc_attr($history_type_key); ?>"><?php echo esc_html($history_type_label); ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="wcbe-history-filter-field-item">
                                    <label for="wcbe-history-filter-author"><?php esc_html_e('Author', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                    <select id="wcbe-history-filter-author" class="wcbe-select2-users"></select>
                                </div>
                                <div class="wcbe-history-filter-field-item">
                                    <label for="wcbe-history-filter-fields"><?php esc_html_e('Fields', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                    <input type="text" id="wcbe-history-filter-fields" placeholder="for example: ID">
                                </div>
                                <div class="wcbe-history-filter-field-item wcbe-history-filter-field-date">
                                    <label><?php esc_html_e('Date', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                                    <input type="text" id="wcbe-history-filter-date-from" class="wcbe-datepicker wcbe-date-from" data-to-id="wcbe-history-filter-date-to" placeholder="<?php esc_attr_e('From ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                    <input type="text" id="wcbe-history-filter-date-to" class="wcbe-datepicker" placeholder="<?php esc_attr_e('To ...', 'ithemeland-woo-bulk-product-editor-lite'); ?>">
                                </div>
                            </div>
                            <div class="wcbe-history-filter-buttons">
                                <div class="wcbe-history-filter-buttons-left">
                                    <button <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-button wcbe-button-lg wcbe-button-blue" id="wcbe-history-filter-apply">
                                        <i class="wcbe-icon-filter1"></i>
                                        <span><?php esc_html_e('Apply Filters', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </button>
                                    <button <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-button wcbe-button-lg wcbe-button-gray" id="wcbe-history-filter-reset">
                                        <i class="wcbe-icon-rotate-cw"></i>
                                        <span><?php esc_html_e('Reset Filters', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                    </button>
                                </div>
                                <div class="wcbe-history-filter-buttons-right">
                                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wcbe-history-clear-all">
                                        <?php wp_nonce_field('wcbe_post_nonce'); ?>
                                        <input <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled="disabled"' : ''; ?> type="hidden" name="action" value="wcbe_clear_all_history">
                                        <button <?php echo (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE) ? 'disabled="disabled"' : ''; ?> type="button" name="clear_all" value="1" id="wcbe-history-clear-all-btn" class="wcbe-button wcbe-button-lg wcbe-button-red">
                                            <i class="wcbe-icon-trash-2"></i>
                                            <span><?php esc_html_e('Clear History', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="wcbe-history-items">
                            <h3><?php esc_html_e('Column(s)', 'ithemeland-woo-bulk-product-editor-lite'); ?></h3>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wcbe-history-items">
                                <?php wp_nonce_field('wcbe_post_nonce'); ?>
                                <input type="hidden" name="action" value="wcbe_history_action">
                                <input type="hidden" name="" value="" id="wcbe-history-clicked-id">
                                <div class="wcbe-table-border-radius">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php esc_html_e('History Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                                <th><?php esc_html_e('Author', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                                <th class="wcbe-mw125"><?php esc_html_e('Date Modified', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                                <th class="wcbe-mw250"><?php esc_html_e('Actions', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" style="text-align: center;"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" alt="Loading ..." width="22" height="22"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="wcbe-history-pagination-container"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>