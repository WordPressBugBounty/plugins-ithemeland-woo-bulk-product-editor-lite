<?php

namespace wcbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

class WCBEL_Custom_Queries
{
    private static $instance;

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    private function __construct()
    {
        add_filter('posts_where', [$this, 'general_column_filter'], 10, 2);
        add_filter('posts_where', [$this, 'meta_filter'], 10, 2);
    }

    private function get_wp_posts_columns()
    {
        return [
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_title',
            'post_excerpt',
            'post_status',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_content_filtered',
            'post_parent',
            'guid',
            'menu_order',
            'post_type',
            'post_mime_type',
            'comment_count',
        ];
    }

    public function general_column_filter($where, $wp_query)
    {
        global $wpdb;
        if ($search_term = $wp_query->get('wcbe_general_column_filter')) {
            if (is_array($search_term) && count($search_term) > 0) {
                $wp_posts_columns = $this->get_wp_posts_columns();
                foreach ($search_term as $item) {
                    $field = sanitize_text_field($item['field']);
                    if (!in_array($field, $wp_posts_columns)) {
                        continue;
                    }

                    $value = (is_array($item['value'])) ? array_map('sanitize_text_field', $item['value']) : trim(sanitize_text_field($item['value']));
                    switch ($item['operator']) {
                        case 'like':
                            $where .= "AND ({$wpdb->posts}.{$field} LIKE '%{$value}%')";
                            break;
                        case 'exact':
                            $where .= "AND ({$wpdb->posts}.{$field} = '{$value}')";
                            break;
                        case 'not':
                            $where .= "AND ({$wpdb->posts}.{$field} != '{$value}')";
                            break;
                        case 'begin':
                            $where .= "AND ({$wpdb->posts}.{$field} LIKE '{$value}%')";
                            break;
                        case 'end':
                            $where .= "AND ({$wpdb->posts}.{$field} LIKE '%{$value}')";
                            break;
                        case 'in':
                            $where .= "AND ({$wpdb->posts}.{$field} IN ({$value}))";
                            break;
                        case 'not_in':
                            $where .= "AND ({$wpdb->posts}.{$field} NOT IN ({$value}))";
                            break;
                        case 'between':
                            $value = (is_numeric($value[1])) ? "{$value[0]} AND {$value[1]}" : "'{$value[0]}' AND '{$value[1]}'";
                            $where .= "AND ({$wpdb->posts}.{$field} BETWEEN {$value})";
                            break;
                        case '>':
                            $where .= "AND ({$wpdb->posts}.{$field} > {$value})";
                            break;
                        case '<':
                            $where .= "AND ({$wpdb->posts}.{$field} < {$value})";
                            break;
                        case '>_with_quotation':
                            $where .= "AND ({$wpdb->posts}.{$field} > '{$value}')";
                            break;
                        case '<_with_quotation':
                            $where .= "AND ({$wpdb->posts}.{$field} < '{$value}')";
                            break;
                    }
                }
            }
        }

        return $where;
    }

