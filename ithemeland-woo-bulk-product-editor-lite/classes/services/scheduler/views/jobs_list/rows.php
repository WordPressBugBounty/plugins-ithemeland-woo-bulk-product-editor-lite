<?php

use wcbel\classes\services\scheduler\model\Schedule_Job;
use wcbel\classes\services\scheduler\Job_Presenter;

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

if (!empty($schedule_jobs)):
    $i = 1;
    foreach ($schedule_jobs as $job):
?>
        <tr>
            <td><?php echo intval($i); ?></td>
            <td><?php echo esc_html($job->label); ?></td>
            <td><?php echo wp_kses_post(Job_Presenter::schedules($job)); ?></td>
            <td><?php echo wp_kses_post(Job_Presenter::status($job->status)); ?></td>
            <td>
                <button <?php echo !defined('WCBE_ACTIVE') ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-schedule-jobs-list-action-button" data-id="<?php echo esc_attr($job->id); ?>" data-action="show_edit_items" data-toggle="modal" data-target="#wcbe-modal-schedule-job-edit-items" title="<?php esc_html_e('Edit Items', 'ithemeland-woo-bulk-product-editor-lite'); ?>"><i class="wcbe-icon-list1"></i></button>

                <?php if ($job->status == Schedule_Job::PENDING): ?>
                    <button <?php echo !defined('WCBE_ACTIVE') ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-schedule-jobs-list-action-button" data-id="<?php echo esc_attr($job->id); ?>" data-action="edit" data-toggle="modal" data-target="#wcbe-modal-schedule-edit-job" title="<?php esc_html_e('Edit', 'ithemeland-woo-bulk-product-editor-lite'); ?>"><i class="wcbe-icon-edit"></i></button>
                <?php else: ?>
                    <button type="button" class="wcbe-schedule-jobs-list-action-button" data-id="<?php echo esc_attr($job->id); ?>" data-action="log" data-toggle="modal" data-target="#wcbe-modal-schedule-job-log" title="<?php esc_html_e('Log', 'ithemeland-woo-bulk-product-editor-lite'); ?>"><i class="wcbe-icon-file-text1"></i></button>
                <?php endif; ?>

                <button <?php echo !defined('WCBE_ACTIVE') ? 'disabled="disabled"' : ''; ?> type="button" class="wcbe-schedule-jobs-list-action-button" data-id="<?php echo esc_attr($job->id); ?>" data-action="delete" title="<?php esc_html_e('Delete Job', 'ithemeland-woo-bulk-product-editor-lite'); ?>"><i class="wcbe-icon-trash-2"></i></button>

                <?php if ($job->status == Schedule_Job::RUNNING): ?>
                    <button type="button" class="wcbe-schedule-jobs-list-action-button" data-id="<?php echo esc_attr($job->id); ?>" data-action="stop" title="<?php esc_html_e('Stop Now', 'ithemeland-woo-bulk-product-editor-lite'); ?>"><i class="wcbe-icon-stop-circle"></i></button>
                <?php endif; ?>

                <?php if (!defined('WCBE_ACTIVE')): ?>
                    <span class="wcbe-alert-pro-description"><?php esc_html_e('Upgrade to pro version!', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                <?php endif; ?>
            </td>
        </tr>
    <?php
        $i++;
    endforeach;
else:
    ?>
    <tr>
        <td colspan="100%">No Data Available!</td>
    </tr>
<?php
endif;
?>