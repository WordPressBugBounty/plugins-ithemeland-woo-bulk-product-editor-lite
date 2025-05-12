<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>
<div class="wcbe-tab-content-item" data-content="set_schedule">
    <?php
    if (!defined('WCBE_ACTIVE') || !WCBE_ACTIVE):
        include WCBEL_VIEWS_DIR . 'alerts/warning-active-pro.php';
    else:
    ?>
        <div class="wcbe-alert wcbe-alert-default" style="padding-bottom: 10px;">
            <span><?php esc_html_e('The current Bulk Form will be saved as a job and applied on specific date/time.', 'ithemeland-woo-bulk-product-editor-lite'); ?>
                <button type="button" class="wcbe-schedule-current-time-update-button"><i class="wcbe-icon-refresh-cw"></i></button>
                <div style="float: right;">
                    <span style="color: #444;"><?php esc_html_e('Universal time is:', 'ithemeland-woo-bulk-product-editor-lite'); ?> </span>
                    <span class="wcbe-set-schedule-current-time"><?php echo esc_html(current_datetime()->format('Y-m-d H:i')); ?></span>
                </div>
            </span>
        </div>
        <div class="wcbe-wrap">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Enable Schedule Bulk Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="checkbox" class="wcbe-set-schedule-enable-schedule required" value="yes">
            </div>
            <?php include WCBEL_DIR . "classes/services/scheduler/views/job_form/job_form.php"; ?>
        </div>
    <?php endif; ?>
</div>