<?php

namespace wcbel\classes\services\scheduler;

defined('ABSPATH') || exit();

use wcbel\classes\services\scheduler\model\Schedule_Job;

class Job_Presenter
{
    public static function status($status)
    {
        if (empty($status)) {
            return;
        }

        ob_start();

        switch ($status) {
            case Schedule_Job::PENDING:
                include 'views/jobs_list/statuses/pending.php';
                break;
            case Schedule_Job::RUNNING:
                include 'views/jobs_list/statuses/running.php';
                break;
            case Schedule_Job::DONE:
                include 'views/jobs_list/statuses/done.php';
                break;
            case Schedule_Job::COMPLETED:
                include 'views/jobs_list/statuses/completed.php';
                break;
            case Schedule_Job::INCOMPLETE:
                include 'views/jobs_list/statuses/incomplete.php';
                break;
            case Schedule_Job::STOPPED:
                include 'views/jobs_list/statuses/stopped.php';
                break;
            case Schedule_Job::PROCESSING:
                include 'views/jobs_list/statuses/processing.php';
                break;
        }

        return ob_get_clean();
    }

    public static function log_action($action)
    {
        $output = '';
        switch ($action) {
            case 'update':
                $output = 'Updated';
                break;
            case 'stop_button':
                $output = 'Stopped Manually';
                break;
            case 'stop_date':
                $output = 'Stopped date';
                break;
            case 'revert_date':
                $output = 'Reverted changes date';
                break;
        }

        return $output;
    }

    public static function edit_items($edit_items)
    {
        $output = '';
        $edit_items = json_decode($edit_items, true);
        if (!empty($edit_items) && is_array($edit_items)) {
            $i = 1;
            foreach ($edit_items as $item) {
                if (empty($item['name'])) {
                    continue;
                }
                $output .= ucfirst(str_replace('_', ' ', $item['name']));
                if ($i < count($edit_items)) {
                    $output .= ', ';
                }
            }
        }

        return $output;
    }

    public static function schedules($job)
    {
        $dates = json_decode($job->dates, true);
        $schedules = '';
        if ($job->run_at == 'now') {
            $schedules = '<strong> Now </strong> (' . gmdate('Y-m-d H:i', strtotime($job->created_at)) . ')';
        } else {
            switch ($job->run_for) {
                case 'once':
                    switch ($dates['type']) {
                        case 'specific_date_time':
                            if (!empty($dates['date_time'])) {
                                $schedules = '<strong>' . ucfirst($job->run_for) . '</strong> - ' . esc_html($dates['date_time']);
                            }
                            break;
                        case 'n_hours_later':
                            if (!empty($dates['time']) && !empty($dates['now'])) {
                                $times = explode(':', $dates['time']);
                                if (isset($times[0]) && isset($times[1])) {
                                    $date_time = strtotime(gmdate('Y-m-d H:i', intval($dates['now'])) . ' + ' . intval($times[0]) . ' hours ' . intval($times[1]) . ' minutes');
                                    $schedules = '<strong>' . ucfirst($job->run_for) . '</strong> - ' . esc_html(gmdate('Y-m-d H:i', $date_time));
                                }
                            }
                            break;
                        case 'n_days_later':
                            if (!empty($dates['days']) && !empty($dates['now'])) {
                                if (!empty(intval($dates['days'])) && !empty(intval($dates['now']))) {
                                    $date_time = strtotime(gmdate('Y-m-d H:i', intval($dates['now'])) . ' + ' . intval($dates['days']) . ' days');
                                    $schedules = '<strong>' . ucfirst($job->run_for) . '</strong> - ' . esc_html(gmdate('Y-m-d H:i', $date_time));
                                }
                            }
                            break;
                    }
                    break;
                case 'daily':
                    if (!empty($dates['time'])) {
                        $schedules = '<strong>' . ucfirst($job->run_for) . '</strong> - ' . esc_html($dates['time']);
                    }
                    break;
                case 'weekly':
                    if (!empty($dates['days']) && is_array($dates['days']) && !empty($dates['time'])) {
                        $schedules = '<strong>' . ucfirst($job->run_for) . '</strong> - ' . implode(',', array_map('ucfirst', $dates['days'])) . ' - ' . $dates['time'];
                    }
                    break;
                case 'monthly':
                    if (!empty($dates['days']) && is_array($dates['days']) && !empty($dates['time'])) {
                        $schedules = '<strong>' . ucfirst($job->run_for) . '</strong> - Days: ' . implode(',', $dates['days']) . ' - ' . $dates['time'];
                    }
                    break;
            }
        }


        if (!empty($job->stop_date)) {
            $schedules .= '<br> <strong>Stop on: </strong>' . esc_html(gmdate('Y-m-d H:i', $job->stop_date));
        }
        if (!empty($job->revert_date)) {
            $schedules .= '<br> <strong>Revert on: </strong>' . esc_html(gmdate('Y-m-d H:i', $job->revert_date));
        }

        return $schedules;
    }
}
