<?php

namespace wcbel\classes\services\background_process;

use wcbel\classes\services\background_process\lib\WP_Background_Process;

defined('ABSPATH') || exit(); // Exit if accessed directly

abstract class BackgroundProcess extends WP_Background_Process
{
    protected $handlers;
    protected $stop_option;
    protected $completed_tasks_option;
    protected $complete_message_option;
    protected $total_tasks_option;
    protected $last_handle_time_option;
    protected $start_time_option;
    protected $complete_actions_option;

    protected $action = 'background_process';

    abstract protected function get_handlers();

    public function add_completed_task($count)
    {
        if (!empty($count)) {
            $total_completed = get_option($this->completed_tasks_option, 0);
            update_option($this->completed_tasks_option, intval($total_completed) + intval($count));
        }
    }

    public function __construct()
    {
        parent::__construct();

        $this->handlers = $this->get_handlers();
        $this->stop_option = $this->prefix . '_background_process_stop';
        $this->completed_tasks_option = $this->prefix . '_background_process_completed_tasks';
        $this->complete_message_option = $this->prefix . '_background_process_complete_message';
        $this->total_tasks_option = $this->prefix . '_background_process_total_tasks';
        $this->last_handle_time_option = $this->prefix . '_background_process_last_handle_time';
        $this->start_time_option = $this->prefix . '_background_process_start_time';
        $this->complete_actions_option = $this->prefix . '_background_process_complete_actions';
    }

    protected function task($item)
    {
        $force_stop = get_option($this->stop_option, 'false');
        if ($force_stop == 'true') {
            return false;
        }

        if (isset($item['handler']) && isset($this->handlers[$item['handler']])) {
            update_option($this->last_handle_time_option, time());

            try {
                $handler = new $this->handlers[$item['handler']]();
                $handler->handle($item);
            } catch (\Exception $e) {
                update_option($this->prefix . '_background_process_error', $e->getMessage());
            }
        }

        return false;
    }

    protected function complete()
    {
        parent::complete();

        $this->set_complete_message([
            'message' => 'Your changes have been applied',
            'icon' => 'wcbe-icon-check-circle',
        ]);

        $force_stop = get_option($this->stop_option, 'false');
        $background_process_result = ($force_stop == 'true') ? 'stopped' : 'completed';
        $this->do_complete_actions($background_process_result);

        update_option($this->stop_option, 'false');
    }

    public function is_not_queue_empty()
    {
        return !$this->is_queue_empty();
    }

    public function is_processing()
    {
        return ($this->is_process_running() && $this->is_not_queue_empty());
    }

    public function stop_process()
    {
        if (!$this->is_queue_empty()) {
            update_option($this->stop_option, 'true');

            $batch = $this->get_batch();

            $this->delete($batch->key);

            wp_clear_scheduled_hook($this->cron_hook_identifier);
        }
    }

    public function is_force_stopped()
    {
        return (get_option($this->stop_option, 'false') == 'true');
    }

    public function add_complete_action($action)
    {
        $actions = $this->get_complete_actions();
        array_push($actions, $action);
        update_option($this->complete_actions_option, $actions);
        return $this;
    }

    public function get_complete_actions()
    {
        return get_option($this->complete_actions_option, []);
    }

    public function clear_complete_actions()
    {
        return delete_option($this->complete_actions_option);
    }

    public function save()
    {
        update_option($this->last_handle_time_option, time());

        $key = $this->generate_key();
        if (!empty($this->data)) {
            update_option($key, $this->data);
        }

        $this->data = [];
        return $this;
    }

    public function start()
    {
        update_option($this->start_time_option, time());
        update_option($this->stop_option, 'false');

        $this->dispatch();
    }

    private function set_complete_message($message)
    {
        return update_option($this->complete_message_option, [
            'message' => $message['message'],
            'icon' => $message['icon'],
        ]);
    }

    public function clear_complete_message()
    {
        update_option($this->completed_tasks_option, 0);
        update_option($this->total_tasks_option, 0);
        update_option($this->last_handle_time_option, 0);
        update_option($this->start_time_option, 0);
        return delete_option($this->complete_message_option);
    }

    public function get_remaining_time()
    {
        $start_time = intval(get_option($this->start_time_option, 0));
        $total = intval(get_option($this->total_tasks_option, 0));
        $completed = intval(get_option($this->completed_tasks_option, 0));
        if (empty($start_time) || empty($completed) || empty($total)) {
            return false;
        }

        $delay = (intval($total) > 3000) ? 60 : 30;
        if (($start_time + $delay) > time()) {
            return false;
        }

        $seconds = round(($total - $completed) * (time() - $start_time) / $completed);
        $hours = floor($seconds / 3600);
        $minutes = ($seconds <= 60) ? 1 : floor(($seconds / 60) % 60);

        return empty($hours) ? $minutes . " Minutes" : "{$hours} Hour and $minutes Minutes";
    }

    public function get_complete_message()
    {
        return get_option($this->complete_message_option, []);
    }

    public function set_total_tasks($total_tasks)
    {
        return update_option($this->total_tasks_option, intval($total_tasks));
    }

    public function get_total_tasks()
    {
        return get_option($this->total_tasks_option, 0);
    }

    public function get_completed_tasks_count()
    {
        return get_option($this->completed_tasks_option, 0);
    }

    public function clear_tasks_count()
    {
        update_option($this->total_tasks_option, 0);
        update_option($this->completed_tasks_option, 0);
    }

    public function delete_all_batches()
    {
        global $wpdb;

        $like = $wpdb->esc_like($this->prefix . '_background_process_batch') . '%';
        $query = $wpdb->prepare("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE %s", $like); //phpcs:ignore
        return $wpdb->query($query); //phpcs:ignore
    }

    public function crash_handler()
    {
        $last_handle_time = get_option($this->last_handle_time_option);
        if ((!empty($last_handle_time) && intval($last_handle_time + 120) < time())) {
            $timeout = get_option('_site_transient_timeout_' . $this->prefix . '_background_process_process_lock');
            $try_again = get_option($this->prefix . '_background_process_try_again', 0);
            if (empty($timeout) && (empty($try_again) || (!empty($try_again) && intval($try_again + 180) < time()))) {
                update_option($this->prefix . '_background_process_try_again', time());
                $this->dispatch();
                return false;
            } else {
                delete_option($this->prefix . '_background_process_try_again');
                $this->delete_all_batches();
                delete_option('_site_transient_timeout_' . $this->prefix . '_background_process_process_lock');
                delete_option('_site_transient_' . $this->prefix . '_background_process_process_lock');
                $this->do_complete_actions('crashed');
                return true;
            }
        }

        return false;
    }

    private function do_complete_actions($background_process_result)
    {
        $complete_actions = $this->get_complete_actions();
        if (!empty($complete_actions)) {
            foreach ($complete_actions as $action) {
                if (!empty($action['handler'])) {
                    $handler = new $this->handlers[$action['handler']]();
                    $data = !empty($action['data']) ? $action['data'] : [];
                    $data['background_process_result'] = $background_process_result;
                    $handler->complete($data);
                }

                if (!empty($action['hook'])) {
                    if (!empty($action['data'])) {
                        $action['data']['background_process_result'] = $background_process_result;
                        do_action($action['hook'], $action['data']);
                    } else {
                        do_action($action['hook']);
                    }
                }
            }

            $this->clear_complete_actions();
        }
    }
}
