<?php

namespace wcbel\classes\repositories\history;

defined('ABSPATH') || exit();

use wcbel\classes\helpers\Sanitizer;

class History_Main
{
    const BULK_OPERATION = 'bulk';
    const INLINE_OPERATION = 'inline';

    protected $wpdb;
    protected $sub_system;
    protected $history_table;
    protected $history_items_table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->history_table = $this->wpdb->prefix . 'itbbc_history';
        $this->history_items_table = $this->wpdb->prefix . 'itbbc_history_items';
    }

    public static function get_operation_types()
    {
        return [
            self::BULK_OPERATION => esc_html__('Bulk Operation', 'ithemeland-woo-bulk-product-editor-lite'),
            self::INLINE_OPERATION => esc_html__('Inline Operation', 'ithemeland-woo-bulk-product-editor-lite'),
        ];
    }

    public static function get_operation_type($operation_type)
    {
        $operation_types = self::get_operation_types();
        return (isset($operation_types[$operation_type])) ? $operation_types[$operation_type] : "";
    }

    public function create_history($data)
    {
        $data['sub_system'] = $this->sub_system;
        $format = ['%d', '%s', '%s', '%s', '%s'];
        $this->wpdb->insert($this->history_table, $data, $format); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $this->wpdb->insert_id;
    }

    public function create_history_item($data)
    {
        $format = ['%d', '%d', '%s', '%s', '%s', '%d', '%d'];
        $this->wpdb->insert($this->history_items_table, $data, $format); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $this->wpdb->insert_id;
    }

    public function save_history_item($data)
    {
        if (empty($data['history_id']) || !isset($data['historiable_id']) || empty($data['name']) || empty($data['type'])) {
            return false;
        }

        return $this->create_history_item([
            'history_id' => intval($data['history_id']),
            'historiable_id' => intval($data['historiable_id']),
            'field' => serialize([
                'name' => sanitize_text_field($data['name']),
                'sub_name' => (!empty($data['sub_name'])) ? sanitize_text_field($data['sub_name']) : '',
                'type' => sanitize_text_field($data['type']),
                'action' => (!empty($data['action'])) ? sanitize_text_field($data['action']) : '',
                'undo_operator' => (!empty($data['undo_operator'])) ? sanitize_text_field($data['undo_operator']) : '',
                'redo_operator' => (!empty($data['redo_operator'])) ? sanitize_text_field($data['redo_operator']) : '',
                'extra_fields' => (!empty($data['extra_fields'])) ? esc_sql($data['extra_fields']) : [],
                'deleted_ids' => (!empty($data['deleted_ids'])) ? esc_sql($data['deleted_ids']) : [],
                'created_ids' => (!empty($data['created_ids'])) ? esc_sql($data['created_ids']) : [],
            ]),
            'prev_value' => (!empty($data['prev_value'])) ? serialize($data['prev_value']) : '',
            'new_value' => (!empty($data['new_value'])) ? serialize($data['new_value']) : '',
            'prev_total_count' => (isset($data['prev_total_count'])) ? intval($data['prev_total_count']) : 1,
            'new_total_count' => (isset($data['new_total_count'])) ? intval($data['new_total_count']) : 1,
        ]);
    }

    public function get_histories($where = [], $limit = 10, $offset = 0)
    {
        global $wpdb;

        $query = "SELECT * FROM {$this->history_table} history WHERE history.reverted = 0 AND history.sub_system = %s";
        $params = [$this->sub_system];

        if (!empty($where)) {
            foreach ($where as $field => $value) {
                switch ($field) {
                    case 'operation_type':
                        $query .= " AND history.operation_type = %s";
                        $params[] = $value;
                        break;

                    case 'user_id':
                        $query .= " AND history.user_id = %d";
                        $params[] = intval($value);
                        break;

                    case 'fields':
                        $fields = explode(',', $value);
                        foreach ($fields as $field_item) {
                            $query .= " AND history.fields LIKE %s";
                            $params[] = '%' . $wpdb->esc_like($field_item) . '%';
                        }
                        break;

                    case 'operation_date':
                        $from = (!empty($value['from'])) ? gmdate('Y-m-d H:i:s', strtotime($value['from'])) : null;
                        $to = (!empty($value['to'])) ? gmdate('Y-m-d H:i:s', strtotime($value['to']) + 86400) : null;

                        if ($from && $to) {
                            $query .= " AND history.operation_date BETWEEN %s AND %s";
                            $params[] = $from;
                            $params[] = $to;
                        } elseif ($from) {
                            $query .= " AND history.operation_date >= %s";
                            $params[] = $from;
                        } elseif ($to) {
                            $query .= " AND history.operation_date < %s";
                            $params[] = $to;
                        }
                        break;
                }
            }
        }

        if (!current_user_can('administrator')) {
            $query .= " AND history.user_id = %d";
            $params[] = get_current_user_id();
        }

        $query .= " ORDER BY history.id DESC LIMIT %d OFFSET %d";
        $params[] = intval($limit);
        $params[] = intval($offset);

        return $wpdb->get_results($wpdb->prepare($query, ...$params)); //phpcs:ignore
    }

    public function get_history_count($where = [])
    {
        global $wpdb;

        $query = "SELECT COUNT(id) as count FROM {$this->history_table} history WHERE history.reverted = 0 AND history.sub_system = %s";
        $params = [$this->sub_system];

        if (!empty($where)) {
            foreach ($where as $field => $value) {
                switch ($field) {
                    case 'operation_type':
                        $query .= " AND history.operation_type = %s";
                        $params[] = $value;
                        break;

                    case 'user_id':
                        $query .= " AND history.user_id = %d";
                        $params[] = intval($value);
                        break;

                    case 'fields':
                        $fields = explode(',', $value);
                        foreach ($fields as $field_item) {
                            $query .= " AND history.fields LIKE %s";
                            $params[] = '%' . $wpdb->esc_like($field_item) . '%';
                        }
                        break;

                    case 'operation_date':
                        $from = !empty($value['from']) ? gmdate('Y-m-d H:i:s', strtotime($value['from'])) : null;
                        $to = !empty($value['to']) ? gmdate('Y-m-d H:i:s', strtotime($value['to']) + 86400) : null;

                        if ($from && $to) {
                            $query .= " AND history.operation_date BETWEEN %s AND %s";
                            $params[] = $from;
                            $params[] = $to;
                        } elseif ($from) {
                            $query .= " AND history.operation_date >= %s";
                            $params[] = $from;
                        } elseif ($to) {
                            $query .= " AND history.operation_date < %s";
                            $params[] = $to;
                        }
                        break;
                }
            }
        }

        if (!current_user_can('administrator')) {
            $query .= " AND history.user_id = %d";
            $params[] = get_current_user_id();
        }

        $prepared_query = $wpdb->prepare($query, ...$params); //phpcs:ignore
        $result = $wpdb->get_row($prepared_query); //phpcs:ignore

        return (!empty($result) && isset($result->count)) ? intval($result->count) : 0;
    }

    public function get_history_items($history_id)
    {
        global $wpdb;

        $query = "
            SELECT history_items.*, posts.post_title
            FROM {$this->history_items_table} history_items
            INNER JOIN {$wpdb->prefix}posts posts
                ON history_items.historiable_id = posts.ID
            WHERE history_items.history_id = %d
        ";

        return $wpdb->get_results($wpdb->prepare($query, intval($history_id))); //phpcs:ignore
    }

    public function get_history_rows($history_id, $params = [])
    {
        global $wpdb;

        $items_table_columns = [
            'id',
            'history_id',
            'historiable_id',
            'field',
            'prev_value',
            'new_value',
        ];

        $limit_offset = '';
        if (!empty($params['limit'])) {
            $limit = intval($params['limit']);
            $offset = !empty($params['offset']) ? intval($params['offset']) : 0;
            $limit_offset = $wpdb->prepare('LIMIT %d OFFSET %d', $limit, $offset);
        }

        $columns_string = '*';
        if (!empty($params['columns'])) {
            $columns = array_filter($params['columns'], function ($value) use ($items_table_columns) {
                return in_array($value, $items_table_columns);
            });
            if (!empty($columns)) {
                $columns_string = implode(', ', array_map('sanitize_key', $columns));
            } else {
                $columns_string = 'id';
            }
        }

        $orderby = '';
        if (!empty($params['orderby']) && in_array(strtolower($params['orderby']), ['asc', 'desc'])) {
            $orderby = 'ORDER BY id ' . strtoupper(sanitize_text_field($params['orderby']));
        }

        $query = "
            SELECT {$columns_string}
            FROM {$this->history_items_table}
            WHERE history_id = %d
            {$orderby}
            {$limit_offset}
        ";

        return $wpdb->get_results($wpdb->prepare($query, $history_id)); //phpcs:ignore
    }

    public function get_history_rows_count($history_id)
    {
        $result = $this->wpdb->get_row($this->wpdb->prepare("SELECT COUNT(*) AS `count` FROM {$this->history_items_table} WHERE history_id = %d", intval($history_id)), ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
        return (!empty($result['count'])) ? intval($result['count']) : 0;
    }

    public function get_history_items_total_count($history_id, $column)
    {
        $allowed_columns = ['prev_total_count', 'new_total_count'];

        if (!in_array($column, $allowed_columns, true)) {
            return false;
        }

        global $wpdb;
        $column = sanitize_key($column);
        $query = "
            SELECT SUM(`$column`) AS total_count
            FROM {$this->history_items_table}
            WHERE history_id = %d
        ";

        $result = $wpdb->get_row($wpdb->prepare($query, intval($history_id)), ARRAY_A); //phpcs:ignore
        return (!empty($result['total_count'])) ? intval($result['total_count']) : 0;
    }

    public function get_latest_history()
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->history_table} WHERE reverted = 0 AND sub_system = %s ORDER BY id DESC LIMIT 1", sanitize_text_field($this->sub_system))); //phpcs:ignore
    }

    public function get_latest_reverted()
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->history_table} WHERE reverted = 1 AND sub_system = %s ORDER BY id DESC LIMIT 1", sanitize_text_field($this->sub_system))); //phpcs:ignore
    }

    public function delete_history($history_id)
    {
        // Delete the history item
        $result = $this->wpdb->delete($this->history_table, [
            'id' => intval($history_id),
        ], [
            '%d',
        ]);

        // If the deletion was successful, decrement the undo and redo counts
        if (!empty($result)) {
            $user_id = get_current_user_id(); // Get the current user ID

            // Decrement the undo count
            $user_undo_count = get_user_meta($user_id, 'wcbe_undo_count', true);
            $user_undo_count = empty($user_undo_count) ? 0 : intval($user_undo_count);
            if ($user_undo_count > 0) {
                $user_undo_count--;
                update_user_meta($user_id, 'wcbe_undo_count', $user_undo_count);
            }

            // Decrement the redo count
            $user_redo_count = get_user_meta($user_id, 'wcbe_redo_count', true);
            $user_redo_count = empty($user_redo_count) ? 0 : intval($user_redo_count);
            if ($user_redo_count > 0) {
                $user_redo_count--;
                update_user_meta($user_id, 'wcbe_redo_count', $user_redo_count);
            }
        }

        return !empty($result);
    }

    public function get_history($history_id)
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->history_table} WHERE id = %d", intval($history_id))); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
    }

    public function update_history($history_id, $where)
    {
        $result = $this->wpdb->update($this->history_table, $where, [ //phpcs:ignore
            'id' => intval($history_id),
        ]);

        return !empty($result);
    }

    public function clear_all()
    {
        $this->wpdb->query($this->wpdb->prepare("DELETE FROM {$this->history_table} WHERE sub_system = %s", sanitize_text_field($this->sub_system))); //phpcs:ignore
    }
}
