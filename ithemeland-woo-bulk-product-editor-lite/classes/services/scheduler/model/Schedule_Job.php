<?php

namespace wcbel\classes\services\scheduler\model;

defined('ABSPATH') || exit();

use wcbel\classes\helpers\Sanitizer;

class Schedule_Job
{
    const VERSION = '1.0.2';

    const PENDING = 'pending';
    const RUNNING = 'running';
    const DONE = 'done';
    const COMPLETED = 'completed';
    const INCOMPLETE = 'incomplete';
    const STOPPED = 'stopped';
    const PROCESSING = 'processing';

    private static $instance;

    private $fillable = [
        'history_id',
        'label',
        'description',
        'run_at',
        'run_for',
        'dates',
        'filter_items',
        'edit_items',
        'stop_date',
        'revert_date',
        'status',
        'identifier',
        'created_at',
        'last_run_time',
    ];

    private $table_name;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'itbbc_schedule_jobs';

        $scheduler_version = get_option('wcbe_scheduler_version');
        if (empty($scheduler_version) || $scheduler_version != self::VERSION) {
            $this->maybe_create_table();
            update_option('wcbe_scheduler_version', self::VERSION);
        }
    }

    private function maybe_create_table()
    {
        global $wpdb;

        $query = '';
        if (!$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($this->table_name))) == $this->table_name) { //phpcs:ignore
            $query .= "CREATE TABLE {$this->table_name} ( " //phpcs:ignore
                . "id int(11) NOT NULL AUTO_INCREMENT,
                  history_id int(11) NOT NULL,
                  label varchar(64) NOT NULL,
                  description text,
                  run_at varchar(64) NOT NULL,
                  run_for varchar(64),
                  dates text,
                  filter_items longtext NOT NULL,
                  edit_items longtext NOT NULL,
                  stop_date int(11),
                  revert_date int(11),
                  status varchar(64) NOT NULL,
                  identifier varchar(64) NOT NULL,
                  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  last_run_time int(11),
                  
                  PRIMARY KEY (id),
                  INDEX (history_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        }

        if (!empty($query)) {
            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($query);
        }
    }

    public function create($data)
    {
        global $wpdb;

        if (empty($data) || !is_array($data)) {
            return false;
        }

        $data = array_filter($data, function ($column) {
            return in_array($column, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($data['stop_date'])) {
            $stop_date = (!is_numeric($data['stop_date'])) ? intval(strtotime($data['stop_date'])) : intval($data['stop_date']);
        }

        if (!empty($data['revert_date'])) {
            $revert_date = (!is_numeric($data['revert_date'])) ? intval(strtotime($data['revert_date'])) : intval($data['revert_date']);
        }

        $wpdb->insert($this->table_name, [ //phpcs:ignore
            'history_id' => (!empty($data['history_id'])) ? intval($data['history_id']) : 0,
            'label' => sanitize_text_field($data['label']),
            'description' => (isset($data['description'])) ? sanitize_text_field($data['description']) : '',
            'run_at' => sanitize_text_field($data['run_at']),
            'run_for' => (!empty($data['run_for'])) ? sanitize_text_field($data['run_for']) : null,
            'dates' => (!empty($data['dates'])) ? wp_json_encode($data['dates']) : null,
            'filter_items' => (!empty($data['filter_items'])) ? wp_json_encode($data['filter_items']) : null,
            'edit_items' => wp_json_encode($data['edit_items']),
            'stop_date' => (!empty($stop_date)) ? intval($stop_date) : null,
            'revert_date' => (!empty($revert_date)) ? intval($revert_date) : null,
            'status' => (isset($data['status'])) ? sanitize_text_field($data['status']) : self::PENDING,
            'identifier' => sanitize_text_field($data['identifier']),
            'created_at' => current_datetime()->format('Y-m-d H:i'),
            'last_run_time' => (!empty($data['last_run_time'])) ? intval($data['last_run_time']) : null,
        ], [
            '%d', //history_id
            '%s', //label
            '%s', //description
            '%s', //run_at
            '%s', //run_for
            '%s', //dates
            '%s', //filter_items
            '%s', //edit_items
            '%s', //stop_date
            '%s', //revert_date
            '%s', //status
            '%s', //identifier
            '%s', //created_at
            '%d', //last_run_time
        ]);

        return $wpdb->insert_id;
    }

    public function update($where, $data)
    {
        if (empty($where) || !is_array($where) || empty($where['id']) || empty($where['identifier'])) {
            return false;
        }

        $data = array_filter($data, function ($column) {
            return in_array($column, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($data)) {
            return false;
        }

        global $wpdb;
        foreach ($data as $key => $value) {
            if (in_array($key, ['stop_date', 'revert_date'])) {
                $data[$key] = strtotime($value);
            } elseif (is_array($value)) {
                $data[$key] = wp_json_encode(Sanitizer::array($value));
            } else {
                $data[$key] = sanitize_text_field($value);
            }
        }

        $where_clause = [
            'id' => intval($where['id']),
            'identifier' => sanitize_text_field($where['identifier']),
        ];

        return $wpdb->update($this->table_name, $data, $where_clause); //phpcs:ignore
    }

    public function delete($id, $identifier)
    {
        global $wpdb;
        return $wpdb->delete($this->table_name, [ //phpcs:ignore
            'id' => intval($id),
            'identifier' => sanitize_text_field($identifier),
        ], [
            '%d',
            '%s',
        ]);
    }

    public function get_job($id, $identifier)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d AND identifier = %s", intval($id), sanitize_text_field($identifier))); //phpcs:ignore
    }

    public function get_jobs($args)
    {
        global $wpdb;

        $where_clauses = [];
        $params = [];

        if (!empty($args['identifier'])) {
            $where_clauses[] = 'schedule_jobs.identifier = %s';
            $params[] = sanitize_text_field($args['identifier']);
        }

        if (!empty($args['status'])) {
            if (is_array($args['status'])) {
                $placeholders = implode(',', array_fill(0, count($args['status']), '%s'));
                $where_clauses[] = "schedule_jobs.status IN ($placeholders)";
                foreach ($args['status'] as $status) {
                    $params[] = sanitize_text_field($status);
                }
            } else {
                $where_clauses[] = 'schedule_jobs.status = %s';
                $params[] = sanitize_text_field($args['status']);
            }
        }

        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }

        $limit_sql = '';
        if (!empty($args['limit']) && is_numeric($args['limit'])) {
            $limit_sql = ' LIMIT ' . intval($args['limit']);
        }

        $query = "SELECT * FROM {$this->table_name} schedule_jobs {$where_sql} ORDER BY schedule_jobs.id DESC {$limit_sql}";

        return $wpdb->get_results($wpdb->prepare($query, ...$params)); //phpcs:ignore
    }

    public function get_awaiting_count($args)
    {
        global $wpdb;

        $where_clauses = [];
        $params = [];

        $awaiting_statuses = [self::PENDING, self::RUNNING, self::DONE, self::PROCESSING];
        $status_placeholders = implode(',', array_fill(0, count($awaiting_statuses), '%s'));
        $where_clauses[] = "schedule_jobs.status IN ($status_placeholders)";
        $params = array_merge($params, $awaiting_statuses);

        if (!empty($args['identifier'])) {
            $where_clauses[] = 'schedule_jobs.identifier = %s';
            $params[] = sanitize_text_field($args['identifier']);
        }

        $where_sql = (!empty($where_clauses)) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        $query = "SELECT COUNT(*) as awaiting_count FROM {$this->table_name} schedule_jobs {$where_sql}";
        $result = $wpdb->get_row($wpdb->prepare($query, ...$params)); //phpcs:ignore

        return (!empty($result->awaiting_count)) ? intval($result->awaiting_count) : 0;
    }

    public function get_statuses()
    {
        return [
            self::PENDING => esc_html__('Pending', 'ithemeland-woo-bulk-product-editor-lite'),
            self::RUNNING => esc_html__('Running', 'ithemeland-woo-bulk-product-editor-lite'),
            self::DONE => esc_html__('Done', 'ithemeland-woo-bulk-product-editor-lite'),
            self::COMPLETED => esc_html__('Completed', 'ithemeland-woo-bulk-product-editor-lite'),
            self::INCOMPLETE => esc_html__('Incomplete', 'ithemeland-woo-bulk-product-editor-lite'),
            self::STOPPED => esc_html__('Stopped', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }
}
