<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>

<div class="wcbe-set-schedule-form">
    <div class="wcbe-form-group">
        <label><?php esc_html_e('Name', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <input type="text" class="wcbe-set-schedule-name required" placeholder="<?php esc_html_e('Name', 'ithemeland-woo-bulk-product-editor-lite'); ?> ...">
    </div>
    <div class="wcbe-form-group">
        <label><?php esc_html_e('Description', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <textarea class="wcbe-set-schedule-description" placeholder="<?php esc_html_e('Description', 'ithemeland-woo-bulk-product-editor-lite'); ?> ..."></textarea>
    </div>
    <div class="wcbe-form-group">
        <label><?php esc_html_e('Run at', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
        <select class="wcbe-set-schedule-run-at required">
            <option value="now"><?php esc_html_e('Now', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
            <option value="later"><?php esc_html_e('Later', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
        </select>
    </div>
    <div class="wcbe-set-schedule-dependent">
        <div data-content="later">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Run for', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <select class="wcbe-set-schedule-run-for required">
                    <option value="once"><?php esc_html_e('Once', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="daily"><?php esc_html_e('Daily', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="weekly"><?php esc_html_e('Weekly', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="monthly"><?php esc_html_e('Monthly', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                </select>
            </div>
        </div>

        <div data-content="once">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Type', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <select class="wcbe-set-schedule-once-type required">
                    <option value="specific_date_time"><?php esc_html_e('Specific Date & Time', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="n_hours_later"><?php esc_html_e('n Hours later', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="n_days_later"><?php esc_html_e('n Days later', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                </select>
            </div>
        </div>

        <div data-content="specific_date_time">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Date & Time', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-datetimepicker wcbe-set-schedule-once-date-time required" placeholder="Date & Time">
            </div>
        </div>

        <div data-content="n_hours_later">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select time', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-timepicker wcbe-set-schedule-once-hours required" placeholder="Time">
                <span style="line-height: 34px; font-size: 14px;"><?php esc_html_e('Later', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </div>
        </div>

        <div data-content="n_days_later">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Days', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="number" class="wcbe-set-schedule-once-days required" placeholder="Days">
                <span style="line-height: 34px; font-size: 14px;"><?php esc_html_e('Days Later', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </div>
        </div>

        <div data-content="daily">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Time', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-timepicker wcbe-set-schedule-daily-time required" placeholder="Time">
            </div>
        </div>

        <div data-content="weekly">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Days', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <select class="wcbe-select2 wcbe-set-schedule-weekly-days required" multiple>
                    <option value="monday"><?php esc_html_e('Monday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="tuesday"><?php esc_html_e('Tuesday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="wednesday"><?php esc_html_e('Wednesday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="thursday"><?php esc_html_e('Thursday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="friday"><?php esc_html_e('Friday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="saturday"><?php esc_html_e('Saturday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                    <option value="sunday"><?php esc_html_e('Sunday', 'ithemeland-woo-bulk-product-editor-lite'); ?></option>
                </select>
            </div>
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Time', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-timepicker wcbe-set-schedule-weekly-time required" placeholder="Time">
            </div>
        </div>

        <div data-content="monthly">
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Days', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <select class="wcbe-select2 wcbe-set-schedule-monthly-days required" multiple>
                    <?php for ($i = 1; $i <= 31; $i++) : ?>
                        <option value="<?php echo intval($i); ?>"><?php echo intval($i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="wcbe-form-group">
                <label><?php esc_html_e('Select Time', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-timepicker wcbe-set-schedule-monthly-time required" placeholder="Time">
            </div>
        </div>

        <div data-content="stop_schedule">
            <div class="wcbe-form-group">
                <label><strong><?php esc_html_e('Stop', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong> <?php esc_html_e('schedule on', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-datetimepicker wcbe-set-schedule-stop-date-time" placeholder="Date & Time">
                <span class="wcbe-set-schedule-short-description"><?php esc_html_e('Leave blank if you don\'t want to stop the schedule on specific Date & Time.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </div>
        </div>

        <div data-content="revert_changes">
            <div class="wcbe-form-group">
                <label><strong><?php esc_html_e('Revert', 'ithemeland-woo-bulk-product-editor-lite'); ?></strong> <?php esc_html_e('Last Update', 'ithemeland-woo-bulk-product-editor-lite'); ?></label>
                <input type="text" class="wcbe-schedule-datetimepicker wcbe-set-schedule-revert-date-time" placeholder="Date & Time">
                <span class="wcbe-set-schedule-short-description"><?php esc_html_e('Leave blank if you don\'t want revert changes.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
                <span class="wcbe-set-schedule-short-description"><?php esc_html_e('If set date, it will override your previous update.', 'ithemeland-woo-bulk-product-editor-lite'); ?></span>
            </div>
        </div>
    </div>
</div>