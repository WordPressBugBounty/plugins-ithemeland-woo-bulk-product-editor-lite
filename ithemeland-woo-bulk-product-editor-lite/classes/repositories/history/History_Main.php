<?php

namespace wcbel\classes\repositories\history;

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
        $where_items = "history.reverted = 0 AND history.sub_system = '{$this->sub_system}' ";
        if (!empty($where)) {
            foreach ($where as $field => $value) {
                $field = esc_sql($field);
                $value = esc_sql($value);
                switch ($field) {
                    case 'operation_type':
                        $where_items .= " AND history.{$field} = '{$value}'";
                        break;
                    case 'user_id':
                        $where_items .= " AND history.{$field} = {$value}";
                        break;
                    case 'fields':
                        $fields = explode(',', $value);
                        if (!empty($fields) && is_array($fields)) {
                            foreach ($fields as $field_item) {
                                $where_items .= " AND history.{$field} LIKE '%{$field_item}%'";
                            }
                        }
                        break;
                    case 'operation_date':
                        $from = (!empty($value['from'])) ? gmdate('Y-m-d H:i:s', strtotime($value['from'])) : null;
                        $to = (!empty($value['to'])) ? gmdate('Y-m-d H:i:s', (strtotime($value['to']) + 86400)) : null;
                        if (!empty($from) || !empty($to)) {
                            if (!empty($from) & !empty($to)) {
                                $where_items .= " AND (history.{$field} BETWEEN '{$from}' AND '{$to}')";
                            } else if (!empty($from)) {
                                $where_items .= " AND history.{$field} >= '{$from}'";
                            } else {
                                $where_items .= " AND history.{$field} < '{$to}'";
                            }
                        }
                        break;
                }
            }
        }

        if (!current_user_can('administrator')) {
            $user_id = get_current_user_id();
            $where_items .= " AND history.user_id = {$user_id}";
        }

        $limit = intval(sanitize_text_field($limit));
        $offset = intval(sanitize_text_field($offset));

        $limit_offset = (!empty($offset)) ? "LIMIT {$limit}, {$offset}" : "LIMIT {$limit}";
        return $this->wpdb->get_results("SELECT * FROM {$this->history_table} history WHERE {$where_items} ORDER BY history.id DESC {$limit_offset}"); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }

    public function get_history_count($where = [])
    {
        $where_items = "history.reverted = 0 AND history.sub_system = '{$this->sub_system}' ";
        if (!empty($where)) {
            foreach ($where as $field => $value) {
                $field = sanitize_text_field($field);
                $value = (is_array($value)) ? Sanitizer::array($value) : sanitize_text_field($value);
                switch ($field) {
                    case 'operation_type':
                        $where_items .= " AND history.{$field} = '{$value}'";
                        break;
                    case 'user_id':
                        $where_items .= " AND history.{$field} = {$value}";
                        break;
                    case 'fields':
                        $fields = explode(',', $value);
                        if (!empty($fields) && is_array($fields)) {
                            foreach ($fields as $field_item) {
                                $where_items .= " AND history.{$field} LIKE '%{$field_item}%'";
                            }
                        }
                        break;
                    case 'operation_date':
                        $from = (!empty($value['from'])) ? gmdate('Y-m-d H:i:s', strtotime($value['from'])) : null;
                        $to = (!empty($value['to'])) ? gmdate('Y-m-d H:i:s', (strtotime($value['to']) + 86400)) : null;
                        if (!empty($from) || !empty($to)) {
                            if (!empty($from) & !empty($to)) {
                                $where_items .= " AND (history.{$field} BETWEEN '{$from}' AND '{$to}')";
                            } else if (!empty($from)) {
                                $where_items .= " AND history.{$field} >= '{$from}'";
                            } else {
                                $where_items .= " AND history.{$field} < '{$to}'";
                            }
                        }
                        break;
                }
            }
        }

        if (!current_user_can('administrator')) {
            $user_id = get_current_user_id();
            $where_items .= " AND history.user_id = {$user_id}";
        }

        $result = $this->wpdb->get_results("SELECT COUNT(id) as 'count' FROM {$this->history_table} history WHERE {$where_items} ORDER BY history.id DESC"); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (!empty($result[0]) && !empty($result[0]->count)) ? $result[0]->count : 0;
    }

    public function get_history_items($history_id)
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT history_items.*, posts.post_title FROM {$this->history_items_table} history_items INNER JOIN {$this->wpdb->prefix}posts posts ON (history_items.historiable_id = posts.ID) WHERE history_id = %d", intval($history_id))); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }

    public function get_history_rows($history_id, $params = [])
    {
        $items_table_columns = [
            'id',
            'history_id',
            'historiable_id',
            'field',
            'prev_value',
            'new_value',
        ];

        $limit = '';
        if (!empty($params['limit'])) {
            $limit .= 'LIMIT ' . intval($params['limit']);
            if (!empty($params['offset'])) {
                $limit .= ' OFFSET ' . intval($params['offset']);
            }
        }

        $columns_string = '';
        if (!empty($params['columns'])) {
            $columns = array_filter($params['columns'], function ($value) use ($items_table_columns) {
                return (in_array($value, $items_table_columns));
            });
            if (!empty($columns)) {
                $columns_string = implode(', ', $columns);
            } else {
                $columns_string = 'id';
            }
        } else {
            $columns_string = '*';
        }

        $orderby = '';
        if (!empty($params['orderby'])) {
            $orderby = 'ORDER BY id ' . sanitize_text_field($params['orderby']);
        }

        return $this->wpdb->get_results($this->wpdb->prepare("SELECT {$columns_string} FROM {$this->history_items_table} WHERE history_id = %d {$orderby} {$limit}", intval($history_id))); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
    }

    public function get_history_rows_count($history_id)
    {
        $result = $this->wpdb->get_row($this->wpdb->prepare("SELECT COUNT(*) AS `count` FROM {$this->history_items_table} WHERE history_id = %d", intval($history_id)), ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
        return (!empty($result['count'])) ? intval($result['count']) : 0;
    }

    public function get_history_items_total_count($history_id, $column)
    {
        if (!in_array($column, ['prev_total_count', 'new_total_count'])) {
            return false;
        }

        $column = sanitize_text_field($column);
        $result = $this->wpdb->get_row($this->wpdb->prepare("SELECT SUM($column) AS `total_count` FROM {$this->history_items_table} WHERE history_id = %d", intval($history_id)), ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
        return (!empty($result['total_count'])) ? intval($result['total_count']) : 0;
    }

    public function get_latest_history()
    {
        return $this->wpdb->get_results("SELECT * FROM {$this->history_table} WHERE reverted = 0 AND sub_system = '{$this->sub_system}' ORDER BY id DESC LIMIT 1"); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }

    public function get_latest_reverted()
    {
        return $this->wpdb->get_results("SELECT * FROM {$this->history_table} WHERE reverted = 1 AND sub_system = '{$this->sub_system}' ORDER BY id DESC LIMIT 1"); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
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
        $result = $this->wpdb->update($this->history_table, $where, [
            'id' => intval($history_id),
        ]);

        return !empty($result);
    }

    public function clear_all()
    {
        $this->wpdb->query("DELETE FROM {$this->history_table} WHERE sub_system = '{$this->sub_system}'"); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    }
}
