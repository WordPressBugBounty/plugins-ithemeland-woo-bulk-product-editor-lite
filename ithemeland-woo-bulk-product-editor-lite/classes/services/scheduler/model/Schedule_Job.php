<?php

namespace wcbel\classes\services\scheduler\model;

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

        $i = 1;
        $update_query = '';
        foreach ($data as $column => $value) {
            if (is_numeric($value)) {
                $update_query .= sanitize_text_field($column) . ' = ' . intval($value);
            } else {
                $value = (is_array($value)) ? wp_json_encode(Sanitizer::array($value)) : sanitize_text_field($value);
                if (in_array($column, ['stop_date', 'revert_date'])) {
                    $value = strtotime($value);
                }
                $update_query .= sanitize_text_field($column) . " = '" . $value . "'";
            }
            if (count($data) > $i) {
                $update_query .= ', ';
            }
            $i++;
        }

        return (!empty($update_query)) ? $wpdb->query($wpdb->prepare("UPDATE {$this->table_name} SET {$update_query} WHERE id = %d AND identifier = %s", intval($where['id']), sanitize_text_field($where['identifier']))) : false; //phpcs:ignore
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

        $where = '';
        if (!empty($args['identifier'])) {
            $where .= ' AND schedule_jobs.identifier = "' . sanitize_text_field($args['identifier']) . '"';
        }
        if (!empty($args['status'])) {
            if (is_array($args['status'])) {
                $statuses = '';
                $i = 1;
                foreach ($args['status'] as $status) {
                    $statuses .= "'" . sanitize_text_field($status) . "'";
                    if (count($args['status']) > $i) {
                        $statuses .= ",";
                    }
                    $i++;
                }
                $where .= ' AND schedule_jobs.status IN (' . $statuses . ')';
            } else {
                $where .= " AND schedule_jobs.status = '" . sanitize_text_field($args['status']) . "'";
            }
        }
        $limit = '';
        if (!empty($args['limit']) && is_numeric($args['limit'])) {
            $limit = ' LIMIT ' . intval($args['limit']);
        }

        return $wpdb->get_results("SELECT * FROM {$this->table_name} schedule_jobs WHERE 1=1 {$where} ORDER BY schedule_jobs.id DESC {$limit}"); //phpcs:ignore
    }

    public function get_awaiting_count($args)
    {
        global $wpdb;

        $where = '';
        if (!empty($args['identifier'])) {
            $where .= ' AND schedule_jobs.identifier = "' . sanitize_text_field($args['identifier']) . '"';
        }

        $awaiting_statuses = '"' . self::PENDING . '", "' . self::RUNNING . '", "' . self::DONE . '", "' . self::PROCESSING . '"';
        $result = $wpdb->get_row("SELECT COUNT(*) as awaiting_count FROM {$this->table_name} schedule_jobs WHERE schedule_jobs.status IN ({$awaiting_statuses}) {$where}"); //phpcs:ignore
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
