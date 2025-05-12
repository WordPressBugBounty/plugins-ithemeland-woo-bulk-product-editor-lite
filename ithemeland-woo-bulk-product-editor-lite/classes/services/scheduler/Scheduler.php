<?php

namespace wcbel\classes\services\scheduler;

use wcbel\classes\helpers\Render;
use wcbel\classes\helpers\Sanitizer;
use wcbel\classes\services\scheduler\model\Schedule_Job;

abstract class Scheduler
{
    protected $repository;
    protected $identifier;
    private $schedule_hook;
    private $job_log_prefix;

    public function __construct()
    {
        $this->repository = Schedule_Job::get_instance();
        $this->schedule_hook = 'itbbc_schedule_event';

        $this->job_log_prefix = 'itbbc_schedule_log_job_';

        add_filter('cron_schedules', [$this, 'add_schedules'], 10, 1);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_action($this->identifier . '_bulk_edit_form_after_tab_title', [$this, 'add_tab_title']);
        add_action($this->identifier . '_bulk_edit_form_after_tab_content', [$this, 'add_tab_content']);
        add_action($this->identifier . '_navigation_buttons_before_settings', [$this, 'add_tabs_list_button']);
        add_action($this->identifier . '_bulk_edit_form_after_bulk_edit_button', [$this, 'add_action_button']);
        add_action($this->identifier . '_layout_footer', [$this, 'add_tabs_list_content']);
        add_action($this->schedule_hook, [$this, 'schedule_handler']);
        add_action($this->identifier . '_schedule_update_completed', [$this, 'update_complete_handler'], 10, 1);
        add_action($this->identifier . '_schedule_revert_completed', [$this, 'revert_complete_handler'], 10, 1);

        add_action('wp_ajax_wcbe_schedule_get_current_time', [$this, 'get_current_time']);

        add_action('wp_ajax_' . $this->identifier . '_schedule_update_job', [$this, 'update_job']);
        add_action('wp_ajax_' . $this->identifier . '_schedule_job_stop', [$this, 'stop_job']);
        add_action('wp_ajax_' . $this->identifier . '_schedule_job_delete', [$this, 'delete_job']);
        add_action('wp_ajax_' . $this->identifier . '_schedule_job_edit', [$this, 'edit_job']);
        add_action('wp_ajax_' . $this->identifier . '_schedule_get_job_log', [$this, 'get_job_log_ajax']);
        add_action('wp_ajax_' . $this->identifier . '_schedule_get_job_data', [$this, 'get_job_data']);

        if (!wp_next_scheduled($this->schedule_hook)) {
            wp_schedule_event(time(), 'itbbc_scheduler_2m', $this->schedule_hook);
        }
    }

    public function get_current_time()
    {
        wp_send_json([
            'time' => current_datetime()->format('Y-m-d H:i')
        ]);
    }

