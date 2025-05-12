<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-float-side-modal" id="wcbe-float-side-modal-schedule-jobs">
    <div class="wcbe-float-side-modal-container">
        <div class="wcbe-float-side-modal-box">
            <div class="wcbe-float-side-modal-content">
                <div class="wcbe-float-side-modal-title">
                    <h2><?php esc_html_e('Scheduled jobs', 'ithemeland-woo-bulk-product-editor-lite'); ?></h2>
                    <button type="button" class="wcbe-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wcbe-icon-x"></i>
                    </button>
                </div>
                <div class="wcbe-float-side-modal-body">
                    <div class="wcbe-wrap" style="padding-top: 10px;">
                        <?php
                        if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE):
                            include WCBEL_VIEWS_DIR . 'alerts/warning-active-pro.php';
                        else:
                        ?>
                            <div class="wcbe-float-side-modal-schedule-jobs-table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('#', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Schedules', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Status', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                            <th><?php esc_html_e('Actions', 'ithemeland-woo-bulk-product-editor-lite'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="100%" align="center"><img src="<?php echo esc_url(WCBEL_IMAGES_URL . 'loading-2.gif'); ?>" width="20" height="20" alt="Loading"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (defined('WCBE_ACTIVE') && WCBE_ACTIVE) {
    include "edit_job.php";
    include_once "job_log.php";
}