    public function meta_filter($where, $wp_query)
    {
        if ($search_term = $wp_query->get('wcbe_meta_filter')) {
            if (is_array($search_term) && count($search_term) > 0) {
                foreach ($search_term as $item) {
                    $postmeta = 'postmeta_' . wp_rand(100, 999);
                    add_filter('posts_join', function ($join) use ($item, $postmeta) {
                        global $wpdb;
                        $join .= "LEFT JOIN $wpdb->postmeta AS {$postmeta} ON ($wpdb->posts.ID = {$postmeta}.post_id)";
                        return $join;
                    });

                    $key = sanitize_text_field($item['key']);
                    $value = (is_array($item['value'])) ? array_map('sanitize_text_field', $item['value']) : sanitize_text_field($item['value']);
                    switch ($item['operator']) {
                        case 'like':
                            $before = (!empty($item['before_str'])) ? sanitize_text_field($item['before_str']) : '';
                            $after = (!empty($item['after_str'])) ? sanitize_text_field($item['after_str']) : '';

                            if (is_array($value)) {
                                $where .= "AND (";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $where .= "({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '%{$before}{$value_item}{$after}%')";
                                    if (count($value) > $i) {
                                        $where .= " OR ";
                                    }
                                    $i++;
                                }
                                $where .= ")";
                            } else {
                                $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '%{$before}{$value}{$after}%')";
                            }
                            break;
                        case 'like_and':
                            $before = (!empty($item['before_str'])) ? sanitize_text_field($item['before_str']) : '';
                            $after = (!empty($item['after_str'])) ? sanitize_text_field($item['after_str']) : '';

                            if (is_array($value)) {
                                $where .= "AND (";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $where .= "({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '%{$before}{$value_item}{$after}%')";
                                    if (count($value) > $i) {
                                        $where .= " AND ";
                                    }
                                    $i++;
                                }
                                $where .= ")";
                            } else {
                                $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '%{$before}{$value}{$after}%')";
                            }
                            break;
                        case 'not_like':
                            $before = (!empty($item['before_str'])) ? sanitize_text_field($item['before_str']) : '';
                            $after = (!empty($item['after_str'])) ? sanitize_text_field($item['after_str']) : '';

                            if (is_array($value)) {
                                $where .= "AND (";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $where .= "({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value NOT LIKE '%{$before}{$value_item}{$after}%')";
                                    if (count($value) > $i) {
                                        $where .= " AND ";
                                    }
                                    $i++;
                                }
                                $where .= ")";
                            } else {
                                $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '%{$before}{$value}{$after}%')";
                            }
                            break;
                        case 'exact':
                            $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value = '{$value}')";
                            break;
                        case 'not':
                            $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value != '{$value}')";
                            break;
                        case 'begin':
                            $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '{$value}%')";
                            break;
                        case 'end':
                            $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value LIKE '%{$value}')";
                            break;
                        case 'in':
                            $where .= "AND ({$postmeta}.meta_key = '{$key}' AND {$postmeta}.meta_value IN ({$value}))";
                            break;
                        case 'between':
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value BETWEEN {$value[0]} AND {$value[1]})";
                            break;
                        case 'serialized_date_between':
                            if (!empty($item['item_key'])) {
                                $where .= "AND ({$postmeta}.meta_key = '$key' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING({$postmeta}.meta_value, (INSTR({$postmeta}.meta_value, '" . sanitize_text_field($item['item_key']) . "') + CHAR_LENGTH('" . sanitize_text_field($item['item_key']) . "') + 1 )), '\"', 2), '\"', -1) as DATE) BETWEEN '{$value[0]}' AND '{$value[1]}')";
                            }
                            break;
                        case 'json_between':
                            if (!empty($item['json_key'])) {
                                $where .= "AND ({$postmeta}.meta_key = '$key' AND CAST(JSON_EXTRACT({$postmeta}.meta_value, '$.{$item['json_key']}') as UNSIGNED) BETWEEN {$value[0]} AND {$value[1]})";
                            }
                            break;
                        case 'between_with_quotation':
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value BETWEEN '{$value[0]}' AND '{$value[1]}')";
                            break;
                        case '<=':
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value <= {$value})";
                            break;
                        case '>=':
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value >= {$value})";
                            break;
                        case 'json_<=':
                            if (!empty($item['json_key'])) {
                                $where .= "AND ({$postmeta}.meta_key = '$key' AND CAST(JSON_EXTRACT({$postmeta}.meta_value, '$.{$item['json_key']}') as UNSIGNED) <= {$value})";
                            }
                            break;
                        case 'json_>=':
                            if (!empty($item['json_key'])) {
                                $where .= "AND ({$postmeta}.meta_key = '$key' AND CAST(JSON_EXTRACT({$postmeta}.meta_value, '$.{$item['json_key']}') as UNSIGNED) >= {$value})";
                            }
                            break;
                        case 'serialized_date_<=':
                            if (!empty($item['item_key'])) {
                                $where .= "AND ({$postmeta}.meta_key = '$key' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING({$postmeta}.meta_value, (INSTR({$postmeta}.meta_value, '" . sanitize_text_field($item['item_key']) . "') + CHAR_LENGTH('" . sanitize_text_field($item['item_key']) . "') + 1 )), '\"', 2), '\"', -1) as DATE) <= '{$value}')";
                            }
                            break;
                        case 'serialized_date_>=':
                            if (!empty($item['item_key'])) {
                                $where .= "AND ({$postmeta}.meta_key = '$key' AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING({$postmeta}.meta_value, (INSTR({$postmeta}.meta_value, '" . sanitize_text_field($item['item_key']) . "') + CHAR_LENGTH('" . sanitize_text_field($item['item_key']) . "') + 1 )), '\"', 2), '\"', -1) as DATE) >= '{$value}')";
                            }
                            break;
                        case '<=_with_quotation':
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value <= '{$value}')";
                            break;
                        case '>=_with_quotation':
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value >= '{$value}')";
                            break;
                        default:
                            $where .= "AND ({$postmeta}.meta_key = '$key' AND {$postmeta}.meta_value = '{$value}')";
                            break;
                    }
                }
            }
        }
        return $where;
    }
}