    public function get_job_data()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['job_id'])) {
            wp_send_json([
                'success' => false
            ]);
        }

        $job = $this->repository->get_job(intval($_POST['job_id']), $this->identifier);
        if (empty($job)) {
            wp_send_json([
                'success' => false
            ]);
        }

        $job->stop_date = (!empty($job->stop_date)) ? gmdate('Y-m-d H:i', $job->stop_date) : '';
        $job->revert_date = (!empty($job->revert_date)) ? gmdate('Y-m-d H:i', $job->revert_date) : '';

        if (!empty($job->dates)) {
            $job->dates = json_decode($job->dates, true);
        }

        wp_send_json([
            'success' => true,
            'job' => $job
        ]);
    }

    public function get_job_log_ajax()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['job_id'])) {
            wp_send_json([
                'success' => false
            ]);
        }

        $job = $this->repository->get_job(intval($_POST['job_id']), $this->identifier);
        $log = $this->get_job_log(intval($_POST['job_id']));
        if (empty($job) || empty($log)) {
            wp_send_json([
                'success' => false
            ]);
        }

        $log_html = Render::html(WCBEL_DIR . 'classes/services/scheduler/views/jobs_list/job_log_content.php', compact('job', 'log'));

        wp_send_json([
            'success' => true,
            'html' => $log_html
        ]);
    }

    public function stop_job()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['job_id'])) {
            wp_send_json([
                'success' => false
            ]);
        }

        $result = $this->repository->update([
            'id' => intval($_POST['job_id']),
            'identifier' => $this->identifier
        ], [
            'status' => $this->repository::STOPPED
        ]);

        if ($result) {
            $this->update_job_log(intval($_POST['job_id']), [
                'action' => 'stop_button',
                'datetime' => current_datetime()->format('Y-m-d H:i')
            ]);
        }

        $schedule_jobs = $this->get_jobs();
        $jobs = Render::html(WCBEL_DIR . 'classes/services/scheduler/views/jobs_list/rows.php', compact('schedule_jobs'));
        $awaiting_jobs = $this->repository->get_awaiting_count([
            'identifier' => $this->identifier
        ]);

        wp_send_json([
            'success' => $result,
            'rows' => $jobs,
            'awaiting_count' => $awaiting_jobs
        ]);
    }

    public function delete_job()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['job_id'])) {
            wp_send_json([
                'success' => false
            ]);
        }

        $result = $this->repository->delete(intval($_POST['job_id']), $this->identifier);
        if ($result) {
            $this->delete_job_log(intval($_POST['job_id']));
        }

        $schedule_jobs = $this->get_jobs();
        $jobs = Render::html(WCBEL_DIR . 'classes/services/scheduler/views/jobs_list/rows.php', compact('schedule_jobs', 'identifier'));
        $awaiting_jobs = $this->repository->get_awaiting_count([
            'identifier' => $this->identifier
        ]);

        wp_send_json([
            'success' => $result,
            'rows' => $jobs,
            'awaiting_count' => $awaiting_jobs
        ]);
    }

    public function update_job()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcbe_ajax_nonce')) {
            die();
        }

        if (empty($_POST['job_id']) || empty($_POST['label']) || empty($_POST['run_at'])) {
            wp_send_json([
                'success' => false
            ]);
        }

        if (!empty($_POST['run_for']) && $_POST['run_for'] == 'once') {
            if (!empty($_POST['dates']['type']) && in_array($_POST['dates']['type'], ['n_hours_later', 'n_days_later'])) {
                $_POST['dates']['now'] = strtotime(current_datetime()->format('Y-m-d H:i'));
            }
        }

        $this->repository->update([
            'id' => intval($_POST['job_id']),
            'identifier' => $this->identifier,
        ], [
            'label' => sanitize_text_field(wp_unslash($_POST['label'])),
            'description' => (isset($_POST['description'])) ? sanitize_text_field(wp_unslash($_POST['description'])) : null,
            'run_at' => sanitize_text_field(wp_unslash($_POST['run_at'])),
            'run_for' => (!empty($_POST['run_for'])) ? sanitize_text_field(wp_unslash($_POST['run_for'])) : null,
            'dates' => (!empty($_POST['dates'])) ? Sanitizer::array($_POST['dates']) : null, //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            'stop_date' => (!empty($_POST['stop_date'])) ? sanitize_text_field(wp_unslash($_POST['stop_date'])) : null,
            'revert_date' => (!empty($_POST['revert_date'])) ? sanitize_text_field(wp_unslash($_POST['revert_date'])) : null,
        ]);

        if ($_POST['run_at'] == 'now') {
            $handle_result = $this->schedule_handler();

            $awaiting_jobs = $this->repository->get_awaiting_count([
                'identifier' => $this->identifier
            ]);

            wp_send_json([
                'success' => (isset($handle_result['success'])) ? $handle_result['success'] : false,
                'is_processing' => (isset($handle_result['is_processing'])) ? $handle_result['is_processing'] : false,
                'awaiting_count' => $awaiting_jobs
            ]);
        }

        wp_send_json([
            'success' => true,
        ]);
    }

    public function schedule_handler()
    {
        $processing_jobs = $this->repository->get_jobs([
            'identifier' => $this->identifier,
            'status' => [$this->repository::PROCESSING]
        ]);

        if (!empty($processing_jobs)) {
            return;
        }

        $jobs = $this->repository->get_jobs([
            'identifier' => $this->identifier,
            'status' => [$this->repository::PENDING, $this->repository::RUNNING, $this->repository::DONE]
        ]);

        if (empty($jobs)) {
            return;
        }

        $now = strtotime(current_datetime()->format('Y-m-d H:i'));
        foreach ($jobs as $job) {
            if (!empty($job->stop_date) && in_array($job->run_for, ['daily', 'weekly', 'monthly']) && intval($job->stop_date) <= $now) {
                $status = (!empty($job->revert_date) && intval($job->revert_date) >= intval($job->stop_date)) ?  $this->repository::DONE : $this->repository::COMPLETED;
                if ($status != $job->status) {
                    $this->repository->update([
                        'id' => $job->id,
                        'identifier' => $this->identifier
                    ], [
                        'status' => $status
                    ]);

                    $this->update_job_log($job->id, [
                        'action' => 'stop_date',
                        'datetime' => current_datetime()->format('Y-m-d H:i')
                    ]);
                }
            }

            if (!empty($job->revert_date) && intval($job->revert_date) <= $now && $job->status != $this->repository::PENDING) {
                $this->repository->update([
                    'id' => $job->id,
                    'identifier' => $this->identifier
                ], [
                    'status' => $this->repository::PROCESSING
                ]);

                $this->revert_handle($job);
                return;
            }

            if ($job->status != $this->repository::DONE) {
                if ($job->run_at == 'now') {
                    $this->repository->update([
                        'id' => $job->id,
                        'identifier' => $this->identifier
                    ], [
                        'status' => $this->repository::PROCESSING
                    ]);
                    $handle_result = $this->update_handle($job);
                    break;
                }

                if ($job->run_for == 'once') {
                    $dates = json_decode($job->dates, true);
                    if (empty($dates['type'])) {
                        continue;
                    }

                    switch ($dates['type']) {
                        case 'specific_date_time':
                            if (!empty($dates['date_time'])) {
                                $date_time = strtotime($dates['date_time']);
                            }
                            break;
                        case 'n_hours_later':
                            if (!empty($dates['time']) && !empty(intval($dates['now']))) {
                                $times = explode(':', $dates['time']);
                                if (isset($times[0]) && isset($times[1])) {
                                    $date_time = strtotime(gmdate('Y-m-d H:i', intval($dates['now'])) . ' + ' . intval($times[0]) . ' hours ' . intval($times[1]) . ' minutes');
                                }
                            }
                            break;
                        case 'n_days_later':
                            if (!empty(intval($dates['days'])) && !empty(intval($dates['now']))) {
                                $date_time = strtotime(gmdate('Y-m-d H:i', intval($dates['now'])) . ' + ' . intval($dates['days']) . ' days');
                            }
                            break;
                    }

                    if (!empty($date_time) && $date_time <= $now) {
                        $this->repository->update([
                            'id' => $job->id,
                            'identifier' => $this->identifier
                        ], [
                            'status' => $this->repository::PROCESSING
                        ]);
                        $handle_result = $this->update_handle($job);
                        break;
                    }
                }

                if ($job->status == $this->repository::RUNNING && !empty($job->last_run_time) && gmdate('d', $job->last_run_time) == gmdate('d', $now)) {
                    continue;
                }

                if ($job->run_for == 'daily') {
                    $dates = json_decode($job->dates, true);
                    if (empty($dates['time'])) {
                        continue;
                    }
                    $date_time = $this->get_today_timestamp_with_time($dates['time']);
                    if (!empty($date_time) && $date_time <= $now) {
                        $this->repository->update([
                            'id' => $job->id,
                            'identifier' => $this->identifier
                        ], [
                            'status' => $this->repository::PROCESSING
                        ]);
                        $handle_result = $this->update_handle($job);
                        break;
                    }
                }

                if ($job->run_for == 'weekly') {
                    $dates = json_decode($job->dates, true);
                    if (empty($dates['days']) || !is_array($dates['days']) || empty($dates['time'])) {
                        continue;
                    }
                    foreach ($dates['days'] as $day) {
                        if (strtolower($day) == strtolower(gmdate('l', $now))) {
                            $date_time = $this->get_today_timestamp_with_time($dates['time']);
                            if (!empty($date_time) && $date_time <= $now) {
                                $this->repository->update([
                                    'id' => $job->id,
                                    'identifier' => $this->identifier
                                ], [
                                    'status' => $this->repository::PROCESSING
                                ]);
                                $handle_result = $this->update_handle($job);
                                break;
                            }
                        }
                    }
                }

                if ($job->run_for == 'monthly') {
                    $dates = json_decode($job->dates, true);
                    if (empty($dates['days']) || !is_array($dates['days']) || empty($dates['time'])) {
                        continue;
                    }
                    foreach ($dates['days'] as $day) {
                        if ($day == gmdate('j', $now)) {
                            $date_time = $this->get_today_timestamp_with_time($dates['time']);
                            if (!empty($date_time) && $date_time <= $now) {
                                $this->repository->update([
                                    'id' => $job->id,
                                    'identifier' => $this->identifier
                                ], [
                                    'status' => $this->repository::PROCESSING
                                ]);
                                $handle_result = $this->update_handle($job);
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($handle_result) && $handle_result['success']) {
            if (!empty($handle_result['history_id'])) {
                $this->repository->update([
                    'id' => $job->id,
                    'identifier' => $this->identifier
                ], [
                    'history_id' => intval($handle_result['history_id'])
                ]);
            }

            return $handle_result;
        }
    }

    public function add_tab_title()
    {
        echo wp_kses('<li><a class="wcbe-tab-item" data-content="set_schedule" href="#">' . esc_html__('Set Schedule', 'ithemeland-woo-bulk-product-editor-lite') . '</a></li>', Sanitizer::allowed_html());
    }

    public function add_tab_content()
    {
        $identifier = $this->identifier;
        ob_start();
        include WCBEL_DIR . 'classes/services/scheduler/views/bulk_edit_form/set_schedule.php';
        echo wp_kses(ob_get_clean(), Sanitizer::allowed_html());
    }

    public function add_tabs_list_content()
    {
        $identifier = $this->identifier;
        ob_start();
        include WCBEL_DIR . 'classes/services/scheduler/views/jobs_list/main.php';
        echo wp_kses(ob_get_clean(), Sanitizer::allowed_html());
    }

    public function add_tabs_list_button()
    {
        $awaiting_jobs = $this->repository->get_awaiting_count([
            'identifier' => $this->identifier
        ]);

        ob_start();
        include WCBEL_DIR . 'classes/services/scheduler/views/jobs_list/navigation_button.php';
        echo wp_kses(ob_get_clean(), Sanitizer::allowed_html());
    }

    public function add_action_button()
    {
        ob_start();
        include WCBEL_DIR . 'classes/services/scheduler/views/bulk_edit_form/action_button.php';
        echo wp_kses(ob_get_clean(), Sanitizer::allowed_html());
    }

    public function enqueue_scripts($page)
    {
        if (!isset($_GET['page']) || (isset($_GET['page']) && $_GET['page'] != $this->identifier)) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        wp_enqueue_style('wcbe-schedule', WCBEL_URL . 'classes/services/scheduler/assets/css/schedule.css', [], WCBEL_VERSION);
        wp_enqueue_script('wcbe-schedule', WCBEL_URL . 'classes/services/scheduler/assets/js/schedule.js', ['jquery'], WCBEL_VERSION); //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter 
        wp_localize_script('wcbe-schedule', 'WCBE_SCHEDULE_DATA', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wcbe_ajax_nonce'),
            'identifier' => $this->identifier
        ]);
    }

    public function add_schedules($schedules)
    {
        $schedules['itbbc_scheduler_2m'] = [
            'interval' => 1 * MINUTE_IN_SECONDS,
            'display'  => esc_html__('Every 2 minutes', 'ithemeland-woo-bulk-product-editor-lite')
        ];

        return $schedules;
    }

    public function add_job($data)
    {
        if (!is_array($data)) {
            return false;
        }

        if (!empty($data['run_for']) && $data['run_for'] == 'once') {
            if (!empty($data['dates']['type']) && in_array($data['dates']['type'], ['n_hours_later', 'n_days_later'])) {
                $data['dates']['now'] = strtotime(current_datetime()->format('Y-m-d H:i'));
            }
        }

        if (!empty($data['run_for']) && in_array($data['run_for'], ['daily', 'weekly', 'monthly'])) {
            if (!empty($data['dates']['time'])) {
                if (strtotime(current_datetime()->format('Y-m-d H:i')) >= $this->get_today_timestamp_with_time($data['dates']['time'])) {
                    $data['last_run_time'] = strtotime(current_datetime()->format('Y-m-d H:i'));
                }
            }
        }

        if (!empty($data['stop_date']) || !empty($data['revert_date'])) {
            $run_time = $this->get_run_time($data);

            if (!empty($run_time)) {
                if (!empty($data['stop_date']) && $run_time >= strtotime($data['stop_date'])) {
                    return [
                        'success' => false,
                        'message' => 'The Stop schedule date should be after running the schedule.'
                    ];
                }

                if (!empty($data['revert_date']) && $run_time >= strtotime($data['revert_date'])) {
                    return [
                        'success' => false,
                        'message' => 'The Revert changes date should be after running the schedule.'
                    ];
                }
            }
        }

        $data['identifier'] = $this->identifier;
        $result = $this->repository->create($data);

        return [
            'success' => $result,
        ];
    }

    private function get_today_timestamp_with_time($time)
    {
        return strtotime(current_datetime()->format('Y-m-d') . ' ' . $time);
    }

    protected function clear_event()
    {
        return wp_clear_scheduled_hook($this->schedule_hook);
    }

    public function get_jobs()
    {
        return $this->repository->get_jobs([
            'identifier' => $this->identifier,
        ]);
    }

    public function update_complete_handler($data)
    {
        if (empty($data['job_id']) || !is_numeric($data['job_id'])) {
            return;
        }

        $job = $this->repository->get_job(intval($data['job_id']), $this->identifier);
        if (empty($job)) {
            return;
        }

        if (!empty($data['background_process_result'])) {
            switch ($data['background_process_result']) {
                case 'stopped':
                    $status = $this->repository::STOPPED;
                    break;
                case 'crashed':
                    $status = $this->repository::INCOMPLETE;
                    break;
                case 'completed':
                    if (in_array($job->run_for, ['daily', 'weekly', 'monthly'])) {
                        $status = $this->repository::RUNNING;
                    } else {
                        $status = (empty($job->stop_date) && empty($job->revert_date)) ? $this->repository::COMPLETED : $this->repository::DONE;
                    }
                    break;
            }
        } else {
            if (isset($data['result']) && $data['result']) {
                if (in_array($job->run_for, ['daily', 'weekly', 'monthly'])) {
                    $status = $this->repository::RUNNING;
                } else {
                    $status = (empty($job->stop_date) && empty($job->revert_date)) ? $this->repository::COMPLETED : $this->repository::DONE;
                }
            } else {
                $status = $this->repository::INCOMPLETE;
            }
        }

        $this->update_job_log($job->id, [
            'action' => 'update',
            'datetime' => current_datetime()->format('Y-m-d H:i')
        ]);

        $this->repository->update([
            'id' => $job->id,
            'identifier' => $this->identifier
        ], [
            'status' => $status,
            'last_run_time' => strtotime(current_datetime()->format('Y-m-d H:i'))
        ]);
    }

    public function revert_complete_handler($data)
    {
        if (empty($data['job_id']) || !is_numeric($data['job_id'])) {
            return;
        }

        if (isset($data['background_process_result'])) {
            $status = ($data['background_process_result'] == 'stopped') ? $this->repository::STOPPED : $this->repository::COMPLETED;
        } else {
            $status = (isset($data['result']) && $data['result'] === true) ? $this->repository::COMPLETED : $this->repository::INCOMPLETE;
        }

        $this->repository->update([
            'id' => intval($data['job_id']),
            'identifier' => $this->identifier
        ], [
            'status' => $status,
            'last_run_time' => strtotime(current_datetime()->format('Y-m-d H:i'))
        ]);

        $this->update_job_log(intval($data['job_id']), [
            'action' => 'revert_date',
            'datetime' => current_datetime()->format('Y-m-d H:i')
        ]);
    }

    private function update_job_log($job_id, $data)
    {
        $log = get_option($this->job_log_prefix . intval($job_id), []);
        if (!empty($data['action']) && !empty($data['datetime'])) {
            $log[] = [
                'action' => sanitize_text_field($data['action']),
                'datetime' => sanitize_text_field($data['datetime']),
            ];
        }
        return update_option($this->job_log_prefix . intval($job_id), $log);
    }

    private function delete_job_log($job_id)
    {
        return delete_option($this->job_log_prefix . intval($job_id));
    }

    private function get_job_log($job_id)
    {
        return get_option($this->job_log_prefix . intval($job_id), []);
    }

    private function get_run_time($data)
    {
        if (empty($data['dates']) || empty($data['run_at'])) {
            return 0;
        }

        $now = strtotime(current_datetime()->format('Y-m-d H:i'));

        if ($data['run_at'] == 'now') {
            return $now;
        }

        if ($data['run_at'] == 'later') {
            if ($data['run_for'] == 'once') {
                switch ($data['dates']['type']) {
                    case 'specific_date_time':
                        if (!empty($data['dates']['date_time'])) {
                            return strtotime($data['dates']['date_time']);
                        }
                        break;
                    case 'n_hours_later':
                        if (!empty($data['dates']['time']) && !empty(intval($data['dates']['now']))) {
                            $times = explode(':', $data['dates']['time']);
                            if (isset($times[0]) && isset($times[1])) {
                                return strtotime(gmdate('Y-m-d H:i', intval($data['dates']['now'])) . ' + ' . intval($times[0]) . ' hours ' . intval($times[1]) . ' minutes');
                            }
                        }
                        break;
                    case 'n_days_later':
                        if (!empty(intval($data['dates']['days'])) && !empty(intval($data['dates']['now']))) {
                            return strtotime(gmdate('Y-m-d H:i', intval($data['dates']['now'])) . ' + ' . intval($data['dates']['days']) . ' days');
                        }
                        break;
                }
            }

            if ($data['run_for'] == 'daily') {
                return $this->get_today_timestamp_with_time($data['dates']['time']);
            }

            if ($data['run_for'] == 'weekly' && !empty($data['dates']['days'])) {
                foreach ($data['dates']['days'] as $day) {
                    if (strtolower($day) == strtolower(gmdate('l', $now))) {
                        return $this->get_today_timestamp_with_time($data['dates']['time']);
                    }
                }
            }

            if ($data['run_for'] == 'monthly' && !empty($data['dates']['days'])) {
                foreach ($data['dates']['days'] as $day) {
                    if ($day == gmdate('j', $now)) {
                        return $this->get_today_timestamp_with_time($data['dates']['time']);
                    }
                }
            }
        }

        return 0;
    }

    abstract function update_handle($job);

    abstract function revert_handle($job);
}
